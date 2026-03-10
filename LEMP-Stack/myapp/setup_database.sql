-- =============================================================
-- MariaDB Setup Script
-- Run as root: mysql -u root -p < setup_database.sql
-- =============================================================

-- Create the database
CREATE DATABASE IF NOT EXISTS my_database
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Create the user (connect it to db_connect)
CREATE USER IF NOT EXISTS 'my_user'@'localhost' IDENTIFIED BY 'my_password';

-- Grant all privileges on my_database to my_user
GRANT ALL PRIVILEGES ON my_database.* TO 'my_user'@'localhost';

-- Apply privilege changes immediately
FLUSH PRIVILEGES;

-- Switch to the new database
USE my_database;

-- Create the contacts table
CREATE TABLE IF NOT EXISTS contacts (
    id    INT          NOT NULL AUTO_INCREMENT,
    name  VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30)  NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
