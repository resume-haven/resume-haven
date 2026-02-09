# ResumeHaven

[![Code Quality](https://github.com/YOUR_USERNAME/resume-haven/actions/workflows/code-quality.yml/badge.svg)](https://github.com/YOUR_USERNAME/resume-haven/actions/workflows/code-quality.yml)
[![CI Pipeline](https://github.com/YOUR_USERNAME/resume-haven/actions/workflows/ci.yml/badge.svg)](https://github.com/YOUR_USERNAME/resume-haven/actions/workflows/ci.yml)
[![Security](https://github.com/YOUR_USERNAME/resume-haven/actions/workflows/security.yml/badge.svg)](https://github.com/YOUR_USERNAME/resume-haven/actions/workflows/security.yml)

A modern resume builder application built with Laravel 12 and FrankenPHP.

## Tech Stack

- **Backend**: Laravel 12.49.0
- **PHP**: 8.5.2 with strict types enabled
- **Web Server**: FrankenPHP (PHP application server)
- **Database**: SQLite (default), easily switchable to PostgreSQL/MySQL
- **Development**: Docker with docker-compose

## Quick Start

```bash
# Start the application
make up

# Access the application
open http://localhost
```

## Installation

### Prerequisites

- Docker and Docker Compose
- Make (optional, but recommended)

### Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd resume-haven
   ```

2. **Start Docker containers**
   ```bash
   make up
   ```

3. **Laravel is pre-installed**
   
   The project comes with Laravel already installed. If you need to reinstall:
   ```bash
   make laravel-install
   ```

4. **Access the application**
   - HTTP: http://localhost
   - HTTPS: https://localhost (self-signed certificate)

## Quick Commands

All commands are available via the self-documenting Makefile:

```bash
make help              # Show all available commands
make up                # Start all containers
make down              # Stop all containers
make shell             # Open shell in app container
make logs              # View container logs
make migrate           # Run database migrations
make test              # Run Pest tests
make lint              # Run Laravel Pint (code style)
```

For a complete list of commands, see [docs/MAKEFILE.md](docs/MAKEFILE.md).

## Code Quality

This project implements comprehensive code quality standards:

- **Pint** - PSR-12 code formatting with strict types
- **PHPStan Level 8** - Static analysis with Larastan
- **Pest** - Modern testing framework with Architecture Tests
- **Rector** - Automated refactoring (PHP 8.5 + Laravel 12)

### Quick Quality Checks

```bash
make lint              # Auto-fix code style
make phpstan           # Static analysis
make test              # Run all tests
make quality           # Validate all (no changes)
```

See [docs/CODE_QUALITY.md](docs/CODE_QUALITY.md) for detailed documentation.

## Development

### Database

The project uses SQLite by default. The database file is located at `database/database.sqlite`.

To run migrations:
```bash
make migrate
```

### Code Style

All PHP files use `declare(strict_types=1)`. The codebase follows PSR-12 standards.

Run code formatting:
```bash
make lint
```

### Testing

Run the test suite:
```bash
make test
```

### Debugging

Xdebug is pre-configured. See [docs/XDEBUG.md](docs/XDEBUG.md) for setup instructions.

## API Endpoints

All JSON endpoints live in `routes/api.php`.

**Resumes**
- `GET /api/resumes/{id}` - Fetch resume read model
- `POST /api/resumes` - Create resume

Example:
```bash
curl -X POST http://localhost/api/resumes \
   -H "Content-Type: application/json" \
   -d '{"name":"Test Resume","email":"resume@example.com"}'
```

Example (httpie):
```bash
http POST http://localhost/api/resumes name="Test Resume" email="resume@example.com"
```

**Users**
- `GET /api/users/{id}` - Fetch user read model
- `POST /api/users` - Create user

Example:
```bash
curl -X POST http://localhost/api/users \
   -H "Content-Type: application/json" \
   -d '{"name":"Test User","email":"user@example.com","password":"password123"}'
```

Example (httpie):
```bash
http POST http://localhost/api/users name="Test User" email="user@example.com" password="password123"
```

## Project Structure

```
resume-haven/
├── app/                    # Laravel application code
├── bootstrap/              # Laravel bootstrap files
├── config/                 # Configuration files
├── database/               # Migrations, seeders, factories
├── docker/                 # Docker configuration
│   └── FrankenPHP/        # FrankenPHP Dockerfile & Caddyfile
├── docs/                   # Documentation
├── public/                 # Web root
├── resources/              # Views, assets
├── routes/                 # Route definitions
│   ├── api.php            # API routes (stateless)
│   └── web.php            # Web routes (CSRF protected)
├── storage/                # Logs, cache, uploads
├── tests/                  # Test suite
├── Makefile               # Development commands
└── docker-compose.yml     # Docker services
```

Current UI layer lives in `app/Http` (controllers, middleware, requests). A separate Vue.js frontend is planned for a later phase.
Query handlers return read models via read repositories; command handlers work with domain entities.

## Documentation

### 📋 Setup & Development
- [Docker Setup](docs/DOCKER.md) - Docker configuration and usage
- [Development Guide](docs/DEVELOPMENT.md) - Development workflow and standards
- [Makefile Reference](docs/MAKEFILE.md) - Complete Makefile documentation
- [Xdebug Setup](docs/XDEBUG.md) - Debugging configuration

### 🔍 Code Quality & Testing
- [Code Quality Guide](docs/CODE_QUALITY.md) - Pint, PHPStan, Pest, Rector
- [GitHub Actions](docs/GITHUB_ACTIONS.md) - CI/CD workflows and setup
- [GitHub Actions Quick Reference](docs/GITHUB_ACTIONS_QUICK.md) - Quick command reference

### 🏗️ Architecture & Planning
- [Architecture Overview](docs/ARCHITECTURE.md) - System architecture and diagrams
- [Technical Debt](docs/TECHNICAL_DEBT.md) - Known issues and TODOs
- [Deployment Guide](docs/DEPLOYMENT.md) - Deployment instructions

## Contributing

## AI Instructions

If you use an AI coding assistant, please follow the guidance in
[.github/agents/default.yaml](.github/agents/default.yaml).

We welcome contributions! Please read our [Contributing Guide](CONTRIBUTING.md) for details on:

- Code of conduct
- Development process
- Commit message conventions (Conventional Commits)
- Pull request process

### Commit Message Format

We use [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/):

```
feat: add user authentication
fix: resolve database connection issue
docs: update installation guide
```

## CI/CD

All code is automatically validated through GitHub Actions:

- **Code Quality Workflow** - Pint, PHPStan, Rector checks
- **CI Pipeline** - Full test suite with coverage
- **Security Workflow** - Architecture tests, dependency validation

Pull requests require all checks to pass before merging.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

For questions or issues, please open an issue on GitHub.
