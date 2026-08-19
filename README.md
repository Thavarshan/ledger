# Ledger

Ledger is a privacy-conscious personal finance ledger for tracking accounts and transactions with exact minor-unit monetary values.

It is built with Laravel, Inertia, React, TypeScript, Tailwind CSS, and PostgreSQL. The application keeps account ownership and authorization explicit, uses context-specific data contracts, and is designed to deploy to Laravel Cloud.

## Features

- Account and transaction management with ownership isolation
- Account filtering, search, sorting, and pagination
- Exact currency-aware money conversion and formatting
- Secure handling of sensitive account identifiers
- Password authentication, email verification, two-factor authentication, and passkeys through Laravel Fortify
- PostgreSQL finance test coverage alongside a fast SQLite test suite
- Consolidated GitHub Actions CI with PHP, frontend, security, and build gates

## Requirements

- PHP 8.5+
- Composer 2+
- Node.js 22+
- PostgreSQL 17 for production and finance integration tests

SQLite is supported for the default test suite only. Laravel Cloud deployments should use PostgreSQL.

## Local development

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
composer run dev
```

The development server starts the Laravel application, queue worker, log viewer, and Vite development server together.

## Validation

```bash
composer ci:check
npm run test:frontend
npm run build
composer test:finance:postgres
```

The PostgreSQL finance tests require Docker. The primary `CI` workflow runs these checks with PostgreSQL as a service and exposes a final `CI / Deployment gate` status.

## Laravel Cloud deployment

Connect this repository to Laravel Cloud, configure a PostgreSQL database, and enable push-to-deploy for `main`. Protect `main` with the required `CI / Deployment gate` check so only validated changes reach production.

Keep application secrets and environment variables in Laravel Cloud. Do not commit `.env` files or credentials.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening an issue or pull request. By participating, you agree to follow the project [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

Please report vulnerabilities privately as described in [SECURITY.md](SECURITY.md). Do not disclose security issues in public issues or discussions.

## License

Ledger is open source software licensed under the [MIT license](LICENSE).
