<?php

use NewSong\BibleVerseFinder\Data\BibleMetadata;

describe('findBook', function () {
    it('finds book by full name', function () {
        $book = BibleMetadata::findBook('Genesis');
        expect($book)->not->toBeNull();
        expect($book['name'])->toBe('Genesis');
        expect($book['number'])->toBe(1);
    });

    it('finds book by name case-insensitively', function () {
        $book = BibleMetadata::findBook('genesis');
        expect($book)->not->toBeNull();
        expect($book['name'])->toBe('Genesis');
    });

    it('finds book by abbreviation', function () {
        $book = BibleMetadata::findBook('Gen');
        expect($book)->not->toBeNull();
        expect($book['name'])->toBe('Genesis');
    });

    it('finds numbered books', function () {
        $book = BibleMetadata::findBook('1 Samuel');
        expect($book)->not->toBeNull();
        expect($book['name'])->toBe('1 Samuel');
        expect($book['number'])->toBe(9);
    });

    it('finds Psalms by various names', function () {
        expect(BibleMetadata::findBook('Psalms'))->not->toBeNull();
        expect(BibleMetadata::findBook('Psalm'))->not->toBeNull();
        expect(BibleMetadata::findBook('Ps'))->not->toBeNull();
    });

    it('returns null for non-existent book', function () {
        $book = BibleMetadata::findBook('NotABook');
        expect($book)->toBeNull();
    });
});

describe('validateChapter', function () {
    it('validates existing chapter', function () {
        expect(BibleMetadata::validateChapter('Genesis', 1))->toBeTrue();
        expect(BibleMetadata::validateChapter('Genesis', 50))->toBeTrue();
    });

    it('invalidates non-existent chapter', function () {
        expect(BibleMetadata::validateChapter('Genesis', 0))->toBeFalse();
        expect(BibleMetadata::validateChapter('Genesis', 51))->toBeFalse();
    });

    it('invalidates for non-existent book', function () {
        expect(BibleMetadata::validateChapter('NotABook', 1))->toBeFalse();
    });
});

describe('validateVerse', function () {
    it('validates existing verse', function () {
        // Genesis 1 has 31 verses
        expect(BibleMetadata::validateVerse('Genesis', 1, 1))->toBeTrue();
        expect(BibleMetadata::validateVerse('Genesis', 1, 31))->toBeTrue();
    });

    it('invalidates non-existent verse', function () {
        expect(BibleMetadata::validateVerse('Genesis', 1, 0))->toBeFalse();
        expect(BibleMetadata::validateVerse('Genesis', 1, 32))->toBeFalse();
    });

    it('validates John 3:16', function () {
        expect(BibleMetadata::validateVerse('John', 3, 16))->toBeTrue();
    });

    it('validates Psalm 23', function () {
        // Psalm 23 has 6 verses
        expect(BibleMetadata::validateVerse('Psalms', 23, 1))->toBeTrue();
        expect(BibleMetadata::validateVerse('Psalms', 23, 6))->toBeTrue();
        expect(BibleMetadata::validateVerse('Psalms', 23, 7))->toBeFalse();
    });
});

describe('getVerseCount', function () {
    it('returns correct verse count', function () {
        // Genesis 1 has 31 verses
        expect(BibleMetadata::getVerseCount('Genesis', 1))->toBe(31);
        // Psalm 23 has 6 verses
        expect(BibleMetadata::getVerseCount('Psalms', 23))->toBe(6);
        // Psalm 119 has 176 verses (longest chapter)
        expect(BibleMetadata::getVerseCount('Psalms', 119))->toBe(176);
    });

    it('returns null for invalid chapter', function () {
        expect(BibleMetadata::getVerseCount('Genesis', 100))->toBeNull();
    });

    it('returns null for invalid book', function () {
        expect(BibleMetadata::getVerseCount('NotABook', 1))->toBeNull();
    });
});

describe('getChapterCount', function () {
    it('returns correct chapter count', function () {
        expect(BibleMetadata::getChapterCount('Genesis'))->toBe(50);
        expect(BibleMetadata::getChapterCount('Psalms'))->toBe(150);
        expect(BibleMetadata::getChapterCount('Obadiah'))->toBe(1);
        expect(BibleMetadata::getChapterCount('Revelation'))->toBe(22);
    });

    it('returns null for invalid book', function () {
        expect(BibleMetadata::getChapterCount('NotABook'))->toBeNull();
    });
});

describe('getBookList', function () {
    it('returns all 66 books', function () {
        $list = BibleMetadata::getBookList();
        expect($list)->toHaveCount(66);
    });

    it('includes both testaments', function () {
        $list = BibleMetadata::getBookList();
        $otBooks = array_filter($list, fn ($b) => $b['testament'] === 'ot');
        $ntBooks = array_filter($list, fn ($b) => $b['testament'] === 'nt');

        expect(count($otBooks))->toBe(39);
        expect(count($ntBooks))->toBe(27);
    });
});
