# Project Structure and Layers

## Project Structure

```
resume-haven/
├── app/                          # Application source code
│   ├── Domain/                   # Domain layer (entities, value objects)
│   ├── Application/              # Application layer (use cases, handlers)
│   ├── Infrastructure/           # Infrastructure layer (persistence, adapters)
│   ├── Http/                     # UI layer (controllers, middleware, requests)
│   └── ...
├── public/                       # Web-accessible files
│   ├── index.php                 # Application entry point
│   ├── css/                      # Stylesheets
│   ├── js/                       # JavaScript
│   └── assets/                   # Images, fonts, etc.
├── bootstrap/                    # Application initialization
│   └── app.php                   # Bootstrap configuration
├── storage/                      # Runtime data
│   ├── logs/                     # Application logs
│   ├── cache/                    # Temporary cache
│   └── exports/                  # Generated exports
├── database/                     # Data layer
│   ├── migrations/               # Schema changes
│   ├── factories/                # Model factories
│   └── seeders/                  # Sample data
├── routes/                       # Route definitions
│   ├── api.php                   # API routes (stateless)
│   └── web.php                   # Web routes (CSRF protected)
├── tests/                        # Test suites
│   ├── Unit/                     # Unit tests
│   ├── Feature/                  # Integration tests
│   └── ...
├── docker/                       # Docker configuration
│   ├── FrankenPHP/
│   │   ├── Dockerfile
│   │   ├── Caddyfile
│   │   └── xdebug.ini
│   └── entrypoint.sh
├── docs/                         # Documentation
├── docker-compose.yml            # Multi-container orchestration
├── .env.docker                   # Environment template
└── README.md                     # Project documentation
```

Current UI layer lives in `app/Http` (controllers, middleware, requests). A separate Vue.js frontend is planned for a later phase.

## Layered Architecture

```
┌─────────────────────┐
│   Presentation      │  Controllers, API responses
├─────────────────────┤
│   Application       │  Services, use cases
├─────────────────────┤
│   Domain            │  Models, business logic
├─────────────────────┤
│   Infrastructure    │  Database, external APIs
└─────────────────────┘
```

### Layer Responsibilities

**Presentation Layer**
- HTTP request/response handling
- Input validation
- Response formatting

**Application Layer**
- Orchestration of services
- Transaction management
- Cross-cutting concerns

**Domain Layer**
- Business rules
- Entity models
- Domain logic

**Infrastructure Layer**
- Database access
- External service calls
- File I/O operations
