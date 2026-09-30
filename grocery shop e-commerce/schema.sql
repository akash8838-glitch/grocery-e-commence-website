CREATE DATABASE IF NOT EXISTS super_mart_cart;
USE super_mart_cart;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    image VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(5, 2) DEFAULT 0.00,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    rating DECIMAL(2, 1) DEFAULT 0.0,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    address TEXT,
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert initial categories
INSERT INTO categories (name) VALUES ('Vegetables'), ('Pulses'), ('Cooking Oils'), ('Spices');

-- Default Admin (Password: admin123)
-- Note: In a real app, you should hash this. This is for initial setup.
-- You can register a user and then change their role to 'admin' in the database.
INSERT INTO users (name, email, password, role) VALUES ('Admin User', 'admin@supermart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample Products
INSERT INTO products (name, category_id, price, discount, stock, description, image, rating) VALUES 
('Fresh Organic Tomatoes', 1, 2.99, 10, 50, 'Grown locally, 100% organic tomatoes.', 'https://images.unsplash.com/photo-1518977676601-b53f02bad177?auto=format&fit=crop&q=80&w=400', 4.8),
('Red Lentils (Masoor Dal)', 2, 4.50, 0, 100, 'Premium quality split red lentils.', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&q=80&w=400', 4.5),
('Pure Olive Oil', 3, 12.99, 5, 30, 'Cold-pressed extra virgin olive oil.', 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&q=80&w=400', 4.9),
('Turmeric Powder', 4, 3.20, 0, 80, 'Pure organic turmeric powder with high curcumin.', 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?auto=format&fit=crop&q=80&w=400', 4.7);
