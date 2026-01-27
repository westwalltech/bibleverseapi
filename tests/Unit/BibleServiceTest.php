<?php

use NewSong\BibleVerseFinder\Services\BibleService;

describe('parseReference', function () {
    beforeEach(function () {
        $this->service = new BibleService;
    });

    it('parses single verse reference', function () {
        $result = $this->service->parseReference('John 3:16');

        expect($result)->not->toBeNull();
        expect($result['book'])->toBe('John');
        expect($result['chapter'])->toBe(3);
        expect($result['start_verse'])->toBe(16);
        expect($result['end_verse'])->toBeNull();
    });

    it('parses verse range', function () {
        $result = $this->service->parseReference('John 3:16-17');

        expect($result)->not->toBeNull();
        expect($result['book'])->toBe('John');
        expect($result['chapter'])->toBe(3);
        expect($result['start_verse'])->toBe(16);
        expect($result['end_verse'])->toBe(17);
    });

    it('parses whole chapter', function () {
        $result = $this->service->parseReference('Psalm 23');

        expect($result)->not->toBeNull();
        expect($result['book'])->toBe('Psalm');
        expect($result['chapter'])->toBe(23);
        expect($result['start_verse'])->toBeNull();
        expect($result['end_verse'])->toBeNull();
    });

    it('parses chapter range', function () {
        $result = $this->service->parseReference('Psalm 46-47');

        expect($result)->not->toBeNull();
        expect($result['book'])->toBe('Psalm');
        expect($result['chapter'])->toBe(46);
        expect($result['end_chapter'])->toBe(47);
    });

    it('parses reference with version', function () {
        $result = $this->service->parseReference('John 3:16 NKJV');

        expect($result)->not->toBeNull();
        expect($result['book'])->toBe('John');
        expect($result['version'])->toBe('NKJV');
    });

    it('parses verse range with version', function () {
        $result = $this->service->parseReference('Romans 8:28-30 ESV');

        expect($result)->not->toBeNull();
        expect($result['book'])->toBe('Romans');
        expect($result['chapter'])->toBe(8);
        expect($result['start_verse'])->toBe(28);
        expect($result['end_verse'])->toBe(30);
        expect($result['version'])->toBe('ESV');
    });

    it('parses numbered book references', function () {
        $result = $this->service->parseReference('1 Corinthians 13:4-7');

        expect($result)->not->toBeNull();
        expect($result['book'])->toBe('1 Corinthians');
        expect($result['chapter'])->toBe(13);
        expect($result['start_verse'])->toBe(4);
        expect($result['end_verse'])->toBe(7);
    });

    it('uses default version when not specified', function () {
        $result = $this->service->parseReference('John 3:16');

        expect($result)->not->toBeNull();
        expect($result['version'])->toBe('NKJV'); // Default from config
    });

    it('returns null for invalid reference', function () {
        $result = $this->service->parseReference('Not a valid reference');
        expect($result)->toBeNull();
    });

    it('returns null for non-existent book', function () {
        $result = $this->service->parseReference('FakeBook 1:1');
        expect($result)->toBeNull();
    });
});
