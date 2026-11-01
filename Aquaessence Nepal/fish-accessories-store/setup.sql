-- Create database
CREATE DATABASE IF NOT EXISTS fish_accessories_store;
USE fish_accessories_store;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table (optional)
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table (optional)
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2),
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default users (plaintext passwords for development)
INSERT INTO users (username, password, role) VALUES
('admin', 'admin123', 'admin'),
('user', 'user123', 'user');

-- Insert sample products
INSERT INTO products (name, price, quantity, description) VALUES
('Goldfish', 15.99, 50, 'Beautiful orange goldfish'),
('Aquarium Tank 20L', 49.99, 20, 'Complete starter aquarium kit'),
('Fish Food', 9.99, 100, 'Nutritious flakes for tropical fish'),
('Filter System', 29.99, 30, 'Essential water filtration system'),
('Decorative Plants', 12.99, 80, 'Artificial aquarium plants');
