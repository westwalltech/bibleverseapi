# Changelog

All notable changes to Bible Verse Finder will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2024-11-10

### Added
- Single chapter parsing support in Quick Add (e.g., "Psalm 46" or "Psalm 46 NKJV")
- Simplified reference format without requiring verse numbers for whole chapters

### Changed
- Reference parser now checks patterns in optimal order (verse, chapter range, single chapter)
- Updated Quick Add placeholder text with simpler examples
- Updated help text to show all supported formats more clearly
- Pattern matching is now more intuitive and user-friendly

### Fixed
- Parser now correctly identifies single chapter references before attempting other patterns

## [1.1.0] - 2024-11-10

### Added
- **Chapter Range Support**: Fetch multiple chapters at once (e.g., "Psalm 46-47")
- **Whole Chapter Fetching**: Fetch entire chapters without specifying verse numbers
- New "End Chapter" field in Control Panel for chapter range selection
- Support for parsing chapter ranges in Quick Add (e.g., "Psalm 46-47 NKJV")
- Visual indicators showing what will be fetched (chapter range, whole chapter, verse range, or single verse)
- New template variables: `is_chapter_range` and `is_verse_range` for Antlers templates
- `end_chapter` field in data structure for storing chapter ranges

### Changed
- Reference parser now supports three formats:
  - Chapter ranges: "Book Chapter-Chapter" (e.g., "Psalm 46-47")
  - Verse ranges: "Book Chapter:Verse-Verse" (e.g., "John 3:16-17")
  - Single verses: "Book Chapter:Verse" (e.g., "John 3:16")
- "Start Verse" field is now optional (can be left empty for whole chapters)
- Verse input fields are automatically disabled when chapter range is selected
- Grid layout changed from 4 columns to 5 columns to accommodate End Chapter field
- Updated help text to show all supported reference formats

### Technical
- Added `fetchChapterRange()` method to BibleService for fetching multiple chapters
- Added `fetchWholeChapter()` method to BibleService for fetching complete chapters
- Updated `fetchVerse()` signature to accept optional `$endChapter` parameter
- Enhanced validation logic to support chapter-only requests without verses
- Updated API controller to handle nullable `start_verse` and new `end_chapter` parameters
- Improved fieldtype augmentation to distinguish between chapter and verse ranges

## [1.0.0] - 2024-11-10

### Added
- Initial release of Bible Verse Finder for Statamic 5
- Repeater-style fieldtype for adding multiple Bible verses
- Support for 66 books from Genesis to Revelation
- Multiple Bible version support (NKJV, KJV, ESV, NIV, AMP, WEB)
- Quick Add feature with reference parsing (e.g., "John 3:16-17 NKJV")
- Verse range support (single verses or ranges)
- API integration with Bolls.life and Bible-API.com
- Optional API.Bible integration for ESV, NIV, and AMP versions
- Offline mode with downloadable Bible versions as JSON
- Smart caching (30-day default TTL)
- Artisan commands:
  - `bible-verses:test` - Test API connections
  - `bible-verses:download` - Download complete Bible versions
  - `bible-verses:list` - List downloaded versions
  - `bible-verses:clear-cache` - Clear cached verses
- Augmented template data for Antlers
- Validation for chapter and verse references
- Error handling with automatic API fallback
- Vue.js-based Control Panel interface
- Consistent Statamic Control Panel styling
- Publishable fieldset for easy blueprint integration
- Configurable options (max verses, default version, etc.)

### Features
- **Book Selection**: Grouped Old Testament and New Testament dropdowns
- **Real-time Preview**: See fetched verses immediately in Control Panel
- **Refresh All**: Batch refresh all verses in a field
- **Remove Verses**: Easy verse removal with confirmation
- **Fetch on Demand**: Only fetch verses when needed
- **Loading States**: Visual feedback during API calls
- **Error Messages**: User-friendly error display

### Technical
- Built with Vue 2.7 and Vite
- PHP 8.2+ and Statamic 5+ required
- Guzzle HTTP client for API requests
- PSR-4 autoloading
- Comprehensive README documentation
- MIT License

[1.1.1]: https://github.com/westwalltech/bibleverseapi/releases/tag/v1.1.1
[1.1.0]: https://github.com/westwalltech/bibleverseapi/releases/tag/v1.1.0
[1.0.0]: https://github.com/westwalltech/bibleverseapi/releases/tag/v1.0.0
