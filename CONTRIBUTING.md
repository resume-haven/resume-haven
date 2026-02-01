# Contributing to ResumeHaven

Welcome! We're glad you're interested in contributing to ResumeHaven. This document provides guidelines and instructions for contributing.

## Code of Conduct

Be respectful and constructive in all interactions with other contributors.

## Getting Started

### 1. Fork and Clone

```bash
# Fork on GitHub
# Clone your fork
git clone https://github.com/YOUR-USERNAME/resume-haven.git
cd resume-haven

# Add upstream remote
git remote add upstream https://github.com/ORIGINAL-OWNER/resume-haven.git
```

### 2. Create Feature Branch

Always use conventional branch names:

```bash
# Feature branch
git checkout -b feat/add-resume-templates

# Bug fix branch
git checkout -b fix/email-validation

# Documentation branch
git checkout -b docs/update-deployment-guide
```

**Branch Naming:**
- `feat/<description>` - New features
- `fix/<description>` - Bug fixes
- `docs/<description>` - Documentation
- `refactor/<description>` - Refactoring
- `test/<description>` - Tests

### 3. Make Changes

Follow the [Development Guide](DEVELOPMENT.md):
- Use strict types in all PHP files
- Follow PSR-12 code style
- Write clear commit messages (see below)
- Add tests for new features
- Update documentation

### 4. Commit with Conventional Commits

Follow [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/):

```bash
# Good examples
git commit -m "feat(models): add Resume template support"
git commit -m "fix(export): correct PDF margin calculation"
git commit -m "docs: add Conventional Commits guide"
git commit -m "test(services): add unit tests for ResumeService"
git commit -m "refactor(validation): simplify email validation logic"
```

**Commit Message Template:**
```
<type>(<scope>): <subject>

<body>

<footer>
```

