# Manual Testing Checklist

Use this checklist after importing:

- `database/schema/minipos_system.sql`
- `database/seeds/step2_sample_users.sql`
- `database/seeds/step9_sample_data.sql`

## Authentication

- Login works with `admin@minipos.local` / `Admin@123`
- Login works with `cashier@minipos.local` / `Cashier@123`
- Invalid credentials show an error message
- Repeated failed login attempts trigger temporary rate limiting
- Logout returns to the login page successfully

## Role Access

- Admin can open categories, products, inventory, POS, transactions, reports, and audit log
- Cashier can open dashboard, POS, and transactions
- Cashier is blocked from admin-only modules

## Categories

- Create a category with valid data
- Duplicate category name is rejected
- Edit a category successfully
- Delete an unused category successfully
- Deleting a category with assigned products is blocked

## Products

- Create a product with valid category, SKU, price, and stock
- Duplicate SKU is rejected
- Negative price or negative stock is rejected
- Edit a product successfully
- Deleting a product with sales or inventory history is blocked

## Inventory

- Stock In increases product stock
- Stock Out decreases product stock
- Stock Out larger than available stock is rejected
- Inventory history records the user, quantity, type, and note
- Low-stock products appear on the inventory and reports pages

## POS

- Search by product name, SKU, or category works
- Add product to cart works
- Update cart quantity works
- Remove item from cart works
- Clear cart works
- Paid amount lower than total is rejected
- Checkout saves the transaction and deducts stock
- Out-of-stock items cannot be sold

## Transactions and Receipt

- Transaction history loads
- Search and payment filter work
- Transaction details page loads correctly
- Receipt page loads correctly
- Browser print preview works from the receipt page

## Reports

- Daily sales section loads
- Monthly summary section loads
- Top-selling products section loads
- Low-stock products section loads
- Sales CSV export downloads
- Low-stock CSV export downloads

## Audit Log

- Login and logout actions appear
- Category and product changes appear
- Inventory stock in/out appears
- POS checkout appears

## UI and Responsive Checks

- Sidebar works on desktop
- Sidebar toggle works on smaller screens
- Delete confirmation modal is styled correctly
- Loading bar appears during page transitions or form submission
- Core pages remain readable on mobile width
