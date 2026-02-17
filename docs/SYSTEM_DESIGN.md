# Resume Haven – System Design (Deep Technical Overview)

This document provides a deep technical overview of the Resume Haven backend system.  
It describes the internal architecture, data flow, domain boundaries, integration points, and the rationale behind key design decisions.

The goal is to give developers and architects a clear understanding of how the system works under the hood and how its components interact.

---

# 🧱 1. Architectural Style

Resume Haven follows a **modular, service‑oriented backend architecture** built on:

- **Symfony** as the application framework  
- **Pimcore** as the content and data management layer  
- **Domain‑driven design (DDD) principles**  
- **Event‑driven communication** for asynchronous workflows  
- **RESTful APIs** for external and internal clients  
- **Message queues** for long‑running tasks  
- **Caching and distributed state** via Valkey  

The system is designed for:

- Scalability  
- Maintainability  
- Extensibility  
- Clear separation of concerns  

---

# 🧩 2. High-Level Component Overview

```
+-------------------------------------------------------------+
|                         Traefik                             |
|                 (Reverse Proxy + TLS)                       |
+-------------------------------+-----------------------------+
                                |
                                v
+-------------------------------------------------------------+
|                           Nginx                             |
|                 (Static assets + PHP-FPM proxy)             |
+-------------------------------+-----------------------------+
                                |
                                v
+-------------------------------------------------------------+
|                        Symfony Kernel                       |
|-------------------------------------------------------------|
| Controllers | Services | Domain Logic | Repositories | API  |
+-------------------------------------------------------------+
                                |
                                v
+-------------------------------------------------------------+
|                          Pimcore                            |
|   Data Objects | Assets | Admin UI | Workflows | Documents  |
+-------------------------------------------------------------+
                                |
                                v
+-------------------------------------------------------------+
| MariaDB | Valkey | RabbitMQ | Filesystem | Monitoring Stack |
+-------------------------------------------------------------+
```

---

# 🧠 3. Domain Architecture

The system is divided into **domain modules**, each encapsulating its own logic:

### Core Domains

- **User Domain**  
  Authentication, profiles, preferences

- **Resume Domain**  
  Uploading, parsing, skill extraction, structured resume data

- **Job Domain**  
  Job postings, requirements, metadata

- **Matching Domain**  
  Resume-to-job matching, scoring algorithms, ranking

- **Application Domain**  
  User job applications, status tracking, workflow transitions

### Supporting Domains

- **Notification Domain**  
  Email, in-app notifications, async dispatch

- **Search Domain**  
  Full-text search, indexing, filters

- **Analytics Domain**  
  Metrics, events, dashboards

Each domain is implemented using:

- Symfony services  
- Doctrine entities  
- Pimcore data objects  
- Domain events  
- Message handlers  

---

# 🔄 4. Request Lifecycle

A typical request flows through the system as follows:

```
Client
  |
  v
Traefik (TLS termination)
  |
  v
Nginx (static assets + PHP-FPM proxy)
  |
  v
Symfony Kernel
  |
  +--> Routing
  +--> Controller
  +--> Domain Services
  +--> Repositories
  +--> Pimcore API
  +--> Response
```

Caching layers:

- HTTP cache (Symfony)
- Application cache (Valkey)
- Doctrine query cache
- Pimcore object cache

---

# 📨 5. Asynchronous Processing (RabbitMQ + Symfony Messenger)

Long-running tasks are offloaded to message queues:

### Typical async tasks:

- Resume parsing  
- Skill extraction  
- Job matching  
- Notification sending  
- Data enrichment  
- Import/export workflows  

### Flow:

```
Symfony Service
      |
      v
Messenger Bus
      |
      v
RabbitMQ Exchange
      |
      v
Queue
      |
      v
Worker Process
```

Workers run continuously and scale horizontally.

---

# 🗄 6. Data Storage Design

### MariaDB (Primary Database)

Stores:

- Users  
- Job postings  
- Applications  
- Matching results  
- Resume metadata  
- Pimcore object references  

### Pimcore Data Objects

Used for:

- Structured resume data  
- Job definitions  
- Taxonomies (skills, industries, roles)  
- Assets (PDFs, images, documents)  

### Valkey (Redis-Compatible)

Used for:

- Caching  
- Sessions  
- Rate limiting  
- Queue backend (optional)  
- Locking mechanisms  

### Filesystem

Stores:

- Uploaded resumes  
- Generated previews  
- Temporary parsing artifacts  

---

# 🧮 7. Matching Engine (Technical Overview)

The matching engine compares resumes and job postings using:

### 1. **Skill Vectorization**
- Extracted skills → normalized → weighted  
- Stored as vectors for fast comparison  

### 2. **Scoring Algorithm**
Weighted scoring based on:

- Skill overlap  
- Experience relevance  
- Education match  
- Seniority level  
- Keyword density  
- Custom business rules  

### 3. **Ranking**
- Normalized score (0–100)  
- Sorted descending  
- Threshold-based filtering  

### 4. **Asynchronous Execution**
Matching is executed via RabbitMQ workers to avoid blocking requests.

---

# 🧠 8. Resume Parsing Pipeline

```
Upload
  |
  v
File stored in filesystem
  |
  v
Message dispatched to queue
  |
  v
Worker:
  - Extract text
  - Identify sections
  - Extract skills
  - Normalize data
  - Store structured resume object
  |
  v
Matching triggered (optional)
```

Technologies involved:

- PDF text extraction  
- NLP-based skill extraction  
- Regex-based section detection  
- Pimcore object creation  

---

# 🔐 9. Security Architecture

### Authentication

- Symfony Security Component  
- JWT or session-based auth  
- Password hashing (argon2id)  

### Authorization

- Role-based access control  
- Pimcore permission system  
- Custom voters for domain rules  

### Input Validation

- Symfony Validator  
- Request DTOs  
- Strict type enforcement  

### Hardening

- HTTPS enforced via Traefik  
- Security headers  
- Rate limiting via Valkey  
- CSRF protection (admin UI)  

---

# 📊 10. Observability & Monitoring

### Metrics (Prometheus)

- Request latency  
- Worker throughput  
- Queue sizes  
- Cache hit/miss ratio  
- Database query performance  

### Logs (Loki)

- Structured JSON logs  
- Correlation IDs  
- Error tracking  

### Dashboards (Grafana)

- Application performance  
- Worker health  
- Database metrics  
- Traffic overview  

---

# 🧩 11. Integration Points

### External Integrations (future-ready)

- Job board APIs  
- Resume import APIs  
- Notification providers  
- AI-based skill extraction services  

### Internal Integrations

- Pimcore admin UI  
- Symfony controllers  
- Domain services  
- Message handlers  

---

# 🚀 12. Scalability Strategy

### Horizontal scaling:

- PHP-FPM workers  
- RabbitMQ workers  
- Nginx  
- Traefik  

### Vertical scaling:

- MariaDB  
- Valkey  

### Stateless design:

- All state stored in DB, Valkey, or Pimcore  
- PHP containers remain stateless  

### Caching:

- Aggressive caching of expensive operations  
- Precomputed matching results  

---

# 🧭 13. Future Extensions

- AI-based resume parsing  
- Semantic skill matching  
- Multi-language support  
- Microservice extraction (matching engine)  
- Event sourcing for auditability  
- Graph-based skill ontology  

---

# 🎯 Summary

Resume Haven is a modular, scalable, event-driven backend built on Symfony and Pimcore.  
It uses asynchronous processing, caching, structured data modeling, and a clear domain architecture to deliver a robust and extensible platform for resume management and job matching.

This document serves as a technical foundation for developers and architects working on the system.
