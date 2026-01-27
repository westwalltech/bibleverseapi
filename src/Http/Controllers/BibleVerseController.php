<?php

namespace NewSong\BibleVerseFinder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NewSong\BibleVerseFinder\Data\BibleMetadata;
use NewSong\BibleVerseFinder\Services\BibleService;

class BibleVerseController extends Controller
{
    protected BibleService $bibleService;

    public function __construct(BibleService $bibleService)
    {
        $this->bibleService = $bibleService;
    }

    /**
     * Fetch a single verse, verse range, whole chapter, or chapter range
     *
     * POST /cp/bible-verses/fetch
     */
    public function fetch(Request $request)
    {
        $validated = $request->validate([
            'book' => 'required|string',
            'chapter' => 'required|integer|min:1',
            'end_chapter' => 'nullable|integer|min:1',
            'start_verse' => 'nullable|integer|min:1',
            'end_verse' => 'nullable|integer|min:1',
            'version' => 'required|string',
        ]);

        $result = $this->bibleService->fetchVerse(
            $validated['book'],
            $validated['chapter'],
            $validated['start_verse'] ?? null,
            $validated['end_verse'] ?? null,
            $validated['version'],
            $validated['end_chapter'] ?? null
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'verse' => $result['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'],
        ], 422);
    }

    /**
     * Fetch multiple verses at once
     *
     * POST /cp/bible-verses/fetch-multiple
     */
    public function fetchMultiple(Request $request)
    {
        $validated = $request->validate([
            'verses' => 'required|array',
            'verses.*.book' => 'required|string',
            'verses.*.chapter' => 'required|integer|min:1',
            'verses.*.start_verse' => 'required|integer|min:1',
            'verses.*.end_verse' => 'nullable|integer|min:1',
            'verses.*.version' => 'required|string',
        ]);

        $results = [];
        $errors = [];

        foreach ($validated['verses'] as $index => $verse) {
            $result = $this->bibleService->fetchVerse(
                $verse['book'],
                $verse['chapter'],
                $verse['start_verse'],
                $verse['end_verse'] ?? null,
                $verse['version']
            );

            if ($result['success']) {
                $results[] = $result['data'];
            } else {
                $errors[] = [
                    'index' => $index,
                    'error' => $result['error'],
                ];
            }
        }

        return response()->json([
            'success' => empty($errors),
            'verses' => $results,
            'errors' => $errors,
        ]);
    }

    /**
     * Parse a Bible reference string
     *
     * POST /cp/bible-verses/parse
     */
    public function parse(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string',
        ]);

        $parsed = $this->bibleService->parseReference($validated['reference']);

        if ($parsed) {
            return response()->json([
                'success' => true,
                'parsed' => $parsed,
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Could not parse reference. Expected format: "Book Chapter:Verse" or "Book Chapter:Verse-Verse VERSION"',
        ], 422);
    }

    /**
     * Validate a verse reference
     *
     * POST /cp/bible-verses/validate
     */
    public function validate(Request $request)
    {
        $validated = $request->validate([
            'book' => 'required|string',
            'chapter' => 'required|integer|min:1',
            'verse' => 'required|integer|min:1',
        ]);

        $book = $validated['book'];
        $chapter = $validated['chapter'];
        $verse = $validated['verse'];

        $bookData = BibleMetadata::findBook($book);

        if (! $bookData) {
            return response()->json([
                'valid' => false,
                'error' => "Book '{$book}' not found",
            ]);
        }

        if (! BibleMetadata::validateChapter($book, $chapter)) {
            $maxChapters = BibleMetadata::getChapterCount($book);

            return response()->json([
                'valid' => false,
                'error' => "Chapter {$chapter} does not exist in {$book} (max: {$maxChapters})",
            ]);
        }

        if (! BibleMetadata::validateVerse($book, $chapter, $verse)) {
            $maxVerses = BibleMetadata::getVerseCount($book, $chapter);

            return response()->json([
                'valid' => false,
                'error' => "Verse {$verse} does not exist in {$book} {$chapter} (max: {$maxVerses})",
            ]);
        }

        return response()->json([
            'valid' => true,
            'max_verses' => BibleMetadata::getVerseCount($book, $chapter),
        ]);
    }

    /**
     * Get chapter count for a book
     *
     * GET /cp/bible-verses/chapters/{book}
     */
    public function getChapters(string $book)
    {
        $chapterCount = BibleMetadata::getChapterCount($book);

        if ($chapterCount === null) {
            return response()->json([
                'success' => false,
                'error' => "Book '{$book}' not found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'count' => $chapterCount,
            'chapters' => range(1, $chapterCount),
        ]);
    }

    /**
     * Get verse count for a chapter
     *
     * GET /cp/bible-verses/verses/{book}/{chapter}
     */
    public function getVerses(string $book, int $chapter)
    {
        $verseCount = BibleMetadata::getVerseCount($book, $chapter);

        if ($verseCount === null) {
            return response()->json([
                'success' => false,
                'error' => "Chapter {$chapter} not found in {$book}",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'count' => $verseCount,
            'verses' => range(1, $verseCount),
        ]);
    }

    /**
     * Get available Bible versions
     *
     * GET /cp/bible-verses/versions
     */
    public function getVersions()
    {
        $versions = config('bible-verse-finder.versions', []);
        $formatted = [];

        foreach ($versions as $code => $name) {
            $formatted[] = [
                'value' => $code,
                'label' => "{$code} - {$name}",
            ];
        }

        return response()->json([
            'success' => true,
            'versions' => $formatted,
        ]);
    }
}
