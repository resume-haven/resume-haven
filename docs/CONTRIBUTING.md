# Contributing to ResumeHaven

Thank you for your interest in ResumeHaven!
This document describes the rules and expectations for contributions to the project.

---

# 🎯 Project philosophy

ResumeHaven is a deliberately **minimalist**, **rules-based** analysis tool.
The MVP should:

- clearly structured
- easy to understand
- without unnecessary complexity
- without AI
- without database
- without user accounts

be.

Please adhere to these principles when posting.

---

# 🧱 Architecture principles

- The analysis is carried out via the **AnalysisEngine**.
- The engine consists of:
  - JobExtractor
  - ResumeExtractor
  - Matchers
  - Taggers
- The engine returns a `AnalysisResult`.
- Controllers are thin and contain no logic.
- Views are minimalist and use TailwindCSS.
- No storage of user data.

---

# 🛠️ Development environment

ResumeHaven uses Docker:

- php-fpm (PHP 8.5)
- nginx
- node (Tailwind build)
- email pit

Start the environment:

```bash
docker-compose up --build
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
php artisan test
```

New features must be covered with tests.

---

# 📦 Pull Requests

Please note:

- PRs need to be small and focused
- Commit messages clear and descriptive
- No new dependencies without discussion
- No AI features without explicit approval
- No database introduction

---

# 📚 Documentation

All architecture and concept documents are in the repo:

`resume-haven-ideas/`

Please keep the documentation up to date if