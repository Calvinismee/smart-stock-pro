# SmartStock Pro

SmartStock Pro is a comprehensive, modern inventory management system built for PT Maju Bersama Digital. It features a robust multi-role architecture, real-time stock tracking, advanced reporting, and a stunning UI built with React, Tailwind CSS v4, and Inertia.js.

## Tech Stack
- **Backend:** Laravel 13, PostgreSQL
- **Frontend:** React, Inertia.js, Tailwind CSS v4
- **Libraries:** Recharts, React-Leaflet, Lucide-React
- **Tools:** Vite, Pest PHP

## Key Features
- **Multi-Role Authentication:** Admin, Manager, Staff, Viewer with distinct permissions.
- **Stock Transactions & Transfers:** Atomic stock updates and warehouse-to-warehouse transfers.
- **Low Stock Alerts:** Automatic threshold detection and internal notifications.
- **Dynamic Dashboard:** Real-time stock trends chart and geographical warehouse mapping.
- **Import / Export:** Excel/CSV imports, PDF/CSV exports for comprehensive reporting.
- **Comprehensive Audit & Error Logs:** Track all user actions and system errors.

## Business Rules & Access Scope
- **Products:** Treated as global master data across all warehouses.
- **Stock Quantity:** Tracked per product *per warehouse* through the `inventory_stocks` pivot.
- **Admin & Manager:** Can monitor all warehouses globally and access the main `/dashboard`.
- **Staff (Staf Gudang):** Restricted entirely to their assigned warehouse. Uses a dedicated `/my-warehouse` operational page instead of the global dashboard. Cannot modify global product master data.
- **Viewer:** Read-only access to `/reports` and `/inventory-stocks`.

## Installation & Setup

1. **Clone the repository and install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration:**
   Copy the example environment file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Ensure you update the database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) to match your PostgreSQL setup.*

3. **Database Migration & Seeding:**
   Run migrations and seed the database with initial sample data, including users and 5 warehouse locations in Indonesia.
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Build Frontend Assets:**
   ```bash
   npm run build
   ```

5. **Start Development Server:**
   ```bash
   php artisan serve
   ```
   Open `http://localhost:8000` in your browser.

## Demo Accounts

The seeder automatically creates the following demo accounts (password: `password`):
- **Admin:** `admin@example.com`
- **Manager:** `manager@example.com`
- **Staff (Warehouse 1):** `staff1@example.com`
- **Viewer:** `viewer@example.com`

## Testing

This project uses **Pest PHP** for testing. A separate test database is configured via `phpunit.xml`.

1. Create a test database (e.g., `smart_stock_pro_test`).
2. Run tests:
   ```bash
   ./vendor/bin/pest
   ```

## Folder Structure (Key Areas)

- `app/Services/`: Contains core business logic (StockTransactionService, AuditLogService, etc.).
- `resources/js/Pages/`: React components organized by feature module.
- `resources/js/Components/UI/`: Reusable UI elements ensuring design consistency.
- `tests/Feature/`: Pest test files covering feature flows.

## License
Proprietary software of PT Maju Bersama Digital. All rights reserved.
