<?php
// Database settings - change these to match your MySQL / XAMPP setup.
// XAMPP default: user "root" with an empty password.
define('DB_HOST', getenv('CHAT_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('CHAT_DB_NAME') ?: 'chatapp');
define('DB_USER', getenv('CHAT_DB_USER') ?: 'root');
define('DB_PASS', getenv('CHAT_DB_PASS') ?: '');
// Optional full DSN override (only used for testing with another database).
define('DB_DSN',  getenv('CHAT_DSN') ?: '');
