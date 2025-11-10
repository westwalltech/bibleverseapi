<?php

namespace NewSong\BibleVerseFinder\Console\Commands;

use Illuminate\Console\Command;
use NewSong\BibleVerseFinder\Services\BibleService;

class TestBibleAPICommand extends Command
{
    protected $signature = 'bible-verses:test
                          {--book=John : Book name to test}
                          {--chapter=3 : Chapter number}
                          {--verse=16 : Verse number}
                          {--translation=NKJV : Bible translation/version}';

    protected $description = 'Test Bible API connectivity and fetch a sample verse';

    protected BibleService $bibleService;

    public function __construct(BibleService $bibleService)
    {
        parent::__construct();
        $this->bibleService = $bibleService;
    }

    public function handle()
    {
        $book = $this->option('book');
        $chapter = (int) $this->option('chapter');
        $verse = (int) $this->option('verse');
        $version = $this->option('translation');

        $this->info("Testing Bible API with {$book} {$chapter}:{$verse} ({$version})...\n");

        $result = $this->bibleService->fetchVerse($book, $chapter, $verse, null, $version);

        if ($result['success']) {
            $this->info('✓ API Connection Successful!');
            $this->newLine();

            $data = $result['data'];
            $this->line('Reference: ' . $data['reference']);
            $this->line('Version: ' . $data['version']);
            $this->line('API Source: ' . $data['api_source']);
            $this->newLine();

            $this->line('Text:');
            $this->comment($data['text']);
            $this->newLine();

            $this->info('Fetched at: ' . $data['fetched_at']);

            return Command::SUCCESS;
        }

        $this->error('✗ API Request Failed');
        $this->error('Error: ' . $result['error']);

        return Command::FAILURE;
    }
}
