-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 13, 2024 lúc 07:23 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

-- Xóa database nếu tồn tại và tạo mới
DROP DATABASE IF EXISTS ecommerce;
CREATE DATABASE ecommerce;
USE ecommerce;

-- Thiết lập môi trường
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Các bảng độc lập (không có khóa ngoại)
CREATE TABLE payments (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(15,2) NOT NULL,
  `paymentMethod` enum('CASH_ON_DELIVERY', 'QR_TRANSFER') NOT NULL,
  `qrImage` varchar(255) DEFAULT NULL,
  `refNo` varchar(255) DEFAULT NULL,
  `paymentStatus` enum('PENDING', 'COMPLETED', 'FAILED', 'REFUNDED') NOT NULL DEFAULT 'PENDING',
  `paymentDate` datetime DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE otps (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountId` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `accountType` enum('ADMIN', 'USER', 'EMPLOYEE') NOT NULL,
  `isUsed` boolean NOT NULL DEFAULT false,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiredAt` timestamp NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 5 MINUTE),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Các bảng người dùng cơ sở
CREATE TABLE users (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `avatar` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_username` (`username`),
  UNIQUE INDEX `unique_email` (`email`),
  UNIQUE INDEX `unique_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE admins (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_username` (`username`),
  UNIQUE INDEX `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE employees (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `avatar` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `salary` decimal(10,0) NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_username` (`username`),
  UNIQUE INDEX `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Các bảng phụ thuộc người dùng
CREATE TABLE address (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `isDefault` boolean DEFAULT false,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userId`) REFERENCES users(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Các bảng sản phẩm cơ sở
CREATE TABLE products (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productName` varchar(255) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `origin` varchar(255) NOT NULL,
  `category` enum(
    'FRUITS',
    'VEGETABLES',
    'GRAINS',
    'OTHERS'
  ) NOT NULL,
  `price` decimal(15,2) NOT NULL CHECK (price >= 0),
  `salePercent` int DEFAULT 0 CHECK (salePercent >= 0 AND salePercent <= 100),
  `stockQuantity` int(11) NOT NULL CHECK (stockQuantity >= 0),
  `sold` int(11) NOT NULL DEFAULT 0 CHECK (sold >= 0),
  `status` enum(
    'ON_SALE',
    'SUSPENDED',
    'OUT_OF_STOCK'
  ) NOT NULL DEFAULT 'ON_SALE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Các bảng phụ thuộc sản phẩm
CREATE TABLE variant_types (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL, -- Ví dụ: Màu sắc, Kích thước
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`productId`) REFERENCES products(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE variant_values (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variantTypeId` int(11) NOT NULL,
  `value` varchar(255) NOT NULL, -- Ví dụ: Đỏ, Xanh (cho màu sắc) hoặc S, M, L (cho kích thước)
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`variantTypeId`) REFERENCES variant_types(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE product_variants (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL CHECK (price > 0),
  `quantity` int(11) NOT NULL CHECK (quantity >= 0),
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`productId`) REFERENCES products(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE variant_combinations (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productVariantId` int(11) NOT NULL,
  `variantValueId` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`productVariantId`) REFERENCES product_variants(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variantValueId`) REFERENCES variant_values(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_combination` (`productVariantId`, `variantValueId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE product_images (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `variantId` int(11) DEFAULT NULL, -- NULL nếu là ảnh chung của sản phẩm
  `imageUrl` varchar(255) NOT NULL,
  `isThumbnail` boolean NOT NULL DEFAULT false, -- Ảnh đại diện chính của sản phẩm
  `displayOrder` int(11) NOT NULL DEFAULT 0,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`productId`) REFERENCES products(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variantId`) REFERENCES product_variants(`id`) ON DELETE SET NULL,
  CONSTRAINT `unique_variant_image` UNIQUE (`variantId`) -- Đảm bảo 1 biến thể chỉ có 1 ảnh
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Các bảng giỏ hàng và đơn hàng
CREATE TABLE carts (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `variantId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userId`) REFERENCES users(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variantId`) REFERENCES product_variants(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_cart_item` (`userId`, `variantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE orders (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `addressId` int(11) NULL,
  `productList` text NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `orderDate` datetime NOT NULL,
  `shippingDate` datetime DEFAULT NULL,
  `deliveryDate` datetime DEFAULT NULL,
  `status` enum(
    'PENDING',
    'PROCESSING',
    'CONFIRMED',
    'READY_FOR_SHIPPING',
    'SHIPPING',
    'SHIPPED',
    'DELIVERED',
    'CANCELLED',
    'RETURNED'
  ) NOT NULL DEFAULT 'PENDING',
  `paymentId` int(11) NULL,
  `paymentStatus` enum('PENDING', 'COMPLETED', 'FAILED', 'REFUNDED') NOT NULL DEFAULT 'PENDING',
  `note` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userId`) REFERENCES users(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`addressId`) REFERENCES address(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`paymentId`) REFERENCES payments(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE items (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `variantId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `orderId` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`productId`) REFERENCES products(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variantId`) REFERENCES product_variants(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`orderId`) REFERENCES orders(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo các indexes sau khi đã tạo xong bảng
-- Index cho users
ALTER TABLE users
ADD UNIQUE INDEX idx_user_unique (username, email, phone),
ADD INDEX idx_user_search (fullName, phone, email);

-- Index cho products  
ALTER TABLE products
ADD INDEX idx_product_search (productName, category, status),
ADD INDEX idx_product_filters (price, stockQuantity, salePercent);

-- Index cho orders
ALTER TABLE orders 
ADD INDEX idx_order_search (status, orderDate),
ADD INDEX idx_order_user (userId);

-- Index cho variants
ALTER TABLE product_variants
ADD UNIQUE INDEX idx_variant_unique (productId, sku),
ADD INDEX idx_variant_search (price, quantity);

-- Bật lại foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- Tạo Event
CREATE EVENT delete_expired_otps
ON SCHEDULE EVERY 1 MINUTE
ENABLE
DO
  DELETE FROM otps 
  WHERE isUsed = true 
  OR expiredAt < CURRENT_TIMESTAMP;

/* 
-- Các câu lệnh cập nhật mẫu (không thực thi)
UPDATE product_variants 
SET sold = sold + [quantity] 
WHERE id = [variantId];

UPDATE products 
SET sold = sold + [quantity] 
WHERE id = [productId];
*/

-- Tiếp tục với các phần còn lại...

DELIMITER //
CREATE TRIGGER after_order_completed
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'DELIVERED' AND OLD.status != 'DELIVERED' THEN
        UPDATE product_variants pv
        INNER JOIN items i ON pv.id = i.variantId
        SET pv.sold = pv.sold + i.quantity
        WHERE i.orderId = NEW.id;
        
        UPDATE products p
        INNER JOIN items i ON p.id = i.productId
        SET p.sold = p.sold + i.quantity
        WHERE i.orderId = NEW.id;
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE TRIGGER before_insert_product_variant
BEFORE INSERT ON product_variants
FOR EACH ROW
BEGIN
    DECLARE product_code VARCHAR(50);
    
    -- Chỉ tạo product_code
    SELECT CONCAT('SP', LPAD(id, 3, '0')) INTO product_code
    FROM products WHERE id = NEW.productId;
    
    -- Tạo SKU tạm thời
    SET NEW.sku = CONCAT(product_code, '-', UUID());
END //
DELIMITER ;

-- Thêm ràng buộc cho email
ALTER TABLE users 
ADD CONSTRAINT check_email 
CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$');

-- Thêm ràng buộc cho số điện thoại
ALTER TABLE users
ADD CONSTRAINT check_phone
CHECK (phone REGEXP '^[0-9]{10,11}$');

-- Thêm trigger khi cập nhật số lượng trong kho
DELIMITER //
CREATE TRIGGER before_update_product_variant
BEFORE UPDATE ON product_variants
FOR EACH ROW
BEGIN
    IF NEW.quantity < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Số lượng không thể âm';
    END IF;
END //
DELIMITER ;

-- Thêm trigger tự động cập nhật trạng thái sản phẩm
DELIMITER //
CREATE OR REPLACE TRIGGER after_update_variant_quantity
AFTER UPDATE ON product_variants
FOR EACH ROW
BEGIN
    DECLARE total_quantity INT;
    
    -- Tính tổng số lượng của tất cả biến thể
    SELECT SUM(quantity) INTO total_quantity
    FROM product_variants
    WHERE productId = NEW.productId;
    
    -- Chỉ cập nhật status khi số lượng = 0
    IF total_quantity = 0 THEN
        UPDATE products 
        SET status = 'OUT_OF_STOCK'
        WHERE id = NEW.productId;
    END IF;
END //
DELIMITER ;

-- View báo cáo doanh thu theo tháng
CREATE VIEW monthly_revenue_report AS
SELECT 
    DATE_FORMAT(o.createdAt, '%Y-%m') as month_year,
    COUNT(DISTINCT o.id) as total_orders,
    COUNT(DISTINCT o.userId) as total_customers,
    SUM(o.totalAmount) as total_revenue,
    ROUND(AVG(o.totalAmount), 2) as average_order_value
FROM orders o
WHERE o.status = 'DELIVERED'
GROUP BY DATE_FORMAT(o.createdAt, '%Y-%m')
ORDER BY month_year DESC;

-- View báo cáo sản phẩm bán chạy theo tháng
CREATE VIEW monthly_best_selling_products AS
SELECT 
    DATE_FORMAT(o.createdAt, '%Y-%m') as month_year,
    p.id as product_id,
    p.productName,
    p.category,
    SUM(i.quantity) as total_quantity_sold,
    SUM(i.quantity * i.price) as total_revenue,
    COUNT(DISTINCT o.id) as number_of_orders,
    COUNT(DISTINCT o.userId) as number_of_buyers
FROM orders o
JOIN items i ON o.id = i.orderId
JOIN products p ON i.productId = p.id
WHERE o.status = 'DELIVERED'
GROUP BY 
    DATE_FORMAT(o.createdAt, '%Y-%m'),
    p.id
ORDER BY 
    month_year DESC,
    total_quantity_sold DESC;

-- View báo cáo khách hàng thân thiết theo tháng
CREATE VIEW monthly_top_customers AS
SELECT 
    DATE_FORMAT(o.createdAt, '%Y-%m') as month_year,
    u.id as user_id,
    u.fullName,
    u.email,
    u.phone,
    COUNT(DISTINCT o.id) as total_orders,
    SUM(o.totalAmount) as total_spent,
    ROUND(AVG(o.totalAmount), 2) as average_order_value,
    MIN(o.createdAt) as first_order_date,
    MAX(o.createdAt) as last_order_date
FROM orders o
JOIN users u ON o.userId = u.id
WHERE o.status = 'DELIVERED'
GROUP BY 
    DATE_FORMAT(o.createdAt, '%Y-%m'),
    u.id
HAVING total_orders >= 1
ORDER BY 
    month_year DESC,
    total_orders DESC,
    total_spent DESC;

DELIMITER //
CREATE TRIGGER before_insert_cart
BEFORE INSERT ON carts
FOR EACH ROW
BEGIN
    DECLARE available INT;
    SELECT quantity INTO available 
    FROM product_variants 
    WHERE id = NEW.variantId;
    
    IF available < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Số lượng trong kho không đủ';
    END IF;
END //
DELIMITER ;