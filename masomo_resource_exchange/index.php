<?php
/**
 * Index Page with Day-Based Messages
 * KyU Masomo Resource Exchange
 */

// Get current day of the week
$currentDay = date('l'); // Returns full day name: Monday, Tuesday, etc.

// Determine message based on current day using switch statement
$dayMessage = "";

switch ($currentDay) {
    case 'Monday':
        $dayMessage = "Start of the Week - Review Weekend Reading Materials!";
        break;
    case 'Tuesday':
        $dayMessage = "Core Syntax Lab Today!";
        break;
    case 'Wednesday':
        $dayMessage = "Mid-Week Progress Check - Keep Coding!";
        break;
    case 'Thursday':
        $dayMessage = "Practical Session Day - Debug and Test!";
        break;
    case 'Friday':
        $dayMessage = "End of Week - Submit Your Assignments!";
        break;
    case 'Saturday':
        $dayMessage = "Weekend Study - Catch Up on PHP Tutorials!";
        break;
    case 'Sunday':
        $dayMessage = "Rest and Prepare for the Week Ahead!";
        break;
    default:
        $dayMessage = "Welcome to Masomo Resource Exchange!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KyU Masomo Resource Exchange</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
        }
        .content {
            padding: 30px;
        }
        .day-banner {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .welcome-section {
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 10px 10px 0;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #5568d3;
        }
        .info-box {
            background-color: #e8f4f8;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div class="content">
            <div class="day-banner">
                📅 <?php echo $currentDay; ?>: <?php echo $dayMessage; ?>
            </div>
            
            <div class="welcome-section">
                <h2>Welcome to the Resource Exchange!</h2>
                <p>Share and discover educational resources including PDFs, YouTube tutorials, and source code from fellow students at Kirinyaga University.</p>
                
                <div class="info-box">
                    <strong>Current Date & Time:</strong> <?php echo date('l, F j, Y - g:i A'); ?>
                </div>
            </div>
            
            <div style="margin: 30px 0;">
                <h3>Quick Actions</h3>
                <a href="upload_form.php" class="btn">📤 Submit a Resource</a>
                <a href="browse_resources.php" class="btn" style="background-color: #764ba2;">📖 Browse Resources</a>
            </div>
            
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
                <h3>About This Platform</h3>
                <p>The Masomo Resource Exchange is a collaborative platform where students can share educational materials to support each other's learning journey in <?php echo COURSE_TITLE; ?>.</p>
            </div>
        </div>
    </div>
</body>
</html>