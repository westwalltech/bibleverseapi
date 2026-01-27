<?php

namespace NewSong\BibleVerseFinder\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NewSong\BibleVerseFinder\Data\BibleMetadata;

class BibleService
{
    protected array $config;
    protected ?array $localBibleData = null;

    public function __construct()
    {
        $this->config = config('bible-verse-finder');
    }

    /**
     * Fetch a single verse, verse range, whole chapter, or chapter range
     *
     * @param  string  $book  Book name (e.g., "John", "1 Samuel", "Psalm")
     * @param  int  $chapter  Starting chapter number
     * @param  int|null  $startVerse  Start verse number (null for whole chapter)
     * @param  int|null  $endVerse  End verse number (null for single verse)
     * @param  string  $version  Bible version (e.g., "NKJV", "KJV")
     * @param  int|null  $endChapter  End chapter number for chapter ranges (e.g., Psalm 46-47)
     * @return array{success: bool, data: array|null, error: string|null}
     */
    public function fetchVerse(
        string $book,
        int $chapter,
        ?int $startVerse,
        ?int $endVerse,
        string $version,
        ?int $endChapter = null
    ): array {
        // Handle chapter ranges specially (e.g., Psalm 46-47)
        if ($endChapter !== null && $endChapter > $chapter) {
            return $this->fetchChapterRange($book, $chapter, $endChapter, $version);
        }

        // For whole chapter requests (no specific verses), fetch the entire chapter
        if ($startVerse === null) {
            return $this->fetchWholeChapter($book, $chapter, $version);
        }

        // Validate inputs for verse-level requests
        $validation = $this->validate($book, $chapter, $startVerse, $endVerse);
        if (! $validation['valid']) {
            return [
                'success' => false,
                'data' => null,
                'error' => $validation['error'],
            ];
        }

        $endVerse = $endVerse ?? $startVerse;

        // Generate reference string
        $reference = $this->formatReference($book, $chapter, $startVerse, $endVerse);

        // Check cache first
        if ($this->config['cache']['enabled']) {
            $cached = $this->getCachedVerse($book, $chapter, $startVerse, $endVerse, $version);
            if ($cached) {
                Log::info("Bible verse cache hit: {$reference} ({$version})");

                return [
                    'success' => true,
                    'data' => $cached,
                    'error' => null,
                ];
            }
        }

        // Try local JSON first
        if ($this->config['storage']['use_local_json']) {
            $localData = $this->fetchFromLocal($book, $chapter, $startVerse, $endVerse, $version);
            if ($localData) {
                $this->cacheVerse($book, $chapter, $startVerse, $endVerse, $version, $localData);

                return [
                    'success' => true,
                    'data' => $localData,
                    'error' => null,
                ];
            }
        }

        // Fetch from API
        $result = $this->fetchFromAPI($book, $chapter, $startVerse, $endVerse, $version);

        if ($result['success']) {
            $this->cacheVerse($book, $chapter, $startVerse, $endVerse, $version, $result['data']);
        }

        return $result;
    }

    /**
     * Fetch a range of chapters (e.g., Psalm 46-47)
     */
    protected function fetchChapterRange(
        string $book,
        int $startChapter,
        int $endChapter,
        string $version
    ): array {
        $chapters = [];
        $combinedText = '';

        for ($chapterNum = $startChapter; $chapterNum <= $endChapter; $chapterNum++) {
            $result = $this->fetchWholeChapter($book, $chapterNum, $version);

            if (! $result['success']) {
                return $result; // Return error if any chapter fails
            }

            $chapters[] = $result['data'];
            $combinedText .= ($combinedText ? "\n\n" : '').$result['data']['text'];
        }

        $reference = "{$book} {$startChapter}-{$endChapter}";

        return [
            'success' => true,
            'data' => [
                'book' => $book,
                'chapter' => $startChapter,
                'end_chapter' => $endChapter,
                'start_verse' => null,
                'end_verse' => null,
                'version' => $version,
                'text' => $combinedText,
                'reference' => $reference,
                'fetched_at' => now()->toISOString(),
                'api_source' => 'combined',
            ],
            'error' => null,
        ];
    }

    /**
     * Fetch an entire chapter without specifying verses
     */
    protected function fetchWholeChapter(
        string $book,
        int $chapter,
        string $version
    ): array {
        // Get the number of verses in this chapter
        $verseCount = BibleMetadata::getVerseCount($book, $chapter);

        if ($verseCount === null) {
            return [
                'success' => false,
                'data' => null,
                'error' => "Chapter {$chapter} does not exist in {$book}",
            ];
        }

        // Fetch from verse 1 to the last verse
        return $this->fetchVerse($book, $chapter, 1, $verseCount, $version, null);
    }

