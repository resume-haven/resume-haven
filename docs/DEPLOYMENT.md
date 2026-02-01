# Deployment Guide

## Overview

This guide covers deploying ResumeHaven to production environments. The application is containerized using Docker, making it portable across different hosting providers.

## Deployment Architecture

```
Source Code (Git)
    ↓
Build Docker Image
    ↓
Push to Registry (Docker Hub, ECR, etc.)
    ↓
Deploy to Production (Server, Kubernetes, etc.)
    ↓
Persistent Storage (Database, Files)
    ↓
Running Application
```

## Pre-Deployment Checklist

### Security

- [ ] Set `APP_DEBUG=false` in production
- [ ] Enable HTTPS with valid SSL certificate
- [ ] Set secure session cookie settings
- [ ] Configure CORS for allowed origins
- [ ] Implement rate limiting
- [ ] Enable CSRF protection
- [ ] Add security headers (CSP, X-Frame-Options, etc.)

### Database

- [ ] Run migrations: `php artisan migrate --force`
- [ ] Backup existing data
- [ ] Test rollback procedure
- [ ] Monitor database size and performance

### Application

- [ ] All tests passing
- [ ] Code review completed
- [ ] Performance tested under load
- [ ] Error handling verified
- [ ] Logging configured
- [ ] Monitoring setup

### Infrastructure

- [ ] SSL certificate obtained
- [ ] Load balancer configured (if needed)
- [ ] Database backup automation setup
- [ ] Monitoring and alerting configured

## Building for Production

### 1. Prepare Environment

```bash
# Clone repository
git clone <repository-url>
cd resume-haven

# Set production environment
cp .env.production .env

# Edit .env for production
# APP_DEBUG=false
# APP_ENV=production
# XDEBUG_MODE=off
# etc.
```

### 2. Build Docker Image

```bash
# Build production image
docker build -t resume-haven:1.0.0 \
  --build-arg BUILD_ENV=production \
  -f docker/FrankenPHP/Dockerfile .

# Tag for registry
docker tag resume-haven:1.0.0 your-registry/resume-haven:1.0.0
```

### 3. Push to Registry

```bash
# Login to registry
docker login your-registry

# Push image
docker push your-registry/resume-haven:1.0.0

# Verify
docker pull your-registry/resume-haven:1.0.0
```

## Deployment Options

### Option 1: Docker Compose (Single Server)

Best for: Small teams, simple deployments

```bash
# On production server
docker-compose -f docker-compose.production.yml up -d

# Verify
docker-compose ps
curl http://localhost/health
```

**Production docker-compose.yml:**
```yaml
version: '3.8'
services:
  app:
    image: your-registry/resume-haven:1.0.0
    container_name: resume-haven-app
    restart: always
    environment:
      APP_ENV: production
      APP_DEBUG: "false"
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s
      timeout: 10s
      retries: 3
```

### Option 2: Kubernetes (Scalable)

Best for: High traffic, enterprise deployments

**Deployment manifest (k8s-deployment.yaml):**
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: resume-haven
spec:
  replicas: 3
  selector:
    matchLabels:
      app: resume-haven
  template:
    metadata:
      labels:
        app: resume-haven
    spec:
      containers:
      - name: app
        image: your-registry/resume-haven:1.0.0
        ports:
        - containerPort: 80
        env:
        - name: APP_ENV
          value: "production"
        - name: APP_DEBUG
          value: "false"
        resources:
          requests:
            memory: "256Mi"
            cpu: "100m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /health
            port: 80
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /health
            port: 80
          initialDelaySeconds: 5
          periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: resume-haven
spec:
  selector:
    app: resume-haven
  type: LoadBalancer
  ports:
  - port: 80
    targetPort: 80
  - port: 443
    targetPort: 443
```

Deploy to Kubernetes:
```bash
kubectl apply -f k8s-deployment.yaml
kubectl get pods
kubectl logs deployment/resume-haven
```

### Option 3: Cloud Platforms

#### AWS ECS

```bash
# Push to ECR
aws ecr get-login-password --region us-east-1 | \
  docker login --username AWS --password-stdin \
  <account>.dkr.ecr.us-east-1.amazonaws.com

docker push <account>.dkr.ecr.us-east-1.amazonaws.com/resume-haven:1.0.0

