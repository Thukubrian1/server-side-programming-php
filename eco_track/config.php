<?php
// Define configuration variables
$universityName = "Kirinyaga University";
$unitCode = "SSE 2304";
$currentYear = 2024;

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

// Test connection function
function testConnection() {
    $conn = getDBConnection();
    if ($conn) {
        return true;
    }
    return false;
}
?>