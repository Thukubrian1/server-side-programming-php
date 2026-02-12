<?php
/**
 * Header Configuration File
 * KyU Masomo Resource Exchange
 */

// Define constant for course title
define('COURSE_TITLE', 'Server-Side Programming');

// Define variable for lecturer's name
$lecturerName = "Dr. S. Wanjau"; // As per course outline

// Optional: Additional configuration
define('UNIT_CODE', 'SSE 2304');
define('SCHOOL_NAME', 'School of Pure & Applied Sciences');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 KyU Masomo Resource Exchange</h1>
        <p><?php echo COURSE_TITLE; ?> (<?php echo UNIT_CODE; ?>)</p>
        <p>Lecturer: <?php echo $lecturerName; ?> | <?php echo SCHOOL_NAME; ?></p>
    </div>