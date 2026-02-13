<?php

require_once 'config.php';

date_default_timezone_set('Africa/Nairobi');

// Get current timestamp
$currentDateTime = date('l, F j, Y - g:i A');

$currentHour = (int)date('G');
$systemStatus = "";

if ($currentHour >= 0 && $currentHour < 2) {
    $systemStatus = "System Maintenance";
    $statusClass = "maintenance";
} else {
    $systemStatus = "System Active";
    $statusClass = "active";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco-Track Portal - Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5f2d;
            margin-bottom: 10px;
        }
        .welcome-message {
            font-size: 18px;
            color: #333;
            margin: 20px 0;
        }
        .timestamp {
            color: #666;
            font-style: italic;
        }
        .status {
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .active {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .maintenance {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c5f2d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #1e4620;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌱 Eco-Track Sustainability Portal</h1>
        
        <div class="welcome-message">
            <p><strong>Welcome to <?php echo $universityName; ?>!</strong></p>
            <p>Track your carbon footprint reduction and contribute to a sustainable future.</p>
            <p class="timestamp">Your visit: <?php echo $currentDateTime; ?></p>
        </div>
        
        <div class="status <?php echo $statusClass; ?>">
            Status: <?php echo $systemStatus; ?>
        </div>
        
        <?php if ($systemStatus === "System Active"): ?>
            <a href="track.php" class="btn">Start Tracking Your Impact</a>
        <?php else: ?>
            <p>The system is currently undergoing maintenance. Please check back after 2:00 AM.</p>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><small>Unit Code: <?php echo $unitCode; ?> | Year: <?php echo $currentYear; ?></small></p>
        </div>
    </div>
</body>
</html>