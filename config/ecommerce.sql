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

-- Bảng mã OTP (One Time Password) dùng để lấy lại mật khẩu
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

-- Bản người dùng (Chứa thông tin người dùng của cửa hàng)
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
  `eRole` enum('ADMIN','EMPLOYEE','USER') NOT NULL DEFAULT 'USER',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_username` (`username`),
  UNIQUE INDEX `unique_email` (`email`),
  UNIQUE INDEX `unique_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng quản trị viên (Chứa thông tin quản trị viên của cửa hàng)
CREATE TABLE admins (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `eRole` enum('ADMIN','EMPLOYEE','USER') NOT NULL DEFAULT 'ADMIN',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_username` (`username`),
  UNIQUE INDEX `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng nhân viên (Chứa thông tin nhân viên của cửa hàng)
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
  `eRole` enum('ADMIN','EMPLOYEE','USER') NOT NULL DEFAULT 'EMPLOYEE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_username` (`username`),
  UNIQUE INDEX `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bản địa chỉ (Chứa các địa chỉ của người dùng)
CREATE TABLE addresses (
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

-- Bảng sản phẩm (Lưu thông tin cơ bản của một sản phẩm)
CREATE TABLE products (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productName` varchar(255) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `origin` varchar(255) NOT NULL,
  `category` enum(
    'FRUITS',       -- Trái cây
    'VEGETABLES',   -- Rau củ
    'GRAINS',       -- Ngũ cốc
    'OTHERS'        -- Các loại khác
  ) NOT NULL,
  `salePercent` int DEFAULT 0 CHECK (salePercent >= 0 AND salePercent <= 100),
  `sold` int(11) NOT NULL DEFAULT 0 CHECK (sold >= 0),
  `status` enum(
    'ON_SALE',      -- Đang bán
    'SUSPENDED',  -- Tạm ngưng
    'OUT_OF_STOCK'  -- Hết hàng
  ) NOT NULL DEFAULT 'ON_SALE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng sản phẩm phân loại của sản phẩm (Ví dụ: Màu sắc, Kích thước)
CREATE TABLE variant_types (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL, -- Ví dụ: Màu sắc, Kích thước
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`productId`) REFERENCES products(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng giá trị biến thể (Ví dụ: Đỏ, Xanh, Vàng)
CREATE TABLE variant_values (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variantTypeId` int(11) NOT NULL,
  `value` varchar(255) NOT NULL, -- Ví dụ: Đỏ, Xanh (cho màu sắc) hoặc S, M, L (cho kích thước)
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`variantTypeId`) REFERENCES variant_types(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng biến thể sản phẩm (Ví dụ: Sản phẩm A có 3 biến thể: Đỏ, Xanh, Vàng)
CREATE TABLE product_variants (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,  -- Mã riêng của sản phẩm
  `price` decimal(15,2) NOT NULL CHECK (price > 0),
  `quantity` int(11) NOT NULL CHECK (quantity >= 0),
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`productId`) REFERENCES products(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng kết hợp giữa biến thể và giá trị biến thể (Ví dụ: Màu sắc: Đỏ, Trọng lượng: 1kg)
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

-- Bảng hình ảnh sản phẩm (Gồm ảnh chính và ảnh biến thể)
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

-- Bảng giỏ hàng
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

-- Bảng đơn hàng chứa thông tin đơn hàng của khách hàng
CREATE TABLE orders (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `addressId` int(11) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `paymentMethod` enum('CASH_ON_DELIVERY', 'QR_TRANSFER') NOT NULL,
  `paymentStatus` enum(
    'PENDING',               -- Chờ thanh toán
    'PROCESSING',            -- Đang xử lý
    'CONFIRMED',             -- Đã xác nhận
    'PAID',                  -- Đã thanh toán
    'FAILED',                -- Thanh toán thất bại
    'REFUNDED'               -- Đã hoàn tiền
  ) NOT NULL DEFAULT 'PENDING',
  `orderStatus` enum(
    'PENDING',                -- Chờ xử lý
    'PROCESSING',             -- Đang xử lý
    'CONFIRMED',              -- Đã xác nhận
    'READY_FOR_SHIPPING',     -- Sẵn sàng giao hàng
    'SHIPPING',               -- Đang giao hàng
    'DELIVERED',              -- Đã giao hàng
    'RETURN_REQUEST',         -- Yêu cầu hoàn trả
    'RETURN_APPROVED',        -- Đã được phép hoàn trả
    'RETURNED',               -- Đã hoàn trả
    'CANCELLED'               -- Đã hủy
  ) NOT NULL DEFAULT 'PENDING',
  `note` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userId`) REFERENCES users(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`addressId`) REFERENCES addresses(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Các bảng chi tiết giỏ hàng và đơn hàng
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

-- Bảng lưu thông tin chi tiết các sản phẩm của đơn hàng
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    orderId INT NOT NULL,
    variantId INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (orderId) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (variantId) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- Bảng lịch sử đơn hàng
CREATE TABLE order_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    orderId INT NOT NULL,
    status ENUM('PENDING', 'PROCESSING', 'CONFIRMED', 'SHIPPING', 'RETURN_REQUEST', 'RETURN_APPROVED', 'RETURNED', 'DELIVERED', 'CANCELLED') NOT NULL,
    note TEXT,
    createdBy INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (orderId) REFERENCES orders(id),
    FOREIGN KEY (createdBy) REFERENCES users(id)
);

-- Bảng thanh toán (chứa thông tin thanh toán của đơn hàng)
CREATE TABLE payments (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` INT NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paymentMethod` enum('CASH_ON_DELIVERY', 'QR_TRANSFER') NOT NULL,
  `qrImage` varchar(255) DEFAULT NULL,
  `note` TEXT,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Tạo các indexes sau khi đã tạo xong bảng
-- Index cho users
ALTER TABLE users
ADD UNIQUE INDEX idx_user_unique (username, email, phone),
ADD INDEX idx_user_search (fullName, phone, email);

-- Index cho products  
ALTER TABLE products
ADD INDEX idx_product_search (productName, category, status),
ADD INDEX idx_product_filters (salePercent, sold);

-- Index cho orders
ALTER TABLE orders 
ADD INDEX idx_order_search (orderStatus, createdAt),
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

-- Trigger

DELIMITER //
CREATE TRIGGER after_order_completed
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.orderStatus = 'DELIVERED' AND OLD.orderStatus != 'DELIVERED' THEN
        UPDATE products p
        INNER JOIN product_variants pv ON p.id = pv.productId
        INNER JOIN order_items oi ON pv.id = oi.variantId
        SET p.sold = p.sold + oi.quantity
        WHERE oi.orderId = NEW.id;
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE TRIGGER before_insert_product_variant
BEFORE INSERT ON product_variants
FOR EACH ROW
BEGIN
    DECLARE product_code VARCHAR(10);
    DECLARE variant_count INT;
    
    -- Lấy mã sản phẩm dạng SP001
    SELECT CONCAT('SP', LPAD(id, 3, '0')) INTO product_code
    FROM products WHERE id = NEW.productId;
    
    -- Đếm số biến thể hiện có của sản phẩm
    SELECT COUNT(*) + 1 INTO variant_count
    FROM product_variants 
    WHERE productId = NEW.productId;
    
    -- Tạo SKU theo format: SP001-V01
    SET NEW.sku = CONCAT(product_code, '-V', LPAD(variant_count, 2, '0'));
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
WHERE o.orderStatus = 'DELIVERED'
GROUP BY DATE_FORMAT(o.createdAt, '%Y-%m')
ORDER BY month_year DESC;

-- View báo cáo sản phẩm bán chạy
CREATE VIEW top_selling_products AS
SELECT 
    p.id,
    p.productName,
    p.category,
    COUNT(DISTINCT o.id) as total_orders,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.quantity * oi.price) as total_revenue,
    ROUND(AVG(oi.price), 2) as average_price
FROM products p
JOIN product_variants pv ON p.id = pv.productId
JOIN order_items oi ON pv.id = oi.variantId
JOIN orders o ON oi.orderId = o.id
WHERE o.orderStatus = 'DELIVERED'
GROUP BY p.id, p.productName, p.category
ORDER BY total_quantity_sold DESC;

-- View báo cáo khách hàng
CREATE VIEW customer_report AS
SELECT 
    u.id as userId,
    u.username,
    u.fullName,
    COUNT(DISTINCT o.id) as total_orders,
    SUM(o.totalAmount) as total_spent,
    ROUND(AVG(o.totalAmount), 2) as average_order_value,
    MIN(o.createdAt) as first_order_date,
    MAX(o.createdAt) as last_order_date
FROM users u
LEFT JOIN orders o ON u.id = o.userId AND o.orderStatus = 'DELIVERED'
WHERE u.eRole = 'USER'
GROUP BY u.id;

-- View báo cáo thanh toán
CREATE VIEW payment_report AS
SELECT 
    DATE_FORMAT(o.createdAt, '%Y-%m') as month_year,
    o.paymentMethod as payment_method,
    o.paymentStatus as payment_status,
    COUNT(*) as total_transactions,
    SUM(o.totalAmount) as total_amount
FROM orders o
GROUP BY 
    DATE_FORMAT(o.createdAt, '%Y-%m'),
    o.paymentMethod,
    o.paymentStatus
ORDER BY month_year DESC;

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

-- Tạo tài khoản mặc định cho Admin
INSERT INTO admins (username, email, password, eRole) VALUES
('admin', 'admin@gmail.com', '$2y$10$41A7b7y96Icmxa/CbhAAuezZYbsd3A7.YY51zIxbRWpT..a.EYnB.', 'ADMIN');
  -- Username: admin
  -- Password: 123456

-- Tạo tài khoản mặc định cho Employee
INSERT INTO employees (username, email, password, fullName, phone, address, salary, eRole) VALUES
('nhanvien1', 'nhanvien1@gmail.com', '$2y$10$41A7b7y96Icmxa/CbhAAuezZYbsd3A7.YY51zIxbRWpT..a.EYnB.', 'Nguyễn Văn A', '0378627111', '123 Đường ABC, phường 1, TP.Sơn La', 10000000, 'EMPLOYEE');
  -- Username: nhanvien1
  -- Password: 123456
