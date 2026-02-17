# Contributing to Resume Haven

Thank you for your interest in contributing to the Resume Haven application.  
This document outlines the guidelines and expectations for contributing to this project.

The application code lives in this repository, while the infrastructure (Docker, Traefik, mkcert, monitoring, etc.) is maintained separately:

👉 ^<https://github.com/resume-haven/resume-haven-infrastructure>

Please read this guide before submitting issues or pull requests.

---

## 🧭 Code of Conduct

All contributors are expected to:

- Be respectful and constructive  
- Communicate clearly  
- Provide helpful feedback  
- Collaborate in good faith  

Harassment, discrimination, or abusive behavior will not be tolerated.

---

## 🛠 Development Workflow

### 1. Clone the repositories

Clone the infrastructure repository first:

```bash
git clone git@github.com:resume-haven/resume-haven-infrastructure.git
```

Then clone the application repository into the expected location:

```bash
git clone git@github.com:resume-haven/resume-haven.git ../resume-haven
```

The infrastructure bootstrap script will handle the rest.

---

## 🌱 Branching Model

We follow a simple and effective branching strategy:

- `main` — stable, production-ready code  
- `develop` — integration branch for upcoming changes  
- `feature/*` — new features  
- `bugfix/*` — fixes for non-critical issues  
- `hotfix/*` — urgent fixes for production issues  

Examples:

```bash
feature/user-profile
bugfix/resume-parsing
hotfix/admin-login
```

---

## 🔧 Coding Standards

Please follow these conventions:

- PSR-12 coding style  
- Symfony best practices  
- Pimcore conventions  
- Strict typing where possible  
- Meaningful variable and method names  
- Avoid unnecessary complexity  
- Keep controllers thin and move logic into services  

Run code style checks:

```bash
vendor/bin/php-cs-fixer fix --dry-run
```

Run static analysis:

```bash
vendor/bin/phpstan analyse
```

---

## 🧪 Testing

All new features and bug fixes should include appropriate tests.

Run the test suite:

```bash
vendor/bin/phpunit
```

Types of tests:

- **Unit tests** for isolated logic  
- **Integration tests** for services and repositories  
- **Functional tests** for controllers and API endpoints  

---

## 🧩 Symfony Commands

Run Symfony commands inside the PHP container:

```bash
make shell.php
```

Then:

```bash
bin/console
```

Useful commands:

```bash
bin/console cache:clear
bin/console debug:router
bin/console doctrine:migrations:migrate
bin/console pimcore:install
```

---

## 📦 Dependencies

Install dependencies:

```bash
composer install
```

Update dependencies:

```bash
composer update
```

Please avoid introducing unnecessary packages.  
Discuss major dependency changes before submitting a PR.

---

## 🔄 Pimcore Submodule

Pimcore is included as a Git submodule.

Initialize (usually done by bootstrap):

```bash
git submodule update --init --recursive
```

Update Pimcore:

```bash
git submodule update --remote --merge
```

---

## 🐛 Reporting Issues

When reporting a bug, please include:

- A clear description  
- Steps to reproduce  
- Expected vs. actual behavior  
- Relevant logs or screenshots  
- Environment details (PHP version, OS, etc.)  

---

## 🔀 Submitting Pull Requests

Before submitting a PR:

1. Ensure your branch is up to date with `develop`
2. Run tests and ensure they pass
3. Run static analysis and code style checks
4. Write clear commit messages
5. Provide a meaningful PR description

PRs should:

- Be focused (one feature or fix per PR)
- Include tests where appropriate
- Avoid unrelated changes

---

## 📝 Commit Message Guidelines

Use clear, descriptive commit messages:

```bash
feat: add resume parsing service
fix: correct null handling in job matching
refactor: extract skill scoring logic
docs: update API documentation
test: add unit tests for resume parser
```

---

## 🤝 Thank You

Your contributions help make Resume Haven better.  
We appreciate your time, effort, and expertise.

If you have questions, feel free to open a discussion or reach out to the maintainers.
