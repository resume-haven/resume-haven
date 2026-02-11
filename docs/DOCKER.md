# Docker Setup Guide

## Overview

ResumeHaven runs in a Docker container using **FrankenPHP** (PHP 8.5 with Caddy web server) on Alpine Linux. This provides a lightweight, secure, and efficient development environment.

## Architecture

### Services

**app** - FrankenPHP container
- PHP 8.5.2 (Thread-Safe)
- Caddy web server (built-in)
- Xdebug 3.5.0 for debugging
- Composer package manager
- SQLite database support

**mailpit** - Local mail catcher
- SMTP server for development emails
- Web UI for viewing messages

## Prerequisites

- Docker Desktop 4.0+ or Docker + Docker Compose
- WSL2 on Windows (recommended for performance)
- At least 2GB available disk space
- 2GB available RAM

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd resume-haven
```

### 2. Prepare Environment File

```bash
cp .env.docker .env
```

### 3. Build and Start Containers

```bash
docker-compose up -d --build
```

The first build takes 3-5 minutes to compile PHP extensions.

### 4. Verify Installation

```bash
# Check container status
docker-compose ps

# Verify PHP version
docker-compose exec app php -v
```

### 5. Mailpit Access

Mailpit captures all outgoing mail locally.

- Web UI: http://localhost:8025
- SMTP: mailpit:1025

Ensure the mail settings point to Mailpit:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

## Container Management

### Starting Services

```bash
# Start in background
docker-compose up -d

# Start with log output
docker-compose up
```

### Stopping Services

```bash
# Stop all containers
docker-compose stop

# Stop and remove containers
docker-compose down
```

### Viewing Logs

```bash
# View and follow app logs
docker-compose logs -f app

# View last 50 lines
docker-compose logs --tail=50 app
```

### Shell Access

```bash
# Enter container shell
docker-compose exec app bash

# Run PHP command
docker-compose exec app php -v
```

## Dockerfile Configuration

Located at `docker/FrankenPHP/Dockerfile`

### Installed PHP Extensions

- `pdo_sqlite` - SQLite database driver
- `zip` - ZIP file handling
- `gd` - Image processing (with webp, jpeg, freetype support)
- `bcmath` - Arbitrary precision arithmetic
- `exif` - EXIF metadata reading
- `intl` - Internationalization
- `pcntl` - Process control
- `sockets` - Low-level socket operations
- `xdebug` - Debugging and profiling

## Troubleshooting

### Container Won't Start

```bash
# View error logs
docker-compose logs app

# Full rebuild
docker rm -f resume-haven-app
docker-compose up -d --build
```

### Port Already in Use

```bash
# Windows: Find process using port 80
netstat -ano | findstr :80

# Use different port in docker-compose.yml
# Change "80:80" to "8000:80"
```

## Next Steps

1. [Setup Xdebug debugging](XDEBUG.md)
2. [Review Development Guidelines](DEVELOPMENT.md)
3. [Check Project Architecture](ARCHITECTURE.md)
