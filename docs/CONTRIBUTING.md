# Contributing to ResumeHaven

Thank you for your interest in ResumeHaven!
This document describes the rules and expectations for contributions to the project.

---

# 🎯 Project philosophy

ResumeHaven is a deliberately **structured**, **quality-driven** AI-assisted resume analysis tool.
The MVP is designed to be:

- clearly structured
- easy to understand
- without unnecessary complexity
- backed by SOLID & DDD principles
- thoroughly tested (min. 99% coverage)

Please adhere to these principles when contributing.

---

# 🧱 Architecture principles

- The analysis is carried out via the **AnalysisEngine** with AI support.
- The engine consists of:
  - JobExtractor
  - ResumeExtractor
  - Matchers
  - Taggers
  - AI Analyzer (Gemini / Mock)
- The engine returns an `AnalysisResult`.
- Controllers are thin and contain no business logic (Single Action Controllers).
- Views are minimalist and use TailwindCSS.
- CQRS pattern (Commands/Queries strictly separated).
- Repository pattern (no raw SQL outside repositories).

---

# 🛠️ Development environment

ResumeHaven uses Docker:

- php-fpm (PHP 8.5)
- nginx
- node (Tailwind build)
- Mailpit (test mailbox)

Start the environment:

```bash
docker compose up --build
```

Install Laravel:

```bash
docker exec -it php bash
composer install
cp .env.example .env
php artisan key:generate
```

Start Tailwind:

```bash
docker exec -it node bash
npm install
npm run dev
```

---

# 🧪 Tests

Please ensure that all tests run successfully:

```bash
make test
```

New features must be covered with tests (minimum 99% coverage).

Run full quality gate before submitting a PR:

```bash
make test
make phpstan
make pint-fix
```

---

# 📦 Pull Requests

Please note:

- PRs need to be small and focused
- Commit messages clear and descriptive
- No new dependencies without discussion
- All changes must pass CI (Pint + PHPStan Level 9 + Tests + Coverage ≥ 99%)
- Follow the [Coding Guidelines](CODING_GUIDELINES.md)

---

# 📚 Documentation

All architecture and concept documents are in `docs/`.

Please keep the documentation up to date when making significant changes.
