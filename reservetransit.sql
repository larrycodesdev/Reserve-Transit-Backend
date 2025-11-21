-- ------------------------------------------------------------
--  RESERVETRANSIT DATABASE STRUCTURE
--  Compatible with MySQL 5.7+ / 8+
-- ------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS reservetransit;
USE reservetransit;

-- ------------------------------------------------------------
-- 1. admin_users
-- ------------------------------------------------------------
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin') NOT NULL DEFAULT 'admin',
    active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 2. branches
-- ------------------------------------------------------------
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    state VARCHAR(100),
    lga VARCHAR(100),
    active TINYINT(1) DEFAULT 1,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id)
);

-- ------------------------------------------------------------
-- 3. vehicles
-- ------------------------------------------------------------
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate_number VARCHAR(100) NOT NULL UNIQUE,
    model VARCHAR(255),
    seat_count INT NOT NULL DEFAULT 14,
    active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 4. trips
-- ------------------------------------------------------------
CREATE TABLE trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    origin_branch_id INT NOT NULL,
    destination_branch_id INT NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('scheduled','departed','completed','canceled') DEFAULT 'scheduled',
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (origin_branch_id) REFERENCES branches(id),
    FOREIGN KEY (destination_branch_id) REFERENCES branches(id),
    FOREIGN KEY (created_by) REFERENCES admin_users(id)
);

-- ------------------------------------------------------------
-- 5. passengers
-- ------------------------------------------------------------
CREATE TABLE passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 6. bookings
-- ------------------------------------------------------------
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    passenger_id INT NOT NULL,
    seat_number INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','paid','canceled','refunded') DEFAULT 'pending',
    reference VARCHAR(255) UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id),
    FOREIGN KEY (passenger_id) REFERENCES passengers(id)
);

-- ------------------------------------------------------------
-- 7. payments
-- ------------------------------------------------------------
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    provider ENUM('paystack','flutterwave','manual') NOT NULL,
    status ENUM('pending','success','failed') NOT NULL DEFAULT 'pending',
    reference VARCHAR(255) UNIQUE NOT NULL,
    paid_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- ------------------------------------------------------------
-- 8. daily_operations
-- ------------------------------------------------------------
CREATE TABLE daily_operations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    operation_date DATE NOT NULL,
    sold_tickets INT DEFAULT 0,
    total_sales DECIMAL(12,2) DEFAULT 0,
    expenses DECIMAL(12,2) DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (created_by) REFERENCES admin_users(id)
);

-- ------------------------------------------------------------
-- 9. finance_logs
-- ------------------------------------------------------------
CREATE TABLE finance_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('credit','debit') NOT NULL,
    description TEXT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id)
);

-- ------------------------------------------------------------
-- 10. audit_logs
-- ------------------------------------------------------------
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id)
);
