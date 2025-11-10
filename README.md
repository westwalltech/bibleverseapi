# Bible Verse Finder for Statamic

A powerful Statamic addon that allows you to select Bible verses in the Control Panel, fetch them once from external APIs, and store them in YAML for distribution across your website and apps.

## Features

- **Repeater-Style Fieldtype**: Add multiple Bible verses to any entry
- **66 Books Supported**: All books from Genesis to Revelation
- **Multiple Versions**: NKJV, KJV, ESV, NIV, AMP, WEB
- **Reference Parsing**: Paste "John 3:16-17 NKJV" to auto-populate fields
- **Verse Ranges**: Select single verses or ranges (e.g., John 1:1-5)
- **API Integration**: Fetches verses from bolls.life and fallback APIs
- **Offline Mode**: Download complete Bible versions as JSON
- **Smart Caching**: Caches API responses for 30 days
- **Validation**: Prevents invalid chapter/verse selections
- **Augmented Template Data**: Easy access in Antlers templates

## Installation

### Via Composer (Recommended)

```bash
composer require westwalltech/bibleverseapi
```

### Local Development

If developing within a Statamic project:

1. Add to your root `composer.json`:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "addons/bible-verse-finder"
        }
    ]
}
```

2. Require the package:
```bash
composer require westwalltech/bibleverseapi
```

## Configuration

### Publish Config (Optional)

```bash
php please vendor:publish --tag=bible-verse-finder-config
```

Edit `config/bible-verse-finder.php` to customize:

- API endpoints and fallback order
- Available Bible versions
- Cache duration
- Formatting options (include verse numbers, strip HTML)
- Download sources for offline JSON

### Environment Variables

Add to your `.env` file:

```env
# Optional: API.Bible key for ESV, NIV, AMP support
SCRIPTURE_API_KEY=your_api_key_here
```

Get a free API key at [https://scripture.api.bible](https://scripture.api.bible)

## Usage

### Add to Blueprint

1. **Using Fieldset (Recommended)**

   Publish the fieldset:
   ```bash
   php please vendor:publish --tag=bible-verse-finder-fieldsets
   ```

   Import in your blueprint YAML:
   ```yaml
   fields:
     -
       import: bible-verse-finder::bible_verses
   ```

2. **Manual Configuration**

   Add directly to your blueprint YAML:
   ```yaml
   fields:
     -
       handle: scripture
       field:
         type: bible_verse_finder
         display: 'Scripture References'
         default_version: NKJV
         enable_reference_parsing: true
         max_verses: 10
   ```

### In the Control Panel

#### Method 1: Quick Add with Reference Parser

1. Paste a reference in the "Quick Add" field: `John 3:16-17 NKJV`
2. Click "Parse & Add"
3. The verse is automatically fetched and added

#### Method 2: Manual Entry

1. Click "Add Verse"
2. Select book from grouped dropdown (Old/New Testament)
3. Enter chapter, start verse, and optional end verse
4. Select Bible version
5. Click "Fetch Verse"
6. Preview appears showing the fetched text

#### Refresh All Verses

Click the "Refresh All" button to refetch all verses (useful after updating versions or fixing errors)

### In Antlers Templates

```antlers
{{ bible_verses }}
  <div class="scripture">
    <h3>{{ reference }} ({{ version_name }})</h3>
    <p>{{ text }}</p>

    {{ if is_range }}
      <small>This is a verse range</small>
    {{ /if }}
  </div>
{{ /bible_verses }}
```

#### Available Fields

- `reference` - Formatted reference (e.g., "John 3:16-17")
- `book` - Book name (e.g., "John")
- `chapter` - Chapter number
- `start_verse` - Starting verse number
- `end_verse` - Ending verse number
- `version` - Version code (e.g., "NKJV")
- `version_name` - Full version name (e.g., "New King James Version")
- `text` - Complete verse text with verse numbers
- `text_without_numbers` - Text without leading verse numbers
- `is_range` - Boolean indicating if this is a verse range
- `has_text` - Boolean indicating if text was successfully fetched
- `fetched_at` - Timestamp when verse was fetched
- `api_source` - Which API was used (e.g., "bolls")

## Artisan Commands

### Test API Connection

```bash
php artisan bible-verses:test
php artisan bible-verses:test --book=Romans --chapter=8 --verse=28 --version=KJV
```

### Download Bible Versions

Download complete Bible versions as JSON for faster lookups and offline use:

```bash
# Download specific versions
php artisan bible-verses:download kjv nkjv

