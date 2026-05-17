-- ================================================
-- Customer Management System v2 - Database Setup
-- Run this file in phpMyAdmin SQL tab
-- ================================================

CREATE DATABASE IF NOT EXISTS customer_management;
USE customer_management;

-- ─── USERS TABLE (Login System) ───
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','user') DEFAULT 'user',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin user (password: admin123)
INSERT INTO users (name, username, password, role) VALUES
('Administrator', 'admin', MD5('admin123'), 'admin'),
('Nikhil Sarvaliya', 'nikhil', MD5('nikhil123'), 'user');

-- ─── CUSTOMERS TABLE ───
CREATE TABLE IF NOT EXISTS customers (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    customer_code   VARCHAR(50) NOT NULL UNIQUE,
    name            VARCHAR(100) NOT NULL,
    addr1           VARCHAR(200),
    addr2           VARCHAR(200),
    city            VARCHAR(100),
    pincode         VARCHAR(20),
    state           VARCHAR(100),
    country         VARCHAR(100) DEFAULT 'India',
    contact_person  VARCHAR(100),
    contact_number  VARCHAR(20),
    email           VARCHAR(150),
    gstin           VARCHAR(20),
    photo           VARCHAR(255),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─── QUOTATIONS TABLE ───
CREATE TABLE IF NOT EXISTS quotations (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT NOT NULL,
    quotation_date  DATE NOT NULL,
    total_amount    DECIMAL(10,2) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- ─── QUOTATION ITEMS TABLE ───
CREATE TABLE IF NOT EXISTS quotation_items (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id    INT NOT NULL,
    product_name    VARCHAR(200) NOT NULL,
    qty             INT NOT NULL,
    price           DECIMAL(10,2) NOT NULL,
    total           DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
);

-- ─── SAMPLE CUSTOMERS ───
INSERT INTO customers (customer_code, name, addr1, addr2, city, pincode, state, country, contact_person, contact_number, email, gstin) VALUES
('CUST001', 'Raj Enterprises', '123 MG Road', 'Near Station', 'Rajkot', '360001', 'Gujarat', 'India', 'Rajesh Shah', '9876543210', 'raj@rajenterprises.com', '24ABCDE1234F1Z5'),
('CUST002', 'Modi Traders', '45 Industrial Area', '', 'Surat', '395003', 'Gujarat', 'India', 'Amit Modi', '9988776655', 'amit@moditraders.com', '24XYZAB5678G2Z1'),
('CUST003', 'Patel Solutions', '78 Ring Road', 'Block B', 'Ahmedabad', '380015', 'Gujarat', 'India', 'Priya Patel', '9765432108', 'priya@patelsolutions.com', '24LMNOP9012H3Z7'),
('CUST004', 'Shah Industries', '90 GIDC Estate', '', 'Vadodara', '390010', 'Gujarat', 'India', 'Ravi Shah', '9654321087', 'ravi@shahindustries.com', '24QRSTU3456I4Z9'),
('CUST005', 'Mehta & Co', '12 Commercial Zone', 'Floor 2', 'Rajkot', '360002', 'Gujarat', 'India', 'Sonal Mehta', '9543210976', 'sonal@mehtaco.com', '24VWXYZ7890J5Z3');
