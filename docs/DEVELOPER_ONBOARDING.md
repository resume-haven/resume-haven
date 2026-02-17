# Developer Onboarding Guide  
Welcome to the Resume Haven development team!  
This guide walks you through the complete setup process so you can start contributing quickly and confidently.

Resume Haven consists of two repositories:

- **Application Code**  
  https://github.com/resume-haven/resume-haven  
- **Infrastructure (Docker, Traefik, mkcert, Monitoring)**  
  https://github.com/resume-haven/resume-haven-infrastructure  

This document explains how to set up your local environment, run the application, and work effectively with the project.

---

# 🧰 1. Prerequisites

Before you begin, install the following tools:

### Required
- **Docker Desktop**
- **Git**
- **Make**
- **mkcert** (for local HTTPS certificates)
- **SSH key** configured for GitHub access

### Optional but recommended
- PHPStorm or VS Code
- HTTP client (Insomnia, Postman)
- TablePlus / DBeaver for database access

---

# 📦 2. Clone the Repositories

Clone the infrastructure repository first:

```
git clone git@github.com:resume-haven/resume-haven-infrastructure.git
```

Then clone the application repository into the expected directory:

```
git clone git@github.com:resume-haven/resume-haven.git ../resume-haven
```

Your folder structure should look like:

```
workspace/
├── resume-haven-infrastructure/
└── resume-haven/
```

---

# 🚀 3. Bootstrap the Environment

Inside the **infrastructure repository**, run:

```
make bootstrap
```

This command will:

1. Verify the application repository exists  
2. Initialize the Pimcore submodule  
3. Start all Docker containers  
4. Install Composer dependencies for:
   - `/var/www/application`
   - `/var/www/pimcore`
5. Generate local HTTPS certificates via mkcert  
6. Start the monitoring stack (Prometheus, Grafana, Loki)

After bootstrap completes, your environment is fully operational.

---

# 🌐 4. Accessing the Application

Once everything is running, you can access:

| Service | URL |
|--------|-----|
| Application | https://localhost |
| Pimcore Admin | https://localhost/admin |
| Traefik Dashboard | http://localhost:8081 |
| Grafana | http://localhost:3001 |
| Prometheus | http://localhost:9090 |
| Loki | http://localhost:3100 |

---

# 🐳 5. Working with Docker (Infrastructure)

Start all containers:

```
make infra.up
```

Stop all containers:

```
make infra.down
```

Restart:

```
make infra.restart
```

View logs:

```
make infra.logs
```

Cleanup unused Docker resources:

```
make infra.prune
```

---

# 🧩 6. Working with the Application (Symfony + Pimcore)

### Install dependencies (if needed)

```
make app.install
```

### Clear cache

```
make app.cache-clear
```

### Run database migrations

```
make app.migrate
```

### Open a shell inside the PHP container

```
make shell.php
```

Inside the container, you can run:

```
bin/console
```

---

# 🗄 7. Database Access

Open a shell inside the MariaDB container:

```
make shell.db
```

Or connect via a GUI tool using:

- Host: `localhost`
- Port: `3306`
- User: `root`
- Password: (see infrastructure `.env`)

---

# 🧪 8. Running Tests

Inside the PHP container:

```
vendor/bin/phpunit
```

Static analysis:

```
vendor/bin/phpstan analyse
```

Coding standards:

```
vendor/bin/php-cs-fixer fix --dry-run
```

---

# 🔄 9. Updating Pimcore Submodule

To update Pimcore to the latest version:

```
git submodule update --remote --merge
```

To initialize (normally done by bootstrap):

```
git submodule update --init --recursive
```

---

# 🔐 10. HTTPS Certificates (mkcert)

Certificates are generated automatically during bootstrap.

To regenerate manually:

```
make certs.generate
```

Certificates are stored under:

```
resume-haven-infrastructure/docker/traefik/certs/
```

---

# 🧭 11. Project Structure Overview

### Application Repository (`resume-haven/`)

```
application/        # Symfony application
pimcore/            # Pimcore (submodule)
.env.example        # Environment template
```

### Infrastructure Repository (`resume-haven-infrastructure/`)

```
docker/             # All service definitions
docker-compose.yml
docker-compose.monitoring.yml
bootstrap.sh
Makefile
README.md
```

---

# 🤝 12. Contributing

Please read:

- `CONTRIBUTING.md`
- `ARCHITECTURE.md`
- `API.md`

Before submitting a pull request.

---

# 🎉 13. You're Ready to Develop!

You now have a fully functional local environment.  
If you run into issues, check:

- Docker logs  
- Traefik dashboard  
- Symfony logs (`var/log/`)  
- Monitoring dashboards  

Welcome to the team — happy coding!
