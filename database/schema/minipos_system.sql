CREATE DATABASE IF NOT EXISTS mini_pos_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mini_pos_system;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    sku VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    unit_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    cost_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    stock_quantity INT NOT NULL DEFAULT 0,
    min_stock_level INT NOT NULL DEFAULT 5,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_sku (sku),
    KEY idx_products_category_id (category_id),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory_movements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    movement_type ENUM('stock_in', 'stock_out', 'sale_adjustment') NOT NULL,
    quantity INT NOT NULL,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_inventory_product_id (product_id),
    KEY idx_inventory_user_id (user_id),
    CONSTRAINT fk_inventory_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_inventory_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(40) NOT NULL,
    cashier_id INT UNSIGNED NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    balance_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    payment_method ENUM('cash', 'card', 'ewallet') NOT NULL DEFAULT 'cash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_sales_invoice_number (invoice_number),
    KEY idx_sales_cashier_id (cashier_id),
    CONSTRAINT fk_sales_cashier
        FOREIGN KEY (cashier_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sale_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    line_total DECIMAL(10, 2) NOT NULL,
    KEY idx_sale_items_sale_id (sale_id),
    KEY idx_sale_items_product_id (product_id),
    CONSTRAINT fk_sale_items_sale
        FOREIGN KEY (sale_id) REFERENCES sales(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_sale_items_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_audit_logs_user_id (user_id),
    CONSTRAINT fk_audit_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

