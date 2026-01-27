<?php

namespace NewSong\BibleVerseFinder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearBibleCacheCommand extends Command
{
    protected $signature = 'bible-verses:clear-cache';

    protected $description = 'Clear all cached Bible verses';

    public function handle()
    {
        $this->info('Clearing Bible verse cache...');

        // Since we use specific cache keys with prefix "bible_verse_",
        // we'll need to clear the entire cache or use tags if available
        // For simplicity, we'll just clear all cache

        $cacheDriver = config('bible-verse-finder.cache.driver', 'file');

        try {
            Cache::driver($cacheDriver)->flush();
            $this->info('✓ Cache cleared successfully');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Failed to clear cache: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
