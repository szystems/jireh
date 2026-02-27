# Jireh - Enterprise Resource Planning (ERP) & POS System

[![Laravel](https://img.shields.io/badge/Laravel-8.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-7.3%2B%7C8.0%2B-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Jireh** is a comprehensive Enterprise Resource Planning (ERP) system with a built-in Point of Sale (POS) module, designed and built with **Laravel 8** for small and medium businesses. It processes **high-volume transactions** and provides real-time inventory tracking, sales reporting, and workforce management.

## Key Features

- **Inventory Management** — Full stock control with real-time tracking, low-stock alerts, and product categorization.
- **Point of Sale (POS)** — Complete invoicing and sales workflow with client management and payment tracking.
- **Workforce Management** — Employee records, commission tracking, and payroll calculations.
- **Smart Quotations** — Automatic status management and quote regeneration for streamlined sales cycles.
- **Reporting & Analytics** — Real-time business intelligence dashboards built with DataTables for data-driven decision making.
- **PDF Document Generation** — Automated invoice and report generation using DomPDF.
- **Role-Based Access Control** — Granular user permissions ensuring secure data access across departments.

## Technical Architecture

### Tech Stack
| Layer | Technology |
|---|---|
| **Backend** | PHP 8.0+, Laravel 8 (Eloquent ORM, Jobs, Queues) |
| **Database** | MySQL 5.7+ (Relational Design, Query Optimization) |
| **Frontend** | Blade Templates, Bootstrap 5, jQuery, DataTables |
| **PDF Engine** | DomPDF |
| **Authentication** | Laravel Sanctum + Laravel UI |

### Architecture Highlights
- **Modular Design** — Cleanly separated modules for Inventory, Sales, HR, and Finance, following SOLID principles.
- **Optimized Queries** — Eager loading (`with()`) and query optimization to prevent N+1 problems in high-transaction environments.
- **Data Validation** — Multi-layer validation (Form Requests + database constraints) ensuring 100% data integrity.
- **Smart Decimal Handling** — Unit-aware validation system that dynamically adjusts input rules based on product unit type (integer vs. decimal).
- **Responsive UI** — Mobile-optimized tables and navigation for field use.

### Database Schema
The system uses **54+ database migrations** with a normalized relational schema covering:
- Products, Categories, and Stock movements
- Clients, Invoices, and Payment tracking
- Employees, Commissions, and Payroll
- Quotations with automatic status lifecycle
- Full audit logging (Bitácoras)

## Getting Started

### Requirements
- PHP 7.3+ (8.0+ recommended)
- Composer 2.0+
- Node.js 14+
- MySQL 5.7+ or MariaDB 10.3+

### Installation
```bash
git clone https://github.com/szystems/jireh.git
cd jireh
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## Business Impact

- Processes **$1M+ in annual transactions** for retail clients.
- Increased inventory turnover efficiency by **20%** via real-time stock synchronization.
- Reduced manual data entry errors through automated validation and smart form controls.

## Documentation

Full documentation is available in the `docs/` directory:
- [CHANGELOG](docs/CHANGELOG.md) — Version history
- [PROJECT STATUS](docs/PROJECT_STATUS.md) — Current development status
- [PRD](docs/core/PRD.md) — Product Requirements Document

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.

---

**Built by [Otto Szarata](https://github.com/szystems)** — Senior Full-Stack Developer | Victoria, BC, Canada
