<?php

namespace NewSong\BibleVerseFinder\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Statamic\Facades\GraphQL;
use NewSong\BibleVerseFinder\Data\BibleMetadata;

class BibleVerseFinder extends Fieldtype
{
    protected $icon = 'book';

    protected $categories = ['text', 'media'];

    /**
     * The blank/default value
     *
     * @return array
     */
    public function defaultValue()
    {
        return [];
    }

    /**
     * Pre-process the data before it gets sent to the publish page
     *
     * @param mixed $data
     * @return array
     */
    public function preProcess($data)
    {
        if (!is_array($data)) {
            return [];
        }

        // Ensure each verse has the required structure
        return array_map(function ($verse) {
            return [
                'reference' => $verse['reference'] ?? '',
                'book' => $verse['book'] ?? '',
                'chapter' => $verse['chapter'] ?? null,
                'end_chapter' => $verse['end_chapter'] ?? null,
                'start_verse' => $verse['start_verse'] ?? null,
                'end_verse' => $verse['end_verse'] ?? null,
                'version' => $verse['version'] ?? 'NKJV',
                'text' => $verse['text'] ?? '',
                'fetched_at' => $verse['fetched_at'] ?? null,
                'api_source' => $verse['api_source'] ?? null,
            ];
        }, $data);
    }

    /**
     * Process the data before it gets saved
     *
     * @param mixed $data
     * @return array
     */
    public function process($data)
    {
        if (!is_array($data)) {
            return [];
        }

        // Filter out empty entries
        return array_values(array_filter($data, function ($verse) {
            return !empty($verse['text']) && !empty($verse['book']);
        }));
    }

    /**
     * Augment the value for use in templates
     *
     * @param mixed $value
     * @return array
     */
    public function augment($value)
    {
        if (!is_array($value)) {
            return [];
        }

        return array_map(function ($verse) {
            $endChapter = $verse['end_chapter'] ?? null;
            $startVerse = $verse['start_verse'] ?? null;
            $endVerse = $verse['end_verse'] ?? null;

            // Determine if this is a range (chapter range or verse range)
            $isRange = ($endChapter !== null && $endChapter !== $verse['chapter'])
                    || ($startVerse !== null && $endVerse !== null && $startVerse !== $endVerse);

            return [
                'reference' => $verse['reference'] ?? '',
                'book' => $verse['book'] ?? '',
                'chapter' => $verse['chapter'] ?? null,
                'end_chapter' => $endChapter,
                'start_verse' => $startVerse,
                'end_verse' => $endVerse,
                'version' => $verse['version'] ?? '',
                'version_name' => $this->getVersionName($verse['version'] ?? ''),
                'text' => $verse['text'] ?? '',
                'text_without_numbers' => $this->stripVerseNumbers($verse['text'] ?? ''),
                'is_range' => $isRange,
                'is_chapter_range' => $endChapter !== null && $endChapter !== $verse['chapter'],
                'is_verse_range' => $startVerse !== null && $endVerse !== null && $startVerse !== $endVerse,
                'fetched_at' => $verse['fetched_at'] ?? null,
                'api_source' => $verse['api_source'] ?? null,
                'has_text' => !empty($verse['text']),
            ];
        }, $value);
    }

    /**
     * Define the fieldtype config blueprint
     *
     * @return array
     */
    public function configFieldItems(): array
    {
        return [
            'default_version' => [
                'type' => 'select',
                'display' => 'Default Version',
                'instructions' => 'The default Bible version to use when adding new verses',
                'options' => $this->getVersionOptions(),
                'default' => 'NKJV',
                'width' => 50,
            ],
            'allowed_versions' => [
                'type' => 'select',
                'display' => 'Allowed Versions',
                'instructions' => 'Which Bible versions are available in the dropdown (leave empty for all)',
                'options' => $this->getVersionOptions(),
                'multiple' => true,
                'width' => 50,
            ],
            'enable_reference_parsing' => [
                'type' => 'toggle',
                'display' => 'Enable Reference Parsing',
                'instructions' => 'Allow users to paste references like "John 3:16-17 NKJV" to auto-populate fields',
                'default' => true,
                'width' => 50,
            ],
            'max_verses' => [
                'type' => 'integer',
                'display' => 'Maximum Verses',
                'instructions' => 'Maximum number of verses that can be added (0 for unlimited)',
                'default' => 0,
                'width' => 50,
            ],
        ];
    }

    /**
     * Get version options for config
     */
    protected function getVersionOptions(): array
    {
        $versions = config('bible-verse-finder.versions', []);
        $options = [];

        foreach ($versions as $code => $name) {
            $options[$code] = "{$code} - {$name}";
        }

        return $options;
    }

    /**
     * Get full version name
     */
    protected function getVersionName(string $version): string
    {
        $versions = config('bible-verse-finder.versions', []);
        return $versions[$version] ?? $version;
    }

    /**
     * Strip verse numbers from text
     */
    protected function stripVerseNumbers(string $text): string
    {
        // Remove leading verse numbers like "16 " or "16-17 "
        return preg_replace('/^\d+(-\d+)?\s+/', '', $text);
    }

    /**
     * Provide data to the Vue component
     */
    public function preload()
    {
        return [
            'books' => BibleMetadata::getBookList(),
            'versions' => $this->getAllowedVersions(),
            'defaultVersion' => $this->config('default_version', 'NKJV'),
            'enableReferenceParsing' => $this->config('enable_reference_parsing', true),
            'maxVerses' => $this->config('max_verses', 0),
        ];
    }

    /**
     * Get allowed versions based on config
     */
    protected function getAllowedVersions(): array
    {
        $allowed = $this->config('allowed_versions');

        // If no specific versions configured, use all
        if (empty($allowed)) {
            $allowed = array_keys(config('bible-verse-finder.versions', []));
        }

        $versions = config('bible-verse-finder.versions', []);
        $result = [];

        foreach ($allowed as $code) {
            if (isset($versions[$code])) {
                $result[] = [
                    'value' => $code,
                    'label' => "{$code} - {$versions[$code]}",
                ];
            }
        }

        return $result;
    }

    /**
     * Define the GraphQL type for this fieldtype
     *
     * @return array
     */
    public function toGqlType()
    {
        return [
            'type' => GraphQL::listOf(GraphQL::type('BibleVerse')),
            'description' => 'List of Bible verses with references and text',
        ];
    }
}
