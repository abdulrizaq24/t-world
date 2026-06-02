CREATE DATABASE IF NOT EXISTS t_world CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE t_world;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS return_requests;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    phone VARCHAR(40) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(120) NULL,
    postal_code VARCHAR(40) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    category ENUM('plain', 'oversized', 'graphic', 'new') NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_products_category (category),
    INDEX idx_products_active (is_active)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    customer_name VARCHAR(160) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(120) NOT NULL,
    postal_code VARCHAR(40) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_orders_status (status),
    INDEX idx_orders_created_at (created_at)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(160) NOT NULL,
    size VARCHAR(10) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order_items_order (order_id)
);


CREATE TABLE return_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('requested', 'approved', 'rejected', 'received', 'refunded') NOT NULL DEFAULT 'requested',
    admin_note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_return_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_return_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_return_order (order_id),
    INDEX idx_return_status (status)
);
INSERT INTO products (name, category, description, price, stock, image_url) VALUES
('Classic Black Tee', 'plain', 'A clean everyday T-shirt with a soft cotton feel and relaxed shape.', 24.99, 34, 'images/product-1.jpg'),
('Oversized White Tee', 'oversized', 'A relaxed oversized T-shirt made for easy streetwear styling.', 29.99, 21, 'images/product-2.jpg'),
('Street Graphic Tee', 'graphic', 'A bold graphic tee for casual streetwear outfits.', 34.99, 18, 'images/product-3.jpg'),
('Navy Everyday Tee', 'plain', 'A simple navy tee designed for daily wear.', 22.99, 30, 'images/product-4.jpg'),
('Relaxed Sand Tee', 'new', 'A new relaxed-fit tee in a soft sand color.', 27.99, 25, 'images/product-5.jpg'),
('Heavyweight Green Tee', 'new', 'A heavyweight cotton tee with a structured fit.', 31.99, 20, 'images/product-6.jpg');

INSERT INTO users (name, email, password_hash, role) VALUES
('Admin User', 'admin@t-world.test', '$2y$10$GOW/BEL0hWOZuYYlw8jk/OwwMJbgjQPkJXWgrXpKdNocFT8eS8NoK', 'admin');



