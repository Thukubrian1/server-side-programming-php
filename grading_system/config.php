<?php
/**
 * Database Configuration for Grading System
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

// Grade constants
define('PASSMARK', 40);
define('UNIT_CODE', 'SSE 2304');
define('CAT1_WEIGHT', 15);
define('CAT2_WEIGHT', 15);
define('ASSIGNMENT_WEIGHT', 10);
define('TOTAL_INTERNAL_MARKS', 40);
define('LECTURER_EMAIL', 'swanjau@kyu.ac.ke');

$unitTitle = "Server-Side Programming";
?>