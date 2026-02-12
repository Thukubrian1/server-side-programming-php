<?php
/**
 * Database Configuration for Ticket System
 * KyU IT Support Platform
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP MySQL password is empty
define('DB_NAME', 'ticket_system_db');

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

// System configuration
define('IT_SUPPORT_EMAIL', 'support@kyu.ac.ke');
define('SYSTEM_NAME', 'KyU IT Support Ticket Triage');
define('UNIVERSITY_NAME', 'Kirinyaga University');
define('IT_DEPARTMENT', 'IT Department');
define('STANDARD_RESPONSE_TIME', '24 hours');
define('URGENT_RESPONSE_TIME', 'Immediate');

$serverVersion = "XAMPP 8.2";

// Priority mapping
function getPriorityByCategory($category) {
    $priorities = [
        'Hardware' => 'High',
        'Software' => 'Medium',
        'Network' => 'High'
    ];
    return $priorities[$category] ?? 'Medium';
}

// Response time mapping
function getResponseTimeByCategory($category) {
    return ($category === 'Network') ? URGENT_RESPONSE_TIME : STANDARD_RESPONSE_TIME;
}
?>