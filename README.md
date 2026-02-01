# ResumeHaven

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
make test              # Run PHPUnit tests
make lint              # Run Laravel Pint (code style)
```

For a complete list of commands, see [docs/MAKEFILE.md](docs/MAKEFILE.md).

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
├── storage/                # Logs, cache, uploads
├── tests/                  # Test suite
├── Makefile               # Development commands
└── docker-compose.yml     # Docker services
```

## Documentation

- [Docker Setup](docs/DOCKER.md) - Docker configuration and usage
- [Development Guide](docs/DEVELOPMENT.md) - Development workflow and standards
- [Architecture](docs/ARCHITECTURE.md) - System architecture overview
- [Deployment](docs/DEPLOYMENT.md) - Deployment instructions
- [Makefile Reference](docs/MAKEFILE.md) - Complete Makefile documentation
- [Xdebug Setup](docs/XDEBUG.md) - Debugging configuration

## Contributing

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

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

For questions or issues, please open an issue on GitHub.
