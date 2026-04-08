# Mini POS System

Mini POS System is a portfolio-ready PHP and MySQL project focused on realistic point of sale and inventory workflows. The goal is to keep the codebase simple enough for interviews and shared hosting deployment while still demonstrating practical software engineering skills such as modular structure, UI consistency, authentication, CRUD, stock control, reporting, testing, and documentation.

## Step 1 Status

Step 1 sets up the visual and structural foundation only:

- project folder structure aligned for future POS, inventory, and reporting modules
- shared PHP layout includes for reusable head, sidebar, topbar, auth shell, and dashboard shell
- design system with Bootstrap plus custom CSS variables, cards, forms, tables, badges, and responsive dashboard styling
- polished login page UI
- dashboard layout shell with realistic preview cards and tables

No database logic, authentication logic, CRUD operations, or transactions are implemented yet in this step.

## Tech Stack

- PHP
- MySQL
- HTML
- CSS
- Bootstrap 5
- JavaScript

## Proposed Project Structure

```text
MiniPostSystem/
|-- assets/
|   |-- css/
|   |   `-- app.css
|   |-- images/
|   `-- js/
|       `-- app.js
|-- database/
|   |-- schema/
|   `-- seeds/
|-- docs/
|   `-- screenshots/
|-- includes/
|   |-- config/
|   |   `-- app.php
|   |-- helpers/
|   |   `-- ui.php
|   `-- layout/
|       |-- app-shell-end.php
|       |-- app-shell-start.php
|       |-- auth-shell-end.php
|       |-- auth-shell-start.php
|       |-- head.php
|       |-- sidebar.php
|       `-- topbar.php
|-- modules/
|   |-- categories/
|   |-- dashboard/
|   |   `-- index.php
|   |-- inventory/
|   |-- pos/
|   |-- products/
|   |-- reports/
|   `-- transactions/
|-- dashboard.php
|-- index.php
|-- login.php
`-- README.md
```

## UI Direction

The interface is designed to feel like a modern admin dashboard instead of a basic school project:

- clean spacing and soft glassmorphism-inspired surfaces
- strong card and table hierarchy
- reusable badges and button styles
- responsive sidebar and topbar layout
- polished POS-friendly visual system ready for future modules

## Local Preview

Use a local PHP server or XAMPP/Laragon and open the project in your browser.

### Example with PHP built-in server

```bash
php -S localhost:8000
```

Then visit:

- `http://localhost:8000/login.php`
- `http://localhost:8000/dashboard.php`

## Planned Build Roadmap

- Step 2: database schema, connection, authentication, roles, logout
- Step 3: categories and products CRUD
- Step 4: inventory management
- Step 5: POS transactions
- Step 6: receipt and transaction history
- Step 7: reports
- Step 8: audit log, backup/export, validation and security hardening
- Step 9: final polish, sample data, full documentation, deployment notes

