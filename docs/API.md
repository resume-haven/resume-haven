# Resume Haven – REST API Documentation

This document provides an overview and template for documenting the Resume Haven REST API.  
It defines conventions, authentication, versioning, response formats, and example endpoints.

The API is designed to be:

- RESTful  
- JSON-based  
- Versioned  
- Secure  
- Consistent across all domains  

---

# 📌 1. Base URL

All API endpoints are served under:

```
https://localhost/api/v1/
```

Production environments will use a different base URL.

---

# 🔐 2. Authentication

The API uses **JWT-based authentication** (or session-based auth depending on environment).

### Authentication Header

```
Authorization: Bearer <token>
```

### Login Endpoint (example)

```
POST /api/v1/auth/login
```

Request:

```json
{
  "email": "user@example.com",
  "password": "secret"
}
```

Response:

```json
{
  "token": "<jwt-token>",
  "expires_in": 3600
}
```

---

# 🧱 3. API Versioning

The API is versioned via the URL:

```
/api/v1/...
```

Future versions:

```
/api/v2/...
```

Breaking changes always require a new version.

---

# 📦 4. Content Type

All requests and responses use:

```
Content-Type: application/json
Accept: application/json
```

---

# 📘 5. Response Format

All API responses follow a consistent structure.

### Success Response

```json
{
  "success": true,
  "data": { ... },
  "meta": {
    "timestamp": "2026-02-17T18:00:00Z"
  }
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Email is required",
    "details": { ... }
  }
}
```

---

# 🧪 6. Pagination

Endpoints returning lists use cursor-based or offset-based pagination.

### Example

```
GET /api/v1/jobs?page=2&limit=20
```

Response:

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "page": 2,
    "limit": 20,
    "total": 134
  }
}
```

---

# 🧩 7. Domain Endpoints (Template)

Below is a template for documenting each domain.

---

## 📂 7.1 User Endpoints

### Create User

```
POST /api/v1/users
```

Request:

```json
{
  "email": "user@example.com",
  "password": "secret",
  "name": "John Doe"
}
```

Response:

```json
{
  "success": true,
  "data": {
    "id": 123,
    "email": "user@example.com",
    "name": "John Doe"
  }
}
```

---

### Get Current User

```
GET /api/v1/users/me
```

Response:

```json
{
  "success": true,
  "data": {
    "id": 123,
    "email": "user@example.com",
    "name": "John Doe"
  }
}
```

---

## 📄 7.2 Resume Endpoints

### Upload Resume

```
POST /api/v1/resumes
```

Multipart request:

```
file: resume.pdf
```

Response:

```json
{
  "success": true,
  "data": {
    "id": 42,
    "status": "processing"
  }
}
```

---

### Get Resume Details

```
GET /api/v1/resumes/{id}
```

Response:

```json
{
  "success": true,
  "data": {
    "id": 42,
    "skills": ["Java", "Symfony"],
    "experience": [ ... ],
    "education": [ ... ]
  }
}
```

---

## 💼 7.3 Job Endpoints

### List Jobs

```
GET /api/v1/jobs
```

### Get Job Details

```
GET /api/v1/jobs/{id}
```

---

## 🎯 7.4 Matching Endpoints

### Trigger Matching

```
POST /api/v1/matching
```

Request:

```json
{
  "resume_id": 42
}
```

Response:

```json
{
  "success": true,
  "data": {
    "matches": [
      { "job_id": 10, "score": 87 },
      { "job_id": 12, "score": 74 }
    ]
  }
}
```

---

# 🧵 8. Error Codes

| Code                | Meaning                          |
|---------------------|----------------------------------|
| `VALIDATION_ERROR`  | Invalid input data               |
| `UNAUTHORIZED`      | Missing or invalid token         |
| `NOT_FOUND`         | Resource not found               |
| `SERVER_ERROR`      | Unexpected server error          |
| `RATE_LIMITED`      | Too many requests                |

---

# 🧪 9. Testing the API

Use tools like:

- Postman  
- Insomnia  
- curl  
- Symfony Panther (for functional tests)  

Example curl request:

```
curl -X GET https://localhost/api/v1/jobs \
  -H "Authorization: Bearer <token>"
```

---

# 🧭 10. Future Extensions

- Webhooks  
- GraphQL layer  
- API keys for partners  
- Rate limiting per user  
- OpenAPI/Swagger documentation  

---

# 🎉 Summary

This document defines the structure and conventions for the Resume Haven REST API.  
It serves as a template for documenting existing and future endpoints in a consistent and maintainable way.
