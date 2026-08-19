# Contributing to Ledger

Thanks for helping improve Ledger.

## Before you start

- Search existing issues and pull requests before opening a new one.
- For security vulnerabilities, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.
- Keep changes focused and preserve existing user-visible behavior unless the change explicitly requires otherwise.

## Development workflow

1. Fork the repository and create a focused branch from `main`.
2. Install the PHP and Node.js dependencies from the README.
3. Make the smallest change that solves the problem.
4. Add or update PHPUnit and frontend tests for behavior you change.
5. Run `composer ci:check`, `npm run test:frontend`, and `npm run build`.
6. Open a pull request using the project template and describe trade-offs or migration steps.

## Style and architecture

- Follow the existing Laravel, React, TypeScript, and Tailwind conventions.
- Prefer simple, explicit code over speculative abstractions.
- Do not add repositories, generic CRUD layers, or broad framework wrappers without a concrete use case.
- Never commit secrets, production data, `.env` files, generated build output, or dependency directories.

## Pull requests

Pull requests should include a concise problem statement, implementation summary, test evidence, and any deployment or migration notes. Maintainers may request changes to keep the application secure, small, and consistent.
