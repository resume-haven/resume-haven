# ResumeHaven

A modern resume builder application developed with PHP 8.5, running on FrankenPHP in a Docker container with IDE debugging support.

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- VS Code (optional, for debugging)
- WSL2 on Windows (for optimal Docker performance)

### Start Development Environment

```bash
# Clone and setup
git clone <repository-url>
cd resume-haven

# Start Docker containers
docker-compose up -d

# Access the application
open http://localhost
```

The application will be available at `http://localhost`.

## 📋 Project Overview

### Technology Stack
- **Runtime**: PHP 8.5 on FrankenPHP (Alpine Linux)
- **Web Server**: Caddy (built into FrankenPHP)
- **Package Manager**: Composer
- **Development Tools**: Xdebug 3.5.0
- **Code Quality**: Strict types enforcement (`declare(strict_types=1)`)

### Directory Structure

```
resume-haven/
├── public/              # Web entry point
├── app/                 # Application source code
├── bootstrap/           # Application bootstrap files
├── storage/             # Temporary files, logs, cache
├── docker/              # Docker configuration
│   └── FrankenPHP/      # FrankenPHP specific configs
├── docs/                # Documentation
├── docker-compose.yml   # Docker services definition
└── README.md            # This file
```

## 🐳 Docker Setup

ResumeHaven uses **FrankenPHP** (PHP 8.5 on Alpine Linux) for efficient, lightweight containerization.

### Services

- **app**: FrankenPHP container with all PHP extensions and Xdebug

### Starting Services

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f app

# Stop services
## 🔨 Quick Commands with Make

This project includes a `Makefile` for simplified command execution:

```bash
# View all available commands
make help

# Common commands
make up              # Start containers
make down            # Stop containers
make install         # Install dependencies
make test            # Run tests
make shell           # Open container shell
make logs            # View logs
```

**Note:** Make requires Linux/macOS or WSL2 on Windows. See [docs/MAKEFILE.md](docs/MAKEFILE.md) for details.

## 🐳 Docker Commands

Alternatively, use Docker Compose directly:

```bash
# Start containers
docker-compose up -d

# View logs
docker-compose logs -f app

# Stop containers
docker-compose down

# Open a shell in the container
docker-compose exec app bash

# Run PHP commands
docker-compose exec app php -v
docker-compose exec app composer install
```

## 🐛 Debugging with VS Code

This project is configured for **Xdebug 3.5.0** debugging through VS Code.

### Setup Debugging

1. **Install PHP Debug extension** in VS Code (by Felix Becker)
   - Extension ID: `felixbecker.php-debug`

2. **Start listening** for Xdebug connections:
   - Press `F5` or click Debug → Start Debugging
   - Select "Listen for Xdebug" configuration

3. **Set breakpoints** in your code by clicking the line number

4. **Trigger debugging** by accessing http://localhost in your browser

### Xdebug Configuration

- **Port**: 9003
- **Mode**: debug
- **Auto-discovery**: Enabled (for WSL2 compatibility)
- **Fallback Host**: `host.docker.internal`

See [docs/XDEBUG.md](docs/XDEBUG.md) for detailed debugging guide.

## 📝 Code Quality Standards

All PHP files follow strict typing standards:

```php
<?php
declare(strict_types=1);

namespace App\Models;

// Code here...
```

This enforces type safety across the entire codebase. See [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md) for guidelines.

## 📚 Documentation

- [Docker Setup Guide](docs/DOCKER.md) - Container configuration and management
- [Xdebug Debugging Guide](docs/XDEBUG.md) - IDE debugging setup and troubleshooting
- [Development Guide](docs/DEVELOPMENT.md) - Code standards and best practices
- [Architecture Guide](docs/ARCHITECTURE.md) - Project structure and design patterns
- [Deployment Guide](docs/DEPLOYMENT.md) - Production deployment instructions

## 🔧 Common Commands

### Development

```bash
# Enter container shell
docker-compose exec app bash

# Check PHP version and extensions
docker-compose exec app php -v

# Run Composer
docker-compose exec app composer install
docker-compose exec app composer update

# View application logs
docker-compose logs -f app
```

### Maintenance

```bash
# Rebuild containers (after Dockerfile changes)
docker-compose up -d --build

# Remove all containers and networks
docker-compose down

# Clean up old images and unused resources
docker system prune
```

## 🐛 Troubleshooting

### Container won't start
```bash
# Force remove old container
docker rm -f resume-haven-app

# Rebuild and start fresh
docker-compose up -d --build
```

### Xdebug not connecting
- Verify Xdebug is loaded: `docker-compose exec app php -m | grep xdebug`
- Check configuration: `docker-compose exec app php -i | grep xdebug`
- See [docs/XDEBUG.md](docs/XDEBUG.md) for detailed troubleshooting

### Permission issues in mounted volumes
```bash
# Fix permissions from host
chmod -R 755 storage bootstrap/cache
```

## 📄 License

See [LICENSE.md](LICENSE.md) for license information.

## 👨‍💻 Development

For detailed development guidelines, coding standards, and best practices, see [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md).

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

### Commit Standards

This project uses [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/):

```bash
# Feature
git commit -m "feat(export): add PDF export functionality"

# Bug fix
git commit -m "fix(validation): prevent null pointer in email validator"

# Documentation
git commit -m "docs: update deployment guide"
```

See [CONTRIBUTING.md](CONTRIBUTING.md) for detailed commit guidelines.