**Guidelines:**
- Type: One of feat, fix, docs, style, refactor, perf, test, chore, ci
- Scope: Optional, indicates affected component
- Subject: Imperative mood, lowercase, no period
- Body: Explain why, not what (diff shows what)
- Footer: Reference issues (Closes #123) or breaking changes

### 5. Push and Create Pull Request

```bash
# Push to your fork
git push origin feat/your-feature

# Create PR on GitHub
# Fill in PR template with:
# - Description of changes
# - Why changes are needed
# - How to test
# - Screenshots (if UI changes)
```

## Pull Request Process

### PR Requirements

- [ ] Branch is up-to-date with `main`
- [ ] Commits follow Conventional Commits format
- [ ] Code passes local tests
- [ ] Documentation updated
- [ ] No unnecessary files committed
- [ ] Descriptive PR title using type prefix

### PR Title Format

```
feat(scope): description
fix: short description
docs: update guide
```

### PR Description Template

```markdown
## Description
Clear explanation of what this PR does.

## Motivation and Context
Why is this change needed? What problem does it solve?

## How Has This Been Tested?
- [ ] Local testing completed
- [ ] All tests pass
- [ ] Manual testing done

## Screenshots (if applicable)
Include screenshots for UI changes.

## Types of Changes
- [ ] Bug fix (non-breaking change fixing an issue)
- [ ] New feature (non-breaking change adding functionality)
- [ ] Breaking change (fix or feature causing existing functionality to change)
- [ ] Documentation update

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex logic
- [ ] Documentation updated
- [ ] No new warnings generated
- [ ] Tests added/updated
```

## Code Review

### What to Expect

- Constructive feedback on code
- Discussion of approach and design
- Requests for improvements
- Approval when ready

### Tips for Review

1. **Be patient** - Maintainers are volunteers
2. **Be responsive** - Address feedback promptly
3. **Ask questions** - Clarify feedback if unclear
4. **Iterate** - Make requested changes and push updates
5. **Don't be defensive** - Feedback improves code

### Reviewer Checklist

Reviewers will check:
- [ ] Code quality and style
- [ ] Strict types enforced
- [ ] Test coverage adequate
- [ ] Documentation complete
- [ ] Conventional Commits used
- [ ] No security issues
- [ ] Performance acceptable
- [ ] No breaking changes (or documented)

## Coding Standards

### Strict Types

All PHP files must use strict types:

```php
<?php
declare(strict_types=1);

namespace App\Models;

class MyClass { }
```

### Type Hints

All methods must have type hints:

```php
// ❌ Bad
public function process($input) { }

// ✅ Good
public function process(array $input): string { }
```

### PSR-12 Style

Follow [PSR-12](https://www.php-fig.org/psr/psr-12/):
- 4-space indentation
- One blank line between methods
- Opening braces on same line
- Class constants in UPPER_SNAKE_CASE

### Documentation

Document public APIs with PHPDoc:

```php
/**
 * Generates a resume in the specified format.
 *
 * @param array $resumeData The resume data
 * @param string $format Export format (pdf, docx)
 * @return string Formatted resume content
 * @throws \InvalidArgumentException If format unsupported
 */
public function generate(array $resumeData, string $format): string
{
    // Implementation
}
```

## Testing

### Writing Tests

Add tests for all new features:

```php
<?php
declare(strict_types=1);

class ResumeTest extends TestCase
{
    public function testCanCreateResume(): void
    {
        $resume = new Resume('John Doe');
        $this->assertEquals('John Doe', $resume->name());
    }
    
    public function testThrowsOnInvalidName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Resume('');
    }
}
```

### Running Tests

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test file
docker-compose exec app php artisan test tests/Unit/ResumeTest.php

# Run with coverage
docker-compose exec app php artisan test --coverage
```

## Documentation

### Updating Docs

When changing functionality:
1. Update relevant doc files in `docs/`
2. Add examples if behavior changed
3. Update README if public API changed
4. Keep docs in English

### Doc Files

- [README.md](../README.md) - Project overview
- [docs/DEVELOPMENT.md](DEVELOPMENT.md) - Development guidelines
- [docs/DOCKER.md](DOCKER.md) - Docker setup
- [docs/XDEBUG.md](XDEBUG.md) - Debugging guide
- [docs/ARCHITECTURE.md](ARCHITECTURE.md) - System design
- [docs/DEPLOYMENT.md](DEPLOYMENT.md) - Deployment guide

## Development Tools

## Development Tools

### Using Make Commands

This project includes a `Makefile` for common tasks:

```bash
# View all available commands
make help

# Common commands
make up              # Start containers
make down            # Stop containers
make install         # Install dependencies
make test            # Run tests
make lint            # Check code style
make shell           # Open container shell
make logs            # View logs
make dev             # Full setup: up + install
```

### VS Code Extensions

Recommended extensions:
- PHP Debug (felixbecker.php-debug)
- PHP Intelephense (bmewburn.vscode-intelephense-client)
- PHP Sniffer & Beautifier (valeryanm.vscode-phpsab)
- GitLens (eamodio.gitlens)

### Local Development

Using Make (recommended):

```bash
# Initial setup
make dev

# Enter container
make shell

# View logs
make logs

# Run tests
make test

# Check code style
make lint

# Stop everything
make down
```

### Debugging

Enable Xdebug in VS Code:
1. Press F5
2. Select "Listen for Xdebug"
3. Set breakpoints
4. Reload browser

See [docs/XDEBUG.md](XDEBUG.md) for details.

## Common Issues

### "My commits aren't following Conventional Commits"

Amend and force push:

```bash
# Fix last commit
git commit --amend -m "feat: correct message"

# Update PR
git push origin feat/branch-name --force-with-lease
```

### "CI tests are failing"

```bash
# Check what failed
docker-compose logs app

# Run tests locally
docker-compose exec app php artisan test

# Run code style check
docker-compose exec app composer lint
```

### "I have conflicts with main branch"

```bash
# Update main
git fetch upstream
git rebase upstream/main

# If conflicts, resolve and continue
git add .
git rebase --continue

# Force push to your branch
git push origin feat/your-feature --force-with-lease
```

## Release Process

### Version Numbering

Uses [Semantic Versioning](https://semver.org/):
- `MAJOR.MINOR.PATCH` (e.g., 1.2.3)
- MAJOR: Breaking changes
- MINOR: New features (backward compatible)
- PATCH: Bug fixes

### Changelog

Generated from Conventional Commits:
- `feat:` → Minor version bump, "Features" section
- `fix:` → Patch version bump, "Bug Fixes" section
- `BREAKING CHANGE:` → Major version bump

## Resources

- [Conventional Commits](https://www.conventionalcommits.org/)
- [Semantic Versioning](https://semver.org/)
- [PSR-12 Code Style](https://www.php-fig.org/psr/psr-12/)
- [PHP Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- [Git Workflow Guide](https://guides.github.com/introduction/flow/)

## Questions?

- Check existing [Issues](../../issues)
- Read documentation in [docs/](.)
- Look at recent [Pull Requests](../../pulls)
- Ask in discussions

Thank you for contributing!
