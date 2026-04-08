USE mini_pos_system;

INSERT INTO users (full_name, email, password_hash, role, status)
VALUES
    ('System Administrator', 'admin@minipos.local', '$2y$10$z6bIPZqhHA9uCxwuR81YxekukNjmJNSkMLv9Hkx6YYskHymM8Zdru', 'admin', 'active'),
    ('Front Counter Cashier', 'cashier@minipos.local', '$2y$10$uBOulpnEBDBH/z1/5P829.LRf9t1aRylMraSYuA/g7XYlMi0ESn3.', 'cashier', 'active');

