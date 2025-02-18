create database IF NOT EXISTS myDB;

use myDB;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255),
    `phone` VARCHAR(15),
    `money` DECIMAL(10, 2)
);

INSERT INTO `users` (`username`, `password`, `address`, `phone`,`money`) VALUES ('admin','admin','nowhere','099999999','1000'); 
INSERT INTO `users` (`username`, `password`, `address`, `phone`,`money`) VALUES ('john_doe', 'password123', '123 Street, City', '0123456789','1000');
INSERT INTO `users` (`username`, `password`, `address`, `phone`,`money`) VALUES ('jane_smith', 'securepass', '456 Avenue, Town', '0987654321','1000');
INSERT INTO `users` (`username`, `password`, `address`, `phone`,`money`) VALUES ('emma_white', 'emma@2025', '321 Boulevard, Village', '0998877665','1000');
INSERT INTO `users` (`username`, `password`, `address`, `phone`,`money`) VALUES ('oliver_black', 'oliver@pass', '654 Lane, Hamlet', '0776655443','1000');

-- Tạo bảng Product
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_product VARCHAR(255),
    description TEXT
);

INSERT INTO products (name, price, image_product, description) VALUES
('Smartphone XYZ', 499.99, 'smartphone_xyz.jpg', 'A high-end smartphone with a 6.5-inch display and 128GB storage'),
('Wireless Headphones', 79.99, 'wireless_headphones.jpg', 'Comfortable wireless headphones with noise-cancellation feature'),
('Laptop ABC', 899.99, 'laptop_abc.jpg', 'A powerful laptop with 16GB RAM and 512GB SSD'),
('Fitness Tracker', 59.99, 'fitness_trackers.jpg', 'A sleek fitness tracker with heart rate monitoring'),
('Smartwatch DEF', 199.99, 'smartwatch.jpg', 'A stylish smartwatch with fitness tracking and message notifications');

-- Tao bang cart
CREATE TABLE cart (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

--Tao bang cart_product
CREATE TABLE cart_product(
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    id_cart INT NOT NULL,
    id_product INT NOT NULL,
    number_of_ordered_product INT NOT NULL,
    FOREIGN KEY (id_cart) REFERENCES cart(id),
    FOREIGN KEY (id_product) REFERENCES products(id)
);

INSERT INTO cart_product (id_cart, id_product, number_of_ordered_product)
VALUES 
(1, 1, 2), -- John Doe orders 2 Laptops

(2, 2, 1), -- Jane Smith orders 1 Smartphone
(3, 3, 3); -- Alice Wonder orders 3 Headphones

