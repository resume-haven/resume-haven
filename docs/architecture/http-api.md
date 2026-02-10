# HTTP API

## Endpoints

All JSON endpoints are registered in `routes/api.php`.

**Resumes**
- `GET /api/resumes/{id}` - Fetch resume read model
- `POST /api/resumes` - Create resume

**Users**
- `GET /api/users/{id}` - Fetch user read model
- `POST /api/users` - Create user

## Response Samples

Response samples are documented in the controller flow sections:

- Command flow responses (201 + validation errors)
- Query flow responses (200 + 404)
