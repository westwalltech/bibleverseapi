<?php

namespace NewSong\BibleVerseFinder;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\GraphQL;
use NewSong\BibleVerseFinder\Fieldtypes\BibleVerseFinder;
use NewSong\BibleVerseFinder\Console\Commands\TestBibleAPICommand;
use NewSong\BibleVerseFinder\Console\Commands\DownloadBibleCommand;
use NewSong\BibleVerseFinder\Console\Commands\ListBibleVersionsCommand;
use NewSong\BibleVerseFinder\Console\Commands\ClearBibleCacheCommand;
use NewSong\BibleVerseFinder\GraphQL\BibleVerseType;

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
}
