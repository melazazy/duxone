1. Vision & Specs (DuxOne)
Project Overview

Name: DuxOne (All-in-One Business Platform).

Target Audience: SMEs in the MENA region (Middle East & North Africa).

Business Model: SaaS (multi-tenant, subscription-based).

Strategic Goal:

Provide a modular platform (Accounting, HR, CRM, Inventory, Clinics, etc.).

Easy for non-specialists, yet compliant with local business regulations (e.g., VAT, e-invoicing).

Scalable and integration-ready for future modules.

Functional Requirements (Accounting Module Example)

Dashboard: KPIs (Revenue, Expenses, Profit, Cash Flow), alerts.

Invoicing: Create/send/customize invoices, VAT-ready, PDF/email, payment tracking.

Expenses: Track expenses, receipts, categories, approval workflows.

General Ledger: Auto/manual entries, account balances.

Bank Reconciliation: Manual/CSV import, future bank API integration.

Clients & Vendors: Centralized records, balances, history, credit limits.

Tax & Compliance: VAT calculations, tax-ready reports, localization support.

Reports: P&L, Balance Sheet, Cash Flow, Aged Receivables/Payables, export PDF/Excel.

Non-Functional Requirements

UX/UI: Clean, intuitive, RTL-ready, optimized for SMEs.

Performance: Dashboard <3s load, caching, queues, pagination.

Security: SSL/TLS, AES-256 encryption, RBAC, CSRF/XSS protection, API rate limiting.

Scalability: Multi-tenant (stancl/tenancy), Docker, horizontal scaling.

Integration: RESTful APIs (later GraphQL), webhooks, API Gateway.

Accessibility: WCAG compliance.

Extensibility: Modules interact via shared APIs.

Monitoring: Telescope, Horizon, Sentry/New Relic.

Testing: Unit + Feature + E2E, >80% coverage, CI/CD pipeline.

Deliverables

Functional specifications with acceptance criteria.

Wireframes/Mockups for main screens.

System architecture diagram (modules, DB, APIs).

Database schema (invoices, items, accounts, transactions).

API endpoints list.

Future integration plan with other modules.

2. Engineering Guidelines (Unified Context V2)
Development Philosophy (Gear Protocol)

Think → Plan → Act → Verify.

Safe Edits: small, explained, incremental.

Follow KISS & DRY.

Version this document in Git like code.

Back-End (Laravel/PHP)

Standards: PSR (1,4,12), clear naming.

Structure: Slim controllers, Services/Actions, Repository Pattern, Traits/Helpers.

Design: SOLID, suitable Design Patterns, avoid Anemic Domain Models.

Database: Migrations + Seeders, ≥3NF, snake_case, indexes, repositories.

Performance: Redis cache, queues, pagination.

Security: CSRF/XSS, Form Requests, Policies/Gates, rate limiting, encryption.

Testing: Unit + Feature tests (Pest).

Docs: PHPDoc, Swagger/OpenAPI, structured README.

Front-End (Livewire + Tailwind + JS/TS)

Standards: HTML5, CSS3, ECMAScript, no inline CSS/JS.

Structure: Component-based, Atomic Design for scale.

CSS/Styling: TailwindCSS, responsive, mixins/variables.

JavaScript: Master Vanilla JS, modular ES6, SOLID components, TypeScript/JSDoc, Alpine.js/Redux for state.

Performance: Minification, Tree-shaking, Code Splitting, Lazy Loading, optimized images.

Accessibility: Semantic HTML, alt text, ARIA, keyboard nav.

UX/UI: Consistency, clear navigation, feedback.

Security: Client validation, secure API handling.

Testing: Jest/RTL for components, Cypress/Playwright E2E, ESLint + Prettier.

Docs: README, Storybook/Styleguide if needed.

Collaboration & DevOps

Code Review: mandatory for every PR.

Git Workflow: branch → PR → review → merge.

CI/CD: GitHub Actions (tests + lint + deploy).

Environment Variables: .env for configs.

Logging/Monitoring: Telescope, Horizon (dev); Sentry/New Relic (prod).

Backup/Logs: spatie/backup + spatie/activitylog.

Docs: README, Wiki, project guides.

Expected Deliverables (Any Module)

Clean, structured code.

Unit + Feature + E2E tests.

Documentation (README, comments, guides).

Responsive UI.

Scalable design.

Monitoring & logging ready.

3. Project Setup & Tools
Core Tech

Framework: Laravel 12 (Livewire + Tailwind).

Database: MySQL (PostgreSQL-ready).

Multi-tenancy: stancl/tenancy.

Billing: Laravel Cashier.

Admin Panel: Filament.

Caching/Queues: Redis + Horizon.

Debugging/Monitoring: Telescope, Debugbar, Sentry.

Backup/Logs: spatie/backup + spatie/activitylog.

Dev Environment

Dockerized setup with docker-compose.yml.

install.sh for initializing project & packages.

verify.sh for checking package installation.

Access (Local)

App: http://localhost:8081

PhpMyAdmin: http://localhost:8080

Vite/Node: http://localhost:5173

GitHub Workflow

Branching: main (stable), dev (integration), feature/*.

PR template + Issue template.

GitHub Actions for testing + linting + deployment.