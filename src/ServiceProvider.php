<?php

namespace NewSong\BibleVerseFinder;

use NewSong\BibleVerseFinder\Console\Commands\ClearBibleCacheCommand;
use NewSong\BibleVerseFinder\Console\Commands\DownloadBibleCommand;
use NewSong\BibleVerseFinder\Console\Commands\ListBibleVersionsCommand;
use NewSong\BibleVerseFinder\Console\Commands\TestBibleAPICommand;
use NewSong\BibleVerseFinder\Fieldtypes\BibleVerseFinder;
use NewSong\BibleVerseFinder\GraphQL\BibleVerseType;
use NewSong\BibleVerseFinder\Support\Logger;
use Statamic\Facades\GraphQL;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        BibleVerseFinder::class,
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    protected $commands = [
        TestBibleAPICommand::class,
        DownloadBibleCommand::class,
        ListBibleVersionsCommand::class,
        ClearBibleCacheCommand::class,
    ];

    protected $vite = [
        'input' => [
            'resources/js/addon.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon()
    {
        // Register GraphQL types
        GraphQL::addType(BibleVerseType::class);

        // Register permissions
        $this->registerPermissions();

        // Register logging channel
        $this->registerLoggingChannel();

        // Validate production configuration
        $this->validateProductionConfig();

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/bible-verse-finder.php' => config_path('bible-verse-finder.php'),
        ], 'bible-verse-finder-config');

        // Publish fieldsets
        $this->publishes([
            __DIR__.'/../resources/fieldsets' => resource_path('fieldsets/vendor/bible-verse-finder'),
        ], 'bible-verse-finder-fieldsets');

        // Merge config from published file
        $this->mergeConfigFrom(
            __DIR__.'/../config/bible-verse-finder.php',
            'bible-verse-finder'
        );
    }

    /**
     * Register addon permissions.
     */
    protected function registerPermissions(): void
    {
        Permission::group('bible-verse-finder', 'Bible Verse Finder', function () {
            Permission::register('access bible verses')
                ->label('Access Bible Verses');
        });
    }

    /**
     * Register the dedicated logging channel.
     */
    protected function registerLoggingChannel(): void
    {
        if (! config('bible-verse-finder.logging.enabled', true)) {
            return;
        }

        $this->app['config']->set('logging.channels.bible-verse-finder', [
            'driver' => 'daily',
            'path' => storage_path('logs/bible-verse-finder.log'),
            'level' => config('bible-verse-finder.logging.level', 'info'),
            'days' => 14,
        ]);
    }

    /**
     * Validate configuration for production environments.
     */
    protected function validateProductionConfig(): void
    {
        if (! $this->app->environment('production')) {
            return;
        }

        // Warn if Scripture API key is configured but empty
        $apiKey = config('bible-verse-finder.apis.scripture_api_bible.api_key');
        if (config('bible-verse-finder.apis.primary') === 'scripture_api_bible' && empty($apiKey)) {
            Logger::warning('Scripture API Bible key not configured', [
                'recommendation' => 'Set SCRIPTURE_API_KEY environment variable for Scripture API access.',
            ]);
        }
    }
}
