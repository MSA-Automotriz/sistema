# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### PHP/Laravel Commands
- **Development server**: `composer dev` (runs server, queue, logs, and vite concurrently)
- **Individual services**:
  - PHP server: `php artisan serve`
  - Queue worker: `php artisan queue:listen --tries=1`
  - Logs: `php artisan pail --timeout=0`
- **Testing**: `php artisan test` or `vendor/bin/phpunit`
- **Code style**: `vendor/bin/pint` (Laravel Pint for PHP)
- **Database**: 
  - Migrations: `php artisan migrate`
  - Seed: `php artisan db:seed`
  - Fresh: `php artisan migrate:fresh --seed`

### Frontend Commands
- **Development**: `npm run dev` (Vite development server)
- **Build**: `npm run build` (production build)
- **Dependencies**: `npm install`

## Architecture Overview

This is a Laravel application with a comprehensive business management system focusing on automotive parts/services, maintenance, sales, and inventory management.

### Core Modules
- **Ventas (Sales)**: Quote management, opportunities, POS system, payments, documents
- **Mantenimiento (Maintenance)**: Appointments, work orders, vehicle maintenance tracking
- **Compras (Purchasing)**: Suppliers, purchase orders, receipts, requirements
- **Almacenes (Warehouse)**: Parts catalog, services, inventory, stock movements
- **Inventario (Inventory)**: Transfers, stock reports, kardex, returns
- **Clientes (Customers)**: Customer management with categories
- **Usuarios (Users)**: User and role management with Spatie permissions

### Key Technologies
- **Backend**: Laravel 12, PHP 8.2+, Livewire 3, Laravel Jetstream
- **Frontend**: Vite, Tailwind CSS, Bootstrap, Alpine.js
- **Database**: MySQL/MariaDB with Eloquent ORM
- **Permissions**: Spatie Laravel Permission package
- **PDF Generation**: Barryvdh Laravel DomPDF
- **Excel**: Maatwebsite Excel for imports/exports

### Directory Structure
- `app/Http/Controllers/Admin/`: Main business logic controllers organized by module
- `app/Models/`: Eloquent models with relationships
- `resources/views/admin/`: Blade templates organized by module
- `routes/web.php`: Extensive routing with module-based organization
- `database/`: Migrations, factories, seeders

### Important Patterns
- Controllers follow module-based organization (Admin/{Module}/{Controller})
- Routes are grouped by module with middleware and prefix patterns
- Models use Eloquent relationships extensively
- Views follow admin panel structure with nested blade components
- Uses Observer pattern for inventory movements (DetalleOrdenCompraObserver)
- Service classes for complex business logic (KardexService)

### Development Notes
- Authentication handled via Laravel Jetstream
- Role-based access control throughout the application
- Extensive AJAX functionality for dynamic forms and searches
- Complex routing structure with nested resource routes
- Multi-step workflows for quotes, maintenance orders, and purchasing