# Create task definition, service, and cluster via AWS Console or CLI
```

#### Google Cloud Run

```bash
# Build and push
gcloud builds submit --tag gcr.io/PROJECT/resume-haven

# Deploy
gcloud run deploy resume-haven \
  --image gcr.io/PROJECT/resume-haven:latest \
  --platform managed \
  --region us-central1 \
  --allow-unauthenticated
```

#### Azure Container Instances

```bash
# Push to ACR
az acr build --registry <name> --image resume-haven:1.0.0 .

# Deploy
az container create \
  --resource-group myResourceGroup \
  --name resume-haven-app \
  --image <name>.azurecr.io/resume-haven:1.0.0 \
  --ports 80 443 \
  --registry-login-server <name>.azurecr.io \
  --registry-username <username> \
  --registry-password <password>
```

## Production Configuration

### Environment Variables

Create `.env.production`:

```env
# Application
APP_NAME=ResumeHaven
APP_ENV=production
APP_DEBUG=false
APP_URL=https://resumehaven.com

# Security
TRUSTED_PROXIES=*
SESSION_SECURE_COOKIES=true
SESSION_SAME_SITE=Strict

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/storage/database.sqlite

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info
LOG_PATH=/var/www/html/storage/logs

# Xdebug
XDEBUG_MODE=off
```

### SSL/HTTPS Configuration

#### Self-Signed Certificate (Testing)

```bash
# Generate self-signed cert
openssl req -x509 -newkey rsa:4096 -keyout key.pem \
  -out cert.pem -days 365 -nodes

# Place in docker/certs/
mkdir -p docker/certs
mv cert.pem key.pem docker/certs/
```

#### Let's Encrypt (Production)

```bash
# Install certbot
apt-get install certbot python3-certbot-dns-cloudflare

# Obtain certificate
certbot certonly --dns-cloudflare \
  -d resumehaven.com \
  -d www.resumehaven.com

# Copy to docker/certs/
cp /etc/letsencrypt/live/resumehaven.com/fullchain.pem docker/certs/cert.pem
cp /etc/letsencrypt/live/resumehaven.com/privkey.pem docker/certs/key.pem

# Auto-renew
certbot renew --quiet --no-eff-email
```

#### Update Caddyfile

```caddyfile
{
    frankenphp
    https_port 443
}

resumehaven.com, www.resumehaven.com {
    root * /var/www/html/public
    
    # Auto HTTPS with Let's Encrypt
    tls /docker/certs/cert.pem /docker/certs/key.pem
    
    encode zstd gzip
    php_server {
        index index.php
    }
    file_server
    
    # Security headers
    header X-Content-Type-Options "nosniff"
    header X-Frame-Options "DENY"
    header Referrer-Policy "strict-origin-when-cross-origin"
}
```

## Health Checks

### Application Health Endpoint

Create `routes/api.php`:

```php
<?php
declare(strict_types=1);

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => config('app.version'),
    ]);
});

Route::get('/health/ready', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();
        
        return response()->json(['status' => 'ready']);
    } catch (\Exception $e) {
        return response()->json(
            ['status' => 'not-ready', 'error' => $e->getMessage()],
            503
        );
    }
});
```

### Health Check Monitoring

```bash
# Check health endpoint
curl -f http://resumehaven.com/health || exit 1

# Monitor with Uptime Robot, Pingdom, etc.
# or use Kubernetes probes (see Kubernetes section above)
```

## Logging and Monitoring

### Application Logging

Configure in `.env`:

```env
LOG_CHANNEL=single
LOG_LEVEL=info
LOG_PATH=/var/www/html/storage/logs
```

Log locations in container:
```
/var/www/html/storage/logs/laravel.log
```

### Access Logs

Caddy logs HTTP requests to:
```
docker logs resume-haven-app
```

### Monitoring Setup

#### Using Prometheus + Grafana

1. Add Prometheus exporter to container
2. Configure Prometheus scrape config
3. Create Grafana dashboards
4. Set up alerting rules

#### Using ELK Stack

1. Configure Filebeat to collect logs
2. Send to Elasticsearch
3. Visualize in Kibana
4. Create alerts

## Backup and Recovery

### Database Backup

```bash
# Backup SQLite database
docker-compose exec app cp storage/database.sqlite storage/backup_$(date +%Y%m%d).sqlite

