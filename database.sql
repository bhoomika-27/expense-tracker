-- Personal Expense Tracker
-- Database schema + sample data
-- Import this file via phpMyAdmin or: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS expense_tracker
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE expense_tracker;

-- ---------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: expenses
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS expenses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_expenses_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_expenses_user (user_id),
    INDEX idx_expenses_date (expense_date),
    INDEX idx_expenses_category (category),
    INDEX idx_expenses_user_date (user_id, expense_date)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Sample test user
-- Email:    test@example.com
-- Password: Test@1234
--
-- This is a real bcrypt hash of "Test@1234" and will work with PHP's
-- password_verify(). Never store plaintext passwords — always use
-- password_hash()/password_verify() as this app does everywhere else.
-- ---------------------------------------------------------
INSERT INTO users (name, email, password) VALUES
('Test User', 'test@example.com', '$2b$10$XptZne3tZ/obZBsnKZlPv.XYm9WiMmnaykNElzAJ512PwcuEyzNzG');

-- ---------------------------------------------------------
-- Sample expenses for the test user (id = 1)
-- ---------------------------------------------------------
INSERT INTO expenses (user_id, amount, category, description, expense_date) VALUES
(1, 500.00,  'Food',          'Lunch with friends',        '2026-08-01'),
(1, 1200.00, 'Transport',     'Monthly train pass',        '2026-08-02'),
(1, 3000.00, 'Shopping',      'New shoes',                 '2026-08-05'),
(1, 2100.00, 'Bills',         'Electricity bill',          '2026-08-07'),
(1, 800.00,  'Education',     'Online course subscription','2026-08-10'),
(1, 450.00,  'Entertainment', 'Movie night',                '2026-08-12'),
(1, 1500.00, 'Healthcare',    'Doctor visit',               '2026-08-15'),
(1, 300.00,  'Food',          'Groceries',                  '2026-08-18'),
(1, 200.00,  'Other',         'Miscellaneous',              '2026-08-20');