    /**
     * Fetch verse from local JSON file
     */
    protected function fetchFromLocal(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version
    ): ?array {
        $jsonPath = $this->getLocalJsonPath($version);

        if (! File::exists($jsonPath)) {
            return null;
        }

        try {
            if ($this->localBibleData === null || $this->localBibleData['version'] !== $version) {
                $content = File::get($jsonPath);
                $this->localBibleData = [
                    'version' => $version,
                    'data' => json_decode($content, true),
                ];
            }

            $bibleData = $this->localBibleData['data'];
            $bookData = $this->findBookInLocalData($bibleData, $book);

            if (! $bookData) {
                return null;
            }

            // Extract verses
            $verses = [];
            for ($v = $startVerse; $v <= $endVerse; $v++) {
                $verseData = $this->findVerseInChapter($bookData, $chapter, $v);
                if ($verseData) {
                    $verses[] = $verseData;
                }
            }

            if (empty($verses)) {
                return null;
            }

            return $this->formatVerseData($book, $chapter, $startVerse, $endVerse, $version, $verses);
        } catch (\Exception $e) {
            Log::warning("Failed to read local Bible JSON: {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Fetch verse from external API
     */
    protected function fetchFromAPI(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version
    ): array {
        $bookMeta = BibleMetadata::findBook($book);
        $bookNumber = $bookMeta['number'];

        // Try primary API
        $primaryAPI = $this->config['apis']['primary'];

        switch ($primaryAPI) {
            case 'bolls':
                $result = $this->fetchFromBolls($bookNumber, $chapter, $startVerse, $endVerse, $version);

                break;
            case 'bible-api':
                $result = $this->fetchFromBibleAPI($book, $chapter, $startVerse, $endVerse, $version);

                break;
            case 'scripture-api':
                $result = $this->fetchFromScriptureAPI($book, $chapter, $startVerse, $endVerse, $version);

                break;
            default:
                $result = ['success' => false, 'data' => null, 'error' => 'Invalid primary API configured'];
        }

        // If primary fails, try fallbacks
        if (! $result['success']) {
            Log::warning("Primary API failed, trying fallbacks: {$result['error']}");

            // Try Bolls if it wasn't the primary
            if ($primaryAPI !== 'bolls' && $this->config['apis']['bolls']['enabled']) {
                $result = $this->fetchFromBolls($bookNumber, $chapter, $startVerse, $endVerse, $version);
                if ($result['success']) {
                    return $result;
                }
            }

            // Try Bible API if it wasn't the primary
            if ($primaryAPI !== 'bible-api' && $this->config['apis']['bible_api']['enabled']) {
                $result = $this->fetchFromBibleAPI($book, $chapter, $startVerse, $endVerse, $version);
                if ($result['success']) {
                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Fetch from Bolls.life API
     */
    protected function fetchFromBolls(
        int $bookNumber,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version
    ): array {
        try {
            $baseUrl = $this->config['apis']['bolls']['base_url'];
            $timeout = $this->config['apis']['timeout'];

            // Map version to Bolls API version code
            $versionMap = $this->config['apis']['bolls']['versions'];
            $apiVersion = $versionMap[$version] ?? $version;

            // Fetch individual verses
            $verses = [];
            for ($v = $startVerse; $v <= $endVerse; $v++) {
                $url = "{$baseUrl}/get-verse/{$apiVersion}/{$bookNumber}/{$chapter}/{$v}/";
                $response = Http::timeout($timeout)->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['text'])) {
                        $verses[] = [
                            'verse' => $v,
                            'text' => $this->cleanText($data['text']),
                        ];
                    }
                } else {
                    Log::warning("Bolls API failed for verse {$v}: ".$response->body());
                }
            }

            if (empty($verses)) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'No verses found',
                ];
            }

            // Get book name from metadata
            $books = array_values(BibleMetadata::books());
            $bookName = $books[$bookNumber - 1]['name'];

            return [
                'success' => true,
                'data' => $this->formatVerseData($bookName, $chapter, $startVerse, $endVerse, $version, $verses),
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error("Bolls API error: {$e->getMessage()}");

            return [
                'success' => false,
                'data' => null,
                'error' => "API error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Fetch from Bible-API.com
     */
    protected function fetchFromBibleAPI(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version
    ): array {
        try {
            $baseUrl = $this->config['apis']['bible_api']['base_url'];
            $timeout = $this->config['apis']['timeout'];

            // Map version
            $versionMap = $this->config['apis']['bible_api']['versions'];
            $apiVersion = $versionMap[$version] ?? strtolower($version);

            // Build reference
            $reference = $endVerse > $startVerse
                ? "{$book} {$chapter}:{$startVerse}-{$endVerse}"
                : "{$book} {$chapter}:{$startVerse}";

            $url = "{$baseUrl}/{$reference}?translation={$apiVersion}";
            $response = Http::timeout($timeout)->get($url);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => "API returned status {$response->status()}",
                ];
            }

            $data = $response->json();

            if (! isset($data['verses'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Invalid API response format',
                ];
            }

            $verses = [];
            foreach ($data['verses'] as $verseData) {
                $verses[] = [
                    'verse' => $verseData['verse'],
                    'text' => $this->cleanText($verseData['text']),
                ];
            }

            return [
                'success' => true,
                'data' => $this->formatVerseData($book, $chapter, $startVerse, $endVerse, $version, $verses),
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error("Bible-API error: {$e->getMessage()}");

            return [
                'success' => false,
                'data' => null,
                'error' => "API error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Fetch from Scripture API (API.Bible)
     */
    protected function fetchFromScriptureAPI(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version
    ): array {
        if (! $this->config['apis']['scripture_api']['enabled']) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'Scripture API not configured (missing API key)',
            ];
        }

        // Implementation for API.Bible would go here
        // Requires API key and more complex setup
        return [
            'success' => false,
            'data' => null,
            'error' => 'Scripture API not yet implemented',
        ];
    }

    /**
     * Validate verse reference
     */
    protected function validate(
        string $book,
        int $chapter,
        int $startVerse,
        ?int $endVerse
    ): array {
        $bookData = BibleMetadata::findBook($book);

        if (! $bookData) {
            return ['valid' => false, 'error' => "Book '{$book}' not found"];
        }

        if (! BibleMetadata::validateChapter($book, $chapter)) {
            return ['valid' => false, 'error' => "Chapter {$chapter} does not exist in {$book}"];
        }

        if (! BibleMetadata::validateVerse($book, $chapter, $startVerse)) {
            return ['valid' => false, 'error' => "Verse {$startVerse} does not exist in {$book} {$chapter}"];
        }

        if ($endVerse !== null) {
            if ($endVerse < $startVerse) {
                return ['valid' => false, 'error' => 'End verse must be greater than or equal to start verse'];
            }

            if (! BibleMetadata::validateVerse($book, $chapter, $endVerse)) {
                return ['valid' => false, 'error' => "Verse {$endVerse} does not exist in {$book} {$chapter}"];
            }

            $verseCount = $endVerse - $startVerse + 1;
            $maxVerses = $this->config['validation']['max_verses_per_range'];
            if ($verseCount > $maxVerses) {
                return ['valid' => false, 'error' => "Verse range too large (max {$maxVerses} verses)"];
            }
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Format verse data for storage
     */
    protected function formatVerseData(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version,
        array $verses
    ): array {
        $includeVerseNumbers = $this->config['formatting']['include_verse_numbers'];

        // Combine verse texts
        $textParts = [];
        foreach ($verses as $verse) {
            if ($includeVerseNumbers) {
                $textParts[] = "{$verse['verse']} {$verse['text']}";
            } else {
                $textParts[] = $verse['text'];
            }
        }

        $text = implode(' ', $textParts);

        return [
            'reference' => $this->formatReference($book, $chapter, $startVerse, $endVerse),
            'book' => $book,
            'chapter' => $chapter,
            'start_verse' => $startVerse,
            'end_verse' => $endVerse,
            'version' => $version,
            'text' => $text,
            'fetched_at' => now()->toIso8601String(),
            'api_source' => $this->config['apis']['primary'],
        ];
    }

    /**
     * Format reference string
     */
    protected function formatReference(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse
    ): string {
        if ($startVerse === $endVerse) {
            return "{$book} {$chapter}:{$startVerse}";
        }

        return "{$book} {$chapter}:{$startVerse}-{$endVerse}";
    }

    /**
     * Clean verse text
     */
    protected function cleanText(string $text): string
    {
        if ($this->config['formatting']['strip_html']) {
            $text = strip_tags($text);
        }

        if ($this->config['formatting']['trim_whitespace']) {
            $text = trim(preg_replace('/\s+/', ' ', $text));
        }

        return $text;
    }

    /**
     * Get cached verse
     */
    protected function getCachedVerse(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version
    ): ?array {
        $key = $this->getCacheKey($book, $chapter, $startVerse, $endVerse, $version);

        return Cache::get($key);
    }

    /**
     * Cache verse data
     */
    protected function cacheVerse(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version,
        array $data
    ): void {
        $key = $this->getCacheKey($book, $chapter, $startVerse, $endVerse, $version);
        $ttl = $this->config['cache']['ttl'];
        Cache::put($key, $data, $ttl);
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(
        string $book,
        int $chapter,
        int $startVerse,
        int $endVerse,
        string $version
    ): string {
        $bookSlug = str_replace(' ', '_', strtolower($book));

        return "bible_verse_{$version}_{$bookSlug}_{$chapter}_{$startVerse}_{$endVerse}";
    }

    /**
     * Get local JSON file path
     */
    protected function getLocalJsonPath(string $version): string
    {
        $path = $this->config['storage']['path'];

        return "{$path}/{$version}.json";
    }

    /**
     * Find book in local data (handles various JSON structures)
     */
    protected function findBookInLocalData(array $data, string $bookName): ?array
    {
        // This will depend on the JSON structure
        // Implementation would vary based on the source
        return null;
    }

    /**
     * Find verse in chapter data
     */
    protected function findVerseInChapter(array $bookData, int $chapter, int $verse): ?array
    {
        // This will depend on the JSON structure
        // Implementation would vary based on the source
        return null;
    }

    /**
     * Parse a Bible reference string
     * Supports formats:
     * - "Psalm 46" (whole chapter)
     * - "Psalm 46 NKJV" (whole chapter with version)
     * - "Psalm 46-47" (chapter range)
     * - "John 3:16" (single verse)
     * - "John 3:16-17" (verse range)
     * - "John 3:16-17 NKJV" (with version)
     */
    public function parseReference(string $reference): ?array
    {
        $reference = trim($reference);

        // Pattern 1: Verse range - "Book Chapter:Verse-Verse Version" (check first because it's most specific)
        $verseRangePattern = '/^([\d\s\w]+?)\s+(\d+):(\d+)(?:-(\d+))?\s*([A-Z]+)?$/i';

        if (preg_match($verseRangePattern, $reference, $matches)) {
            $book = trim($matches[1]);
            $chapter = (int) $matches[2];
            $startVerse = (int) $matches[3];
            $endVerse = isset($matches[4]) && $matches[4] !== '' ? (int) $matches[4] : null;
            $version = isset($matches[5]) && $matches[5] !== ''
                ? strtoupper($matches[5])
                : $this->config['parsing']['default_version'];

            // Validate book exists
            if (! BibleMetadata::findBook($book)) {
                return null;
            }

            return [
                'book' => $book,
                'chapter' => $chapter,
                'end_chapter' => null,
                'start_verse' => $startVerse,
                'end_verse' => $endVerse,
                'version' => $version,
            ];
        }

        // Pattern 2: Chapter range - "Psalm 46-47" or "Psalm 46-47 NKJV"
        $chapterRangePattern = '/^([\d\s\w]+?)\s+(\d+)-(\d+)\s*([A-Z]+)?$/i';

        if (preg_match($chapterRangePattern, $reference, $matches)) {
            $book = trim($matches[1]);
            $chapter = (int) $matches[2];
            $endChapter = (int) $matches[3];
            $version = isset($matches[4]) && $matches[4] !== ''
                ? strtoupper($matches[4])
                : $this->config['parsing']['default_version'];

            // Validate book exists
            if (! BibleMetadata::findBook($book)) {
                return null;
            }

            return [
                'book' => $book,
                'chapter' => $chapter,
                'end_chapter' => $endChapter,
                'start_verse' => null,
                'end_verse' => null,
                'version' => $version,
            ];
        }

        // Pattern 3: Single chapter - "Psalm 46" or "Psalm 46 NKJV"
        $singleChapterPattern = '/^([\d\s\w]+?)\s+(\d+)\s*([A-Z]+)?$/i';

        if (preg_match($singleChapterPattern, $reference, $matches)) {
            $book = trim($matches[1]);
            $chapter = (int) $matches[2];
            $version = isset($matches[3]) && $matches[3] !== ''
                ? strtoupper($matches[3])
                : $this->config['parsing']['default_version'];

            // Validate book exists
            if (! BibleMetadata::findBook($book)) {
                return null;
            }

            return [
                'book' => $book,
                'chapter' => $chapter,
                'end_chapter' => null,
                'start_verse' => null,
                'end_verse' => null,
                'version' => $version,
            ];
        }

        return null;
    }
}
