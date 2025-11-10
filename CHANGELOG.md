# Changelog

All notable changes to Bible Verse Finder will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[1.0.0]: https://github.com/westwalltech/bibleverseapi/releases/tag/v1.0.0
