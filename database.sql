-- Work Log Database Setup
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS worklog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE worklog_db;

CREATE TABLE IF NOT EXISTS work_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS work_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    details TEXT NOT NULL,
    task_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES work_sessions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS task_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES work_tasks(id) ON DELETE CASCADE
);
