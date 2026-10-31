-- Run this once in phpMyAdmin (Import tab) or:  mysql -u root -p < schema.sql
CREATE DATABASE IF NOT EXISTS chatapp
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chatapp;

-- Registered users
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    INT UNSIGNED NOT NULL,          -- UNIX time
  last_seen     INT UNSIGNED NOT NULL DEFAULT 0 -- UNIX time, used for the online dot
) ENGINE=InnoDB;

-- Chat messages. receiver_id NULL = message to the public group room.
CREATE TABLE IF NOT EXISTS messages (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  sender_id   INT UNSIGNED NOT NULL,
  receiver_id INT UNSIGNED NULL,
  body        VARCHAR(1000) NOT NULL,
  created_at  INT UNSIGNED NOT NULL,
  INDEX idx_receiver (receiver_id, id),
  INDEX idx_pair (sender_id, receiver_id, id),
  CONSTRAINT fk_sender   FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