# Automated daily backup
0 2 * * * docker-compose -f /path/to/docker-compose.yml exec app \
  cp storage/database.sqlite storage/backup_$(date +\%Y\%m\%d).sqlite
```

### File Backup

```bash
# Backup storage directory
tar -czf backup_$(date +%Y%m%d).tar.gz storage/

# To cloud storage
aws s3 cp backup_$(date +%Y%m%d).tar.gz s3://my-backups/
```

### Disaster Recovery

```bash
# Restore from backup
tar -xzf backup_20260201.tar.gz
docker-compose restart app
```

## Zero-Downtime Deployment

### Blue-Green Deployment

```bash
# Run new version
docker-compose -f docker-compose.blue.yml up -d

# Health check
curl http://localhost:8000/health

# Switch traffic
nginx -s reload  # or update load balancer

# Keep old version for quick rollback
docker-compose -f docker-compose.green.yml up -d
```

### Rolling Updates (Kubernetes)

```yaml
spec:
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
```

Kubernetes automatically handles rolling updates.

## Rollback Procedures

### Docker Compose Rollback

```bash
# Identify previous image
docker image ls | grep resume-haven

# Revert to previous tag
docker-compose down
docker pull your-registry/resume-haven:1.0.0
docker-compose up -d
```

### Kubernetes Rollback

```bash
# View rollout history
kubectl rollout history deployment/resume-haven

# Rollback to previous version
kubectl rollout undo deployment/resume-haven

# Rollback to specific revision
kubectl rollout undo deployment/resume-haven --to-revision=2
```

## Performance Optimization

### Caching Strategy

```env
CACHE_DRIVER=file
CACHE_TTL=3600
```

### Database Optimization

```bash
# In production container
docker-compose exec app php artisan db:seed --class=OptimizationSeeder
```

### CDN for Static Assets

```html
<!-- Use CDN for images, stylesheets, JavaScript -->
<link rel="stylesheet" href="https://cdn.resumehaven.com/css/app.css">
<img src="https://cdn.resumehaven.com/img/logo.png" alt="Logo">
```

## Monitoring and Alerts

### Key Metrics

- Application response time
- Error rate
- CPU and memory usage
- Database connections
- Disk space usage

### Alert Examples

```yaml
- alert: HighErrorRate
  expr: rate(http_requests_total{status=~"5.."}[5m]) > 0.05
  annotations:
    summary: High error rate detected

- alert: OutOfMemory
  expr: node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes < 0.1
  annotations:
    summary: Server running low on memory
```

## Security Hardening

### Network Security

```bash
# Firewall rules
ufw allow 80/tcp
ufw allow 443/tcp
ufw deny 9003/tcp  # Block Xdebug port
```

### Container Security

```dockerfile
# Run as non-root user
RUN useradd -m appuser
USER appuser

# Read-only filesystem
--read-only

# No capabilities
--cap-drop=ALL
```

### Secret Management

```bash
# Use Docker Secrets (Swarm)
echo "db_password" | docker secret create db_password -

# Or use environment variables from secure source
docker run -e "DB_PASSWORD=$(aws secretsmanager get-secret-value ...)"
```

## Troubleshooting Production Issues

### Container Won't Start

```bash
# Check logs
docker logs resume-haven-app

# Check resource limits
docker stats resume-haven-app

# Verify environment
docker inspect resume-haven-app
```

### High Memory Usage

```bash
# Monitor memory
watch -n 1 'docker stats resume-haven-app'

# Check for memory leaks
docker-compose exec app php -m | grep xdebug  # Should be empty
```

### Database Connection Issues

```bash
# Test database
docker-compose exec app sqlite3 storage/database.sqlite "SELECT 1;"

# Check connection limits
docker-compose exec app ps aux | grep -c php
```

## Maintenance Windows

### Scheduled Maintenance

```bash
# Maintenance mode
docker-compose exec app touch storage/maintenance

# Run migrations
docker-compose exec app php artisan migrate

# Clear caches
docker-compose exec app php artisan cache:clear

# Resume operation
docker-compose exec app rm storage/maintenance
```

## Next Steps

- [Development Guide](DEVELOPMENT.md) - Development practices
- [Xdebug Debugging](XDEBUG.md) - Debugging setup
- [Architecture Guide](ARCHITECTURE.md) - System design
