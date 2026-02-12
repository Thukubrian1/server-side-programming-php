<?php
/**
 * Configuration File for Eco-Track Student Sustainability Portal
 * Kirinyaga University
 */

// Define configuration variables
$universityName = "Kirinyaga University";
$unitCode = "SSE 2304";
$currentYear = 2024;

/**
 * Database Configuration for Eco-Track Portal
 * Kirinyaga University
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'eco_track_db');

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

// University configuration
$universityName = "Kirinyaga University";
$unitCode = "SSE 2304";
$currentYear = 2024;
?>