# Container and Configuration

## Container Configuration

### FrankenPHP Container

```
┌─────────────────────────┐
│   FrankenPHP Container  │
├─────────────────────────┤
│  Caddy Web Server       │
│  PHP 8.5.2              │
│  Xdebug 3.5.0           │
│  Composer               │
│  SQLite                 │
└─────────────────────────┘
    ↓
  HTTP/HTTPS
    ↓
┌─────────────────────────┐
│   Host Machine          │
│   Port 80, 443          │
└─────────────────────────┘
```

### Volume Architecture

```
Host Machine                Container
────────────────          ──────────
./ ────────────────────→  /var/www/html
storage/ ─────────────→  /var/www/html/storage
bootstrap/cache/ ──────→  /var/www/html/bootstrap/cache
```

## Configuration Management

### Environment Configuration

```
.env.docker (template)
    ↓
.env (instance-specific)
    ↓
Application Configuration
```

**Development**
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
XDEBUG_MODE=debug
```

**Production**
```env
APP_ENV=production
APP_DEBUG=false
XDEBUG_MODE=off
```

## Deployment Considerations

### Container-Based Deployment

1. Build Docker image
2. Push to registry
3. Deploy to orchestrator (Kubernetes, Docker Swarm, etc.)
4. Mount persistent volumes for storage

### Database Migrations

```bash
docker-compose exec app php artisan migrate
```

### Configuration Management

- Environment variables via `.env`
- Secrets via Docker secrets (production)
- Configuration files (development)
