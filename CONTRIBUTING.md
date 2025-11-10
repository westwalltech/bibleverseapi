# Contributing to Bible Verse Finder

Thank you for considering contributing to Bible Verse Finder! We welcome contributions from the community.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the [issue tracker](https://github.com/westwalltech/bibleverseapi/issues) to see if the problem has already been reported. If it has and the issue is still open, add a comment to the existing issue instead of opening a new one.

When creating a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** (code snippets, screenshots, etc.)
- **Describe the behavior you observed** and what you expected
- **Include your environment details**: PHP version, Statamic version, Laravel version

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the proposed feature
- **Explain why this enhancement would be useful** to most users
- **List any similar features** in other tools if applicable

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Install dependencies**: `composer install && pnpm install`
3. **Make your changes** following our coding standards (see below)
4. **Test your changes** thoroughly in a real Statamic project
5. **Update documentation** if needed (README.md, CHANGELOG.md, etc.)
6. **Commit your changes** with clear, descriptive commit messages
7. **Push to your fork** and submit a pull request

#### Pull Request Guidelines

- Follow the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard for PHP
- Write clear, descriptive commit messages
- Update the CHANGELOG.md with your changes under "Unreleased"
- One pull request per feature/fix
- Include tests if adding new functionality (when available)
- Ensure the addon still works with Statamic 5+

## Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and pnpm
- A Statamic 5 project for testing

### Local Development

1. Clone your fork:
   ```bash
   git clone https://github.com/YOUR-USERNAME/bibleverseapi.git
   cd bibleverseapi
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   pnpm install
   ```

4. Build frontend assets:
   ```bash
   pnpm run build
   ```

5. For development with hot reload:
   ```bash
   pnpm run dev
   ```

### Testing in a Statamic Project

1. In your Statamic project's `composer.json`, add a path repository:
   ```json
   {
       "repositories": [
           {
               "type": "path",
               "url": "../path/to/bibleverseapi"
           }
       ]
   }
   ```

2. Require the package:
   ```bash
   composer require westwalltech/bibleverseapi
   ```

3. After making changes to JavaScript/CSS:
   ```bash
   cd path/to/bibleverseapi
   pnpm run build
   cd path/to/statamic-project
   php please vendor:publish --tag=bible-verse-finder --force
   php artisan cache:clear
   ```

## Coding Standards

### PHP

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use type hints where possible
- Write descriptive variable and method names
- Add PHPDoc blocks for classes and methods
- Keep methods focused and single-purpose

### JavaScript/Vue

- Use Vue 2.7 composition
- Follow Vue.js style guide
- Use clear, descriptive component names
- Add comments for complex logic
- Use ES6+ features

### CSS

- Use Tailwind CSS utility classes
- Match Statamic's Control Panel design patterns
- Keep custom styles minimal
- Use Tailwind's `@apply` directive for component styles

## Commit Messages

- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit first line to 72 characters
- Reference issues and pull requests after the first line

Example:
```
Add support for ESV Bible version

- Integrate API.Bible for ESV text
- Add environment variable for API key
- Update documentation with ESV setup

Closes #123
```

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inspiring community for all. Please be respectful and constructive in your interactions.

### Expected Behavior

- Be respectful and inclusive
- Welcome newcomers and help them get started
- Focus on what is best for the community
- Show empathy towards other community members

### Unacceptable Behavior

- Harassment, discrimination, or derogatory comments
- Trolling, insulting comments, or personal attacks
- Publishing others' private information
- Other conduct inappropriate in a professional setting

## Questions?

Feel free to open an issue with the "question" label or reach out to the maintainers.

## License

By contributing to Bible Verse Finder, you agree that your contributions will be licensed under the MIT License.
