USE mini_pos_system;

SET @admin_id = (SELECT id FROM users WHERE email = 'admin@minipos.local' LIMIT 1);
SET @cashier_id = (SELECT id FROM users WHERE email = 'cashier@minipos.local' LIMIT 1);

INSERT INTO categories (name, description)
SELECT 'Beverages', 'Drinks, canned beverages, bottled water, and ready-to-drink items.'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Beverages');

INSERT INTO categories (name, description)
SELECT 'Snacks', 'Light food, chips, biscuits, and convenience snacks.'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Snacks');

INSERT INTO categories (name, description)
SELECT 'Stationery', 'Office and school supplies such as pens, paper, and notebooks.'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Stationery');

INSERT INTO categories (name, description)
SELECT 'Electronics Accessories', 'Chargers, cables, earphones, and mobile accessories.'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Electronics Accessories');

INSERT INTO products (category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status)
SELECT c.id, 'BEV-001', 'Mineral Water 500ml', 'Bottled mineral water for grab-and-go checkout.', 2.50, 1.20, 42, 10, 'active'
FROM categories c
WHERE c.name = 'Beverages'
  AND NOT EXISTS (SELECT 1 FROM products WHERE sku = 'BEV-001');

INSERT INTO products (category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status)
SELECT c.id, 'BEV-002', 'Sparkling Orange Drink', 'Carbonated orange drink in 320ml can.', 3.80, 2.00, 6, 10, 'active'
FROM categories c
WHERE c.name = 'Beverages'
  AND NOT EXISTS (SELECT 1 FROM products WHERE sku = 'BEV-002');

INSERT INTO products (category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status)
SELECT c.id, 'SNK-001', 'Potato Crisps Original', 'Salted potato crisps 60g pack.', 4.50, 2.30, 25, 8, 'active'
FROM categories c
WHERE c.name = 'Snacks'
  AND NOT EXISTS (SELECT 1 FROM products WHERE sku = 'SNK-001');

INSERT INTO products (category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status)
SELECT c.id, 'STN-001', 'A5 Notebook', 'Soft cover notebook for office and student use.', 6.90, 3.40, 18, 5, 'active'
FROM categories c
WHERE c.name = 'Stationery'
  AND NOT EXISTS (SELECT 1 FROM products WHERE sku = 'STN-001');

INSERT INTO products (category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status)
SELECT c.id, 'ELA-001', 'USB-C Charging Cable', '1 meter fast charging cable.', 12.90, 6.50, 4, 6, 'active'
FROM categories c
WHERE c.name = 'Electronics Accessories'
  AND NOT EXISTS (SELECT 1 FROM products WHERE sku = 'ELA-001');

INSERT INTO products (category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status)
SELECT c.id, 'SNK-002', 'Chocolate Wafer Bar', 'Single bar snack near cashier counter.', 2.20, 1.00, 0, 10, 'inactive'
FROM categories c
WHERE c.name = 'Snacks'
  AND NOT EXISTS (SELECT 1 FROM products WHERE sku = 'SNK-002');

INSERT INTO sales (invoice_number, cashier_id, subtotal, tax_amount, discount_amount, total_amount, paid_amount, balance_amount, payment_method, created_at)
SELECT 'INV-SEED-1001', @cashier_id, 17.90, 0.00, 0.00, 17.90, 20.00, 2.10, 'cash', '2026-04-07 10:15:00'
WHERE @cashier_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM sales WHERE invoice_number = 'INV-SEED-1001');

INSERT INTO sales (invoice_number, cashier_id, subtotal, tax_amount, discount_amount, total_amount, paid_amount, balance_amount, payment_method, created_at)
SELECT 'INV-SEED-1002', @cashier_id, 18.20, 0.00, 0.00, 18.20, 18.20, 0.00, 'card', '2026-04-08 12:05:00'
WHERE @cashier_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM sales WHERE invoice_number = 'INV-SEED-1002');

INSERT INTO sales (invoice_number, cashier_id, subtotal, tax_amount, discount_amount, total_amount, paid_amount, balance_amount, payment_method, created_at)
SELECT 'INV-SEED-1003', @cashier_id, 12.80, 0.00, 0.00, 12.80, 20.00, 7.20, 'cash', '2026-04-08 18:20:00'
WHERE @cashier_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM sales WHERE invoice_number = 'INV-SEED-1003');

INSERT INTO sales (invoice_number, cashier_id, subtotal, tax_amount, discount_amount, total_amount, paid_amount, balance_amount, payment_method, created_at)
SELECT 'INV-SEED-1004', @cashier_id, 25.80, 0.00, 0.00, 25.80, 25.80, 0.00, 'ewallet', '2026-04-09 09:45:00'
WHERE @cashier_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM sales WHERE invoice_number = 'INV-SEED-1004');

INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
SELECT s.id, p.id, 2, 2.50, 5.00
FROM sales s
INNER JOIN products p ON p.sku = 'BEV-001'
WHERE s.invoice_number = 'INV-SEED-1001'
  AND NOT EXISTS (SELECT 1 FROM sale_items WHERE sale_id = s.id AND product_id = p.id);

INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
SELECT s.id, p.id, 1, 12.90, 12.90
FROM sales s
INNER JOIN products p ON p.sku = 'ELA-001'
WHERE s.invoice_number = 'INV-SEED-1001'
  AND NOT EXISTS (SELECT 1 FROM sale_items WHERE sale_id = s.id AND product_id = p.id);

INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
SELECT s.id, p.id, 2, 6.90, 13.80
FROM sales s
INNER JOIN products p ON p.sku = 'STN-001'
WHERE s.invoice_number = 'INV-SEED-1002'
  AND NOT EXISTS (SELECT 1 FROM sale_items WHERE sale_id = s.id AND product_id = p.id);

INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
SELECT s.id, p.id, 2, 4.50, 9.00
FROM sales s
INNER JOIN products p ON p.sku = 'SNK-001'
WHERE s.invoice_number = 'INV-SEED-1003'
  AND NOT EXISTS (SELECT 1 FROM sale_items WHERE sale_id = s.id AND product_id = p.id);

INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
SELECT s.id, p.id, 1, 3.80, 3.80
FROM sales s
INNER JOIN products p ON p.sku = 'BEV-002'
WHERE s.invoice_number = 'INV-SEED-1003'
  AND NOT EXISTS (SELECT 1 FROM sale_items WHERE sale_id = s.id AND product_id = p.id);

INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
SELECT s.id, p.id, 2, 6.90, 13.80
FROM sales s
INNER JOIN products p ON p.sku = 'STN-001'
WHERE s.invoice_number = 'INV-SEED-1004'
  AND NOT EXISTS (SELECT 1 FROM sale_items WHERE sale_id = s.id AND product_id = p.id);

INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
SELECT s.id, p.id, 1, 12.90, 12.90
FROM sales s
INNER JOIN products p ON p.sku = 'ELA-001'
WHERE s.invoice_number = 'INV-SEED-1004'
  AND NOT EXISTS (SELECT 1 FROM sale_items WHERE sale_id = s.id AND product_id = p.id);

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @admin_id, 'stock_in', 50, 'Initial seed stock for bottled water.', '2026-04-06 09:00:00'
FROM products p
WHERE p.sku = 'BEV-001'
  AND @admin_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'stock_in' AND notes = 'Initial seed stock for bottled water.'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @admin_id, 'stock_in', 12, 'Small restock for orange drink.', '2026-04-06 09:15:00'
FROM products p
WHERE p.sku = 'BEV-002'
  AND @admin_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'stock_in' AND notes = 'Small restock for orange drink.'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @admin_id, 'stock_in', 30, 'Initial snack shelf fill.', '2026-04-06 09:30:00'
FROM products p
WHERE p.sku = 'SNK-001'
  AND @admin_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'stock_in' AND notes = 'Initial snack shelf fill.'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @admin_id, 'stock_in', 20, 'Initial notebook stock.', '2026-04-06 09:45:00'
FROM products p
WHERE p.sku = 'STN-001'
  AND @admin_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'stock_in' AND notes = 'Initial notebook stock.'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @admin_id, 'stock_in', 8, 'Initial cable stock.', '2026-04-06 10:00:00'
FROM products p
WHERE p.sku = 'ELA-001'
  AND @admin_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'stock_in' AND notes = 'Initial cable stock.'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @cashier_id, 'sale_adjustment', 2, 'POS sale INV-SEED-1001', '2026-04-07 10:15:00'
FROM products p
WHERE p.sku = 'BEV-001'
  AND @cashier_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'sale_adjustment' AND notes = 'POS sale INV-SEED-1001'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @cashier_id, 'sale_adjustment', 1, 'POS sale INV-SEED-1001', '2026-04-07 10:15:00'
FROM products p
WHERE p.sku = 'ELA-001'
  AND @cashier_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'sale_adjustment' AND notes = 'POS sale INV-SEED-1001'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @cashier_id, 'sale_adjustment', 2, 'POS sale INV-SEED-1002', '2026-04-08 12:05:00'
FROM products p
WHERE p.sku = 'STN-001'
  AND @cashier_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'sale_adjustment' AND notes = 'POS sale INV-SEED-1002'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @cashier_id, 'sale_adjustment', 2, 'POS sale INV-SEED-1003', '2026-04-08 18:20:00'
FROM products p
WHERE p.sku = 'SNK-001'
  AND @cashier_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'sale_adjustment' AND notes = 'POS sale INV-SEED-1003'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @cashier_id, 'sale_adjustment', 1, 'POS sale INV-SEED-1003', '2026-04-08 18:20:00'
FROM products p
WHERE p.sku = 'BEV-002'
  AND @cashier_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'sale_adjustment' AND notes = 'POS sale INV-SEED-1003'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @cashier_id, 'sale_adjustment', 2, 'POS sale INV-SEED-1004', '2026-04-09 09:45:00'
FROM products p
WHERE p.sku = 'STN-001'
  AND @cashier_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'sale_adjustment' AND notes = 'POS sale INV-SEED-1004'
  );

INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes, created_at)
SELECT p.id, @cashier_id, 'sale_adjustment', 1, 'POS sale INV-SEED-1004', '2026-04-09 09:45:00'
FROM products p
WHERE p.sku = 'ELA-001'
  AND @cashier_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM inventory_movements
      WHERE product_id = p.id AND movement_type = 'sale_adjustment' AND notes = 'POS sale INV-SEED-1004'
  );
