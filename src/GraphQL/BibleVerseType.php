<?php

namespace NewSong\BibleVerseFinder\GraphQL;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BibleVerseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BibleVerse',
        'description' => 'A Bible verse or passage with reference and text'
    ];

    public function fields(): array
    {
        return [
            'reference' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The full reference (e.g., "John 3:16" or "Psalm 23:1-6")',
            ],
            'book' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The Bible book name (e.g., "John", "Psalm")',
            ],
            'chapter' => [
                'type' => Type::int(),
                'description' => 'The starting chapter number',
            ],
            'end_chapter' => [
                'type' => Type::int(),
                'description' => 'The ending chapter number (for chapter ranges)',
            ],
            'start_verse' => [
                'type' => Type::int(),
                'description' => 'The starting verse number',
            ],
            'end_verse' => [
                'type' => Type::int(),
                'description' => 'The ending verse number (for verse ranges)',
            ],
            'version' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The Bible version code (e.g., "NKJV", "NIV", "ESV")',
            ],
            'version_name' => [
                'type' => Type::string(),
                'description' => 'The full Bible version name (e.g., "New King James Version")',
            ],
            'text' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The verse text with verse numbers',
            ],
            'text_without_numbers' => [
                'type' => Type::string(),
                'description' => 'The verse text with verse numbers stripped',
            ],
            'is_range' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Whether this is a verse or chapter range',
            ],
            'is_chapter_range' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Whether this is a chapter range',
            ],
            'is_verse_range' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Whether this is a verse range',
            ],
            'fetched_at' => [
                'type' => Type::string(),
                'description' => 'When the verse text was fetched from the API',
            ],
            'api_source' => [
                'type' => Type::string(),
                'description' => 'The API source used to fetch the verse',
            ],
            'has_text' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Whether the verse has text content',
            ],
        ];
    }
}
