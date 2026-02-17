# Resume Haven – Application

This repository contains the application code for the Resume Haven platform.  
It includes:

- Symfony (Backend Framework)
- Pimcore (CMS / Data Platform)
- Custom domain logic and services
- API endpoints
- Application models, controllers, and business logic

The infrastructure (Docker, Traefik, mkcert, monitoring, etc.) is located in a separate repository:

👉 <https://github.com/resume-haven/resume-haven-infrastructure>

---

## 📁 Project Structure

```bash
resume-haven/
├── application/        # Symfony application
│   ├── config/
│   ├── public/
│   ├── src/
│   ├── templates/
│   ├── translations/
│   └── ...
├── pimcore/            # Pimcore (as Git submodule)
│   ├── bundles/
│   ├── config/
│   ├── public/
│   └── ...
└── .env.example        # Environment variable template
```

---

## 🚀 Getting Started

The application is designed to run **inside the infrastructure repository**.  
You do **not** run this repository standalone.

To start the full environment:

1. Clone the infrastructure repository  
2. Run the bootstrap script  
3. Access the application via Traefik

Full instructions:

👉 <https://github.com/resume-haven/resume-haven-infrastructure>

---

## 🔧 Symfony Commands

Run Symfony commands inside the PHP container:

```bash
make shell.php
```

Then:

```bash
bin/console
```

Common commands:

```bash
bin/console cache:clear
bin/console debug:router
bin/console doctrine:migrations:migrate
bin/console pimcore:install
```

---

## 🧩 Environment Variables

Copy the example file:

```bash
cp .env.example .env
```

Environment variables are normally injected by the infrastructure layer.  
Local `.env` values are only used for development convenience.

---

## 🧪 Testing

Tests are executed inside the PHP container.

Run PHPUnit:

```bash
vendor/bin/phpunit
```

Run static analysis (if configured):

```bash
vendor/bin/phpstan analyse
```

Run coding standards:

```bash
vendor/bin/php-cs-fixer fix --dry-run
```

---

## 🧱 Coding Standards

The project follows:

- PSR-12 coding style
- Symfony best practices
- Pimcore conventions
- Strict type declarations where possible

---

## 🔄 Pimcore Submodule

Pimcore is included as a Git submodule.

To update:

```bash
git submodule update --remote --merge
```

To initialize (normally done by bootstrap):

```bash
git submodule update --init --recursive
```

---

## 📦 Composer

Install dependencies:

```bash
composer install
```

Update dependencies:

```bash
composer update
```

---

## 🧭 Contributing

1. Create a feature branch  
2. Follow coding standards  
3. Add tests where appropriate  
4. Submit a pull request  

---

## 📄 License

This project is proprietary and not open for public redistribution.

---

## 🎉 Done

Your application is ready to run inside the Resume Haven infrastructure.
