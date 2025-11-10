<?php

namespace NewSong\BibleVerseFinder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class DownloadBibleCommand extends Command
{
    protected $signature = 'bible-verses:download
                          {versions?* : Versions to download (e.g., kjv nkjv esv)}
                          {--all : Download all available versions}';

    protected $description = 'Download complete Bible versions as JSON for offline use';

    public function handle()
    {
        $config = config('bible-verse-finder');
        $storagePath = $config['storage']['path'];

        // Ensure storage directory exists
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
            $this->info("Created storage directory: {$storagePath}");
        }

        $downloadSources = $config['download_sources'];
        $versions = $this->argument('versions');
        $downloadAll = $this->option('all');

        // Determine which versions to download
        if ($downloadAll) {
            $versions = array_keys($downloadSources);
        } elseif (empty($versions)) {
            $this->error('Please specify versions to download or use --all flag');
            $this->line('Example: php artisan bible-verses:download kjv nkjv');
            return Command::FAILURE;
        }

        $this->info('Starting download of ' . count($versions) . ' version(s)...');
        $this->newLine();

        $successful = 0;
        $failed = 0;

        foreach ($versions as $version) {
            $version = strtoupper($version);

            if (!isset($downloadSources[$version])) {
                $this->warn("Skipping {$version}: No download source configured");
                $failed++;
                continue;
            }

            $url = $downloadSources[$version];

            if (empty($url)) {
                $this->warn("Skipping {$version}: Download URL not available");
                $failed++;
                continue;
            }

            $this->line("Downloading {$version}...");

            try {
                $response = Http::timeout(300)->get($url);

                if (!$response->successful()) {
                    $this->error("  ✗ Failed to download {$version} (HTTP {$response->status()})");
                    $failed++;
                    continue;
                }

                $jsonPath = "{$storagePath}/{$version}.json";
                File::put($jsonPath, $response->body());

                $sizeKB = round(File::size($jsonPath) / 1024, 2);
                $this->info("  ✓ Downloaded {$version} ({$sizeKB} KB)");
                $successful++;
            } catch (\Exception $e) {
                $this->error("  ✗ Error downloading {$version}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Download complete: {$successful} successful, {$failed} failed");

        return $failed === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
