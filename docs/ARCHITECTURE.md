# Resume Haven – Architecture Overview

This document provides a high‑level overview of the architecture of the Resume Haven application.  
It explains the major components, how they interact, and the guiding principles behind the system design.

The application is built with:

- **Symfony** (backend framework)
- **Pimcore** (CMS, asset management, data modeling)
- **PHP 8.x**
- **MariaDB** (primary relational database)
- **Valkey** (Redis-compatible cache and queue backend)
- **RabbitMQ** (asynchronous messaging)
- **Traefik** (reverse proxy, TLS termination)
- **Nginx** (internal web server)
- **Docker** (local development environment)

The infrastructure is maintained separately:

👉 https://github.com/resume-haven/resume-haven-infrastructure

---

# 🧱 Core Architectural Principles

- **Separation of concerns**  
  Application logic, infrastructure, and content management are clearly separated.

- **Modular domain design**  
  Business logic is organized into domain‑specific services and models.

- **API‑first mindset**  
  The system exposes structured endpoints for frontend and external integrations.

- **Extensibility**  
  Pimcore’s data objects and Symfony’s service container allow flexible growth.

- **Performance and caching**  
  Valkey is used for caching, sessions, and queue backends.

- **Asynchronous processing**  
  Long‑running tasks (e.g., resume parsing, job matching) run via RabbitMQ workers.

---

# 🏛 High-Level System Components

## 1. Symfony Application (`application/`)

The Symfony application contains:

- Controllers  
- Services  
- Domain models  
- Event subscribers  
- API endpoints  
- Business logic  
- Doctrine entities and repositories  

Symfony acts as the primary backend framework and orchestrates:

- Request handling  
- Routing  
- Authentication  
- Validation  
- Database access  
- Background job dispatching  
- Integration with Pimcore  

---

## 2. Pimcore (`pimcore/`)

Pimcore is included as a Git submodule and provides:

- Data object definitions  
- Asset management  
- Admin UI  
- Document management  
- Structured content modeling  
- Event hooks and workflows  

Pimcore is used for:

- Managing resumes  
- Managing job postings  
- Storing structured user data  
- Providing an admin interface for content editors  

---

## 3. Database Layer (MariaDB)

MariaDB stores:

- User accounts  
- Resume metadata  
- Job postings  
- Application records  
- Matching results  
- Pimcore object data  

Doctrine ORM is used for:

- Entities  
- Repositories  
- Migrations  
- Query abstraction  

---

## 4. Caching & Sessions (Valkey)

Valkey (Redis-compatible) is used for:

- Symfony cache  
- HTTP caching  
- Session storage  
- Rate limiting  
- Queue backend (if needed)  

This improves performance and reduces database load.

---

## 5. Messaging & Background Jobs (RabbitMQ)

RabbitMQ is used for asynchronous processing:

- Resume parsing  
- Skill extraction  
- Job matching  
- Notification dispatching  
- Long-running workflows  

Symfony Messenger integrates with RabbitMQ to handle:

- Queues  
- Retries  
- Dead-lettering  
- Worker processes  

---

## 6. Web Layer (Traefik + Nginx)

### Traefik
- Acts as the **reverse proxy**
- Terminates **HTTPS**
- Routes requests to Nginx
- Provides local TLS via mkcert
- Offers a dashboard for debugging

### Nginx
- Serves static assets
- Forwards PHP requests to PHP-FPM
- Acts as the internal web server

---

## 7. Monitoring Stack

The infrastructure includes:

- **Prometheus** (metrics collection)
- **Grafana** (dashboards)
- **Loki** (log aggregation)
- **Promtail** (log shipping)
- **Node Exporter / cAdvisor** (system metrics)

The application exposes metrics via:

- Symfony Prometheus bundle
- Custom counters, gauges, histograms

---

# 🔌 Application Flow Overview

```
Browser / Client
        |
        v
   Traefik (HTTPS)
        |
        v
      Nginx
        |
        v
   Symfony Kernel
        |
        +--> Controllers
        +--> Services
        +--> Doctrine ORM
        +--> Pimcore API
        +--> Messenger (RabbitMQ)
```

Background jobs:

```
Symfony Messenger
        |
        v
    RabbitMQ
        |
        v
   Worker Processes
```

---

# 🧩 Domain Overview (Simplified)

The core domain models include:

- **User**  
  Authentication, profile, preferences

- **Resume**  
  Uploaded files, parsed content, extracted skills

- **JobOffer**  
  Structured job postings, requirements, metadata

- **Application**  
  Links users to job offers, tracks status

- **Skill**  
  Extracted from resumes and job descriptions

- **MatchingEngine**  
  Compares resumes and job offers using scoring logic

- **Experience / Education**  
  Structured resume components

These models are implemented using a mix of:

- Doctrine entities  
- Pimcore data objects  
- Symfony services  

---

# 🧪 Testing Strategy

- **Unit tests** for isolated logic  
- **Integration tests** for services and repositories  
- **Functional tests** for controllers and API endpoints  
- **Pimcore tests** for data object behavior  

---

# 🚀 Deployment Considerations

The architecture supports:

- Containerized deployment (Docker, Kubernetes)
- Horizontal scaling of:
  - PHP-FPM workers
  - RabbitMQ workers
  - Nginx
- Externalized services:
  - Managed MariaDB
  - Managed Redis/Valkey
  - Managed RabbitMQ
- TLS termination via Traefik or cloud load balancers

---

# 🎯 Summary

Resume Haven is built as a modular, scalable, API-driven backend using Symfony and Pimcore.  
It separates concerns cleanly across:

- Application logic  
- Content management  
- Infrastructure  
- Background processing  
- Monitoring  

This architecture supports long-term growth, maintainability, and high performance.
