<?php

namespace NewSong\BibleVerseFinder\Tests;

use NewSong\BibleVerseFinder\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up default config values for testing
        config([
            'bible-verse-finder.cache.enabled' => false,
            'bible-verse-finder.cache.ttl' => 3600,
            'bible-verse-finder.storage.use_local_json' => false,
            'bible-verse-finder.apis.primary' => 'bolls',
            'bible-verse-finder.apis.timeout' => 10,
            'bible-verse-finder.formatting.include_verse_numbers' => true,
            'bible-verse-finder.formatting.strip_html' => true,
            'bible-verse-finder.formatting.trim_whitespace' => true,
            'bible-verse-finder.validation.max_verses_per_range' => 50,
            'bible-verse-finder.parsing.default_version' => 'NKJV',
        ]);
    }
}
