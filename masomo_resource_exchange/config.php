<?php
/**
 * Database Configuration for Masomo Exchange
 * KyU Resource Sharing Platform
 */

// Database credentials
define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');

// Create database connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to utf8mb4
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Course configuration
define('COURSE_TITLE', 'Server-Side Programming');
define('UNIT_CODE', 'SSE 2304');
define('SCHOOL_NAME', 'School of Pure & Applied Sciences');

$lecturerName = "Dr. S. Wanjau";
?>