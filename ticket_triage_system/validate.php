<?php

date_default_timezone_set('Africa/Nairobi');

require_once 'config.php';

// Initialize variables
$errors = [];
$success = false;
$ticketNumber = '';

// Sanitized data variables
$reporterName = '';
$department = '';
$issueCategory = '';
$detailedDescription = '';
$priority = '';
$responseTime = '';

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize form data using htmlspecialchars() to prevent XSS
    $reporterName = isset($_POST['reporterName']) ? htmlspecialchars(trim($_POST['reporterName']), ENT_QUOTES, 'UTF-8') : '';
    $department = isset($_POST['department']) ? htmlspecialchars(trim($_POST['department']), ENT_QUOTES, 'UTF-8') : '';
    $issueCategory = isset($_POST['issueCategory']) ? htmlspecialchars(trim($_POST['issueCategory']), ENT_QUOTES, 'UTF-8') : '';
    $detailedDescription = isset($_POST['detailedDescription']) ? htmlspecialchars(trim($_POST['detailedDescription']), ENT_QUOTES, 'UTF-8') : '';
    
    // Server-Side Validation
    
    // 1. Check that reporter's name is not blank
    if (empty($reporterName)) {
        $errors[] = "Reporter's Name is required and cannot be left blank.";
    }
    
    // 2. Ensure Detailed Description is at least 30 characters long
    if (empty($detailedDescription)) {
        $errors[] = "Detailed Description is required.";
    } elseif (strlen($detailedDescription) < 30) {
        $errors[] = "Detailed Description must be at least 30 characters long to prevent vague reports. Current length: " . strlen($detailedDescription) . " characters.";
    }
    
    // 3. Additional validations
    if (empty($department)) {
        $errors[] = "Department is required.";
    }
    
    if (empty($issueCategory)) {
        $errors[] = "Please select an Issue Category.";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        // Generate ticket number
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        
        // Get priority and response time based on category
        $priority = getPriorityByCategory($issueCategory);
        $responseTime = getResponseTimeByCategory($issueCategory);
        
        // Save to database
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO support_tickets (ticket_number, reporter_name, department, issue_category, detailed_description, priority, response_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $ticketNumber, $reporterName, $department, $issueCategory, $detailedDescription, $priority, $responseTime);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Submission Result - <?php echo SYSTEM_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d32f2f;
        }
        .error-box {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #d32f2f;
            margin: 20px 0;
        }
        .error-box ul {
            margin: 10px 0 0 20px;
        }
        .success-box {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4caf50;
            margin: 20px 0;
        }
        .alert-box {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
            font-weight: bold;
        }
        .ticket-summary {
            background-color: #fafafa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .ticket-summary h3 {
            margin-top: 0;
            color: #d32f2f;
        }
        .summary-item {
            margin: 15px 0;
            padding: 10px;
            background-color: white;
            border-left: 3px solid #d32f2f;
        }
        .summary-item strong {
            display: block;
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #d32f2f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #b71c1c;
        }
        .ticket-number {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            color: #1976d2;
        }
        .priority-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .priority-high {
            background-color: #ffcdd2;
            color: #c62828;
        }
        .priority-medium {
            background-color: #fff9c4;
            color: #f57f17;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <!-- Success Message -->
            <h1>✅ Ticket Submitted Successfully!</h1>
            <div class="success-box">
                <p><strong>Thank you for reporting this issue.</strong></p>
                <p>Your support ticket has been saved and will be processed by our IT team.</p>
            </div>
            
            <div class="ticket-number">
                🎫 Ticket Number: <?php echo $ticketNumber; ?>
            </div>
            
            <!-- Logic Implementation: Display message based on Issue Category -->
            <?php if ($issueCategory === 'Network'): ?>
                <div class="alert-box">
                    🚨 A technician has been alerted for immediate campus Wi-Fi inspection.
                </div>
            <?php else: ?>
                <div class="alert-box">
                    📋 Ticket received. Expected response time: <?php echo STANDARD_RESPONSE_TIME; ?>.
                </div>
            <?php endif; ?>
            
            <!-- Ticket Summary -->
            <div class="ticket-summary">
                <h3>📄 Ticket Details</h3>
                
                <div class="summary-item">
                    <strong>REPORTER'S NAME</strong>
                    <?php echo $reporterName; ?>
                </div>
                
                <div class="summary-item">
                    <strong>DEPARTMENT</strong>
                    <?php echo $department; ?>
                </div>
                
                <div class="summary-item">
                    <strong>ISSUE CATEGORY</strong>
                    <?php echo $issueCategory; ?>
                </div>
                
                <div class="summary-item">
                    <strong>PRIORITY LEVEL</strong>
                    <span class="priority-badge priority-<?php echo strtolower($priority); ?>">
                        <?php echo $priority; ?> Priority
                    </span>
                </div>
                
                <div class="summary-item">
                    <strong>DETAILED DESCRIPTION</strong>
                    <?php echo nl2br($detailedDescription); ?>
                </div>
                
                <div class="summary-item">
                    <strong>SUBMISSION TIME</strong>
                    <?php echo date('l, F j, Y - g:i A'); ?>
                </div>
                
                <div class="summary-item">
                    <strong>EXPECTED RESPONSE TIME</strong>
                    <?php echo $responseTime; ?>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background-color: #e3f2fd; border-radius: 5px;">
                <strong>Next Steps:</strong>
                <ul style="margin: 10px 0;">
                    <li>You will receive an email confirmation at your university email</li>
                    <li>Our IT team will review your ticket and respond accordingly</li>
                    <li>For urgent matters, contact <?php echo IT_SUPPORT_EMAIL; ?></li>
                    <li>Please reference ticket number: <strong><?php echo $ticketNumber; ?></strong></li>
                </ul>
            </div>
            
        <?php elseif (!empty($errors)): ?>
            <!-- Error Messages -->
            <h1>❌ Validation Error</h1>
            <div class="error-box">
                <p><strong>Please correct the following errors:</strong></p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
        <?php else: ?>
            <!-- Invalid Access -->
            <h1>Invalid Access</h1>
            <p>This page must be accessed through the ticket submission form.</p>
        <?php endif; ?>
        
        <a href="report_issue.php" class="btn">Submit Another Ticket</a>
        <a href="view_tickets.php" class="btn" style="background-color: #1976d2; margin-left: 10px;">View All Tickets</a>
        <a href="categories.php" class="btn" style="background-color: #666; margin-left: 10px;">Back to Categories</a>
    </div>
</body>
</html>