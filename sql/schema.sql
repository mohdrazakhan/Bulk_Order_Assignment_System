-- Bulk Order Assignment System Schema

DROP TABLE IF EXISTS assignments;
DROP TABLE IF EXISTS courier_locations;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS couriers;

-- 1. Couriers Table
CREATE TABLE couriers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    daily_capacity INT NOT NULL DEFAULT 10,
    current_assigned_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Courier Locations (Normalization for many-to-many relationship if needed, 
-- but here simple 1-to-many for simplicity as per requirement 'serviceable_locations')
-- Assuming a courier services multiple locations.
CREATE TABLE courier_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    courier_id INT NOT NULL,
    location VARCHAR(100) NOT NULL,
    FOREIGN KEY (courier_id) REFERENCES couriers(id) ON DELETE CASCADE,
    INDEX idx_location_courier (location, courier_id) -- Optimized for "Find couriers in 'Zone A'"
);

-- 3. Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    delivery_location VARCHAR(100) NOT NULL,
    order_value DECIMAL(10, 2) NOT NULL,
    order_date DATETIME NOT NULL,
    status ENUM('NEW', 'ASSIGNED', 'UNASSIGNED') NOT NULL DEFAULT 'NEW',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status_location_date (status, delivery_location, order_date) -- Optimized for fetching unassigned orders by zone
);

-- 4. Assignments Table
CREATE TABLE assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    courier_id INT NOT NULL,
    assignment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'SUCCESS', -- To track if it was a successful assignment log
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (courier_id) REFERENCES couriers(id),
    UNIQUE KEY unique_order_assignment (order_id) -- Ensure one order isn't assigned twice active
);
