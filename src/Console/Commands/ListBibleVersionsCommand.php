<?php

namespace NewSong\BibleVerseFinder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListBibleVersionsCommand extends Command
{
    protected $signature = 'bible-verses:list';

    protected $description = 'List downloaded Bible versions and their status';

    public function handle()
    {
        $config = config('bible-verse-finder');
        $storagePath = $config['storage']['path'];
        $versions = $config['versions'];
        $downloadSources = $config['download_sources'];

        $this->info('Bible Versions Status');
        $this->newLine();

        $headers = ['Version', 'Name', 'Downloaded', 'Size', 'Download Available'];
        $rows = [];

        foreach ($versions as $code => $name) {
            $jsonPath = "{$storagePath}/{$code}.json";
            $isDownloaded = File::exists($jsonPath);
            $size = $isDownloaded ? $this->formatSize(File::size($jsonPath)) : '-';
            $downloadAvailable = !empty($downloadSources[$code]) ? 'Yes' : 'No';

            $rows[] = [
                $code,
                $name,
                $isDownloaded ? '✓' : '✗',
                $size,
                $downloadAvailable,
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->line('To download versions:');
        $this->comment('  php artisan bible-verses:download kjv nkjv');
        $this->comment('  php artisan bible-verses:download --all');

        return Command::SUCCESS;
    }

    protected function formatSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / 1048576, 2) . ' MB';
        }
    }
}