# Download all available versions
php artisan bible-verses:download --all
```

JSON files are stored in `storage/app/bible-verses/`

### List Downloaded Versions

```bash
php artisan bible-verses:list
```

Shows a table with:
- Version code and name
- Download status
- File size
- Download availability

### Clear Cache

```bash
php artisan bible-verses:clear-cache
```

Clears all cached Bible verses.

## Supported Bible Versions

### Free (No API Key Required)

- **NKJV** - New King James Version
- **KJV** - King James Version
- **WEB** - World English Bible

### Requires API.Bible Key

- **ESV** - English Standard Version
- **NIV** - New International Version
- **AMP** - Amplified Bible

To enable ESV, NIV, and AMP:
1. Sign up at [https://scripture.api.bible](https://scripture.api.bible)
2. Add `SCRIPTURE_API_KEY` to your `.env`
3. The addon will automatically use it

## API Integration

### Primary API: Bolls.life

- **Endpoint**: `https://bolls.life`
- **Versions**: NKJV, KJV, WEB
- **Rate Limits**: None specified
- **Free**: Yes

### Fallback API: Bible-API.com

- **Endpoint**: `https://bible-api.com`
- **Versions**: KJV, WEB
- **Rate Limits**: None
- **Free**: Yes

### Optional: API.Bible (Scripture API)

- **Endpoint**: `https://api.scripture.api.bible`
- **Versions**: 1000+ versions including ESV, NIV, AMP
- **Rate Limits**: 500 requests/day (free tier)
- **Requires**: Free API key

## Performance Optimization

### Caching

All API responses are cached for 30 days by default. Since Bible verses don't change, this greatly improves performance.

Configure in `config/bible-verse-finder.php`:
```php
'cache' => [
    'enabled' => true,
    'ttl' => 86400 * 30, // 30 days
],
```

### Local JSON Storage

For maximum performance, download complete Bible versions:

```bash
php artisan bible-verses:download kjv nkjv
```

The addon will automatically use local JSON files instead of making API calls.

## Validation

The addon validates verse references before making API calls:

- Book name must match or use valid abbreviations
- Chapter must exist in the selected book
- Verse numbers must exist in the chapter
- End verse must be greater than or equal to start verse
- Verse ranges are limited to 20 verses by default (configurable)

## Error Handling

- **Invalid References**: Shows user-friendly error in Control Panel
- **API Failures**: Automatically tries fallback APIs
- **Network Errors**: Displays error message, allows retry
- **Missing Books**: Validates against Bible metadata before API call

## Development

### Building Assets

```bash
cd addons/bible-verse-finder
pnpm install
pnpm run build
```

For development with hot reload:
```bash
pnpm run dev
```

### Testing

```bash
composer test
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release notes.

## License

MIT License. See [LICENSE](LICENSE) for details.

## Support

- **Issues**: [GitHub Issues](https://github.com/westwalltech/bibleverseapi/issues)
- **Discussions**: [GitHub Discussions](https://github.com/westwalltech/bibleverseapi/discussions)

## Credits

Created by [Westwall Tech](https://newsongchurch.org)

### APIs Used

- [Bolls.life](https://bolls.life) - Primary Bible API
- [Bible-API.com](https://bible-api.com) - Fallback API
- [API.Bible](https://scripture.api.bible) - Optional premium API

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Roadmap

- [ ] Support for additional Bible versions
- [ ] Comparison mode (show multiple versions side-by-side)
- [ ] Reading plan generator
- [ ] Cross-reference suggestions
- [ ] Search by topic/keyword
- [ ] Export to PDF/Word
- [ ] Audio verse playback integration

## Related Addons

- [Podcast Link Finder](https://github.com/newsong/podcast-link-finder) - Find podcast episodes across platforms
# bibleverseapi
