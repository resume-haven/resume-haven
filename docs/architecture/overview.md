# Overview

ResumeHaven is a modern PHP application built with strict type safety and containerized development. This document outlines the project structure, design decisions, and how components interact.

## Architecture Diagrams

### Layered Architecture

```mermaid
flowchart TD
    UI[UI Layer\napp/Http] --> APP[Application Layer\napp/Application]
    APP --> DOMAIN[Domain Layer\napp/Domain]
    INFRA[Infrastructure Layer\napp/Infrastructure] --> DOMAIN
    APP --> INFRA
    UI --> INFRA
```

### CQRS Flow

```mermaid
sequenceDiagram
    participant C as Controller (UI)
    participant H as Handler (Application)
    participant QS as Query Service (Application)
    participant CS as Command Service (Application)
    participant R as Repository (Infrastructure)
    participant RR as Read Repository (Infrastructure)
    participant D as Domain

    C->>QS: Read request
    QS->>RR: Read (queries)
    RR-->>QS: Read model
    QS-->>C: Response DTO
    C->>CS: Command request
    CS->>H: Handle command
    H->>D: Invoke domain logic
    H->>R: Persist (commands)
    R-->>H: Domain entity
    H-->>CS: Domain entity
    CS-->>C: Response DTO/Result
```
