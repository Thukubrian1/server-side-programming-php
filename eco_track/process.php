<?php
/**
 * Process Eco-Track Form Submissions with Database Integration
 * Handles POST data with server-side validation and MySQL storage
 */

require_once 'config.php';

// Initialize error array
$errors = [];
$success = false;
$submissionId = null;

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve form data
    $fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
    $studentId = isset($_POST['studentId']) ? trim($_POST['studentId']) : '';
    $actionCategory = isset($_POST['actionCategory']) ? trim($_POST['actionCategory']) : '';
    $impactDescription = isset($_POST['impactDescription']) ? trim($_POST['impactDescription']) : '';
    
    // Server-Side Validation
    
    // 1. Validate Student ID is not empty
    if (empty($studentId)) {
        $errors[] = "Student ID is required and cannot be empty.";
    }
    
    // 2. Validate Impact Description has at least 20 characters
    if (strlen($impactDescription) < 20) {
        $errors[] = "Impact Description must contain at least 20 characters. Current length: " . strlen($impactDescription) . " characters.";
    }
    
    // 3. Additional validation checks
    if (empty($fullName)) {
        $errors[] = "Full Name is required.";
    }
    
    if (empty($actionCategory)) {
        $errors[] = "Please select an Action Category.";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        $conn = getDBConnection();
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO sustainability_submissions (full_name, student_id, action_category, impact_description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullName, $studentId, $actionCategory, $impactDescription);
        
        if ($stmt->execute()) {
            $success = true;
            $submissionId = $conn->insert_id;
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
    <title>Submission Result - Eco-Track</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
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
        }
        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin: 20px 0;
        }
        .error-box ul {
            margin: 10px 0 0 20px;
        }
        .success-box {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .summary-table th {
            background-color: #2c5f2d;
            color: white;
        }
        .summary-table tr:hover {
            background-color: #f5f5f5;
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
        .btn-secondary {
            background-color: #666;
            margin-left: 10px;
        }
        .submission-id {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <!-- Success Message -->
            <h1>✅ Submission Successful!</h1>
            <div class="success-box">
                <p><strong>Thank you for contributing to a sustainable campus!</strong></p>
                <p>Your sustainability action has been recorded.</p>
            </div>
            
            <div class="submission-id">
                📋 Submission ID: #<?php echo str_pad($submissionId, 6, '0', STR_PAD_LEFT); ?>
            </div>
            
            <!-- Data Summary -->
            <h2>Submission Summary</h2>
            <table class="summary-table">
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td><strong>Full Name</strong></td>
                    <td><?php echo htmlspecialchars($fullName); ?></td>
                </tr>
                <tr>
                    <td><strong>Student ID</strong></td>
                    <td><?php echo htmlspecialchars($studentId); ?></td>
                </tr>
                <tr>
                    <td><strong>Action Category</strong></td>
                    <td><?php echo htmlspecialchars($actionCategory); ?></td>
                </tr>
                <tr>
                    <td><strong>Impact Description</strong></td>
                    <td><?php echo htmlspecialchars($impactDescription); ?></td>
                </tr>
                <tr>
                    <td><strong>Submission Time</strong></td>
                    <td><?php echo date('F j, Y - g:i A'); ?></td>
                </tr>
            </table>
            
        <?php elseif (!empty($errors)): ?>
            <!-- Error Messages -->
            <h1>❌ Validation Error</h1>
            <div class="error-box">
                <p><strong>Please correct the following errors:</strong></p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
        <?php else: ?>
            <!-- Invalid Access -->
            <h1>Invalid Access</h1>
            <p>This page must be accessed through form submission.</p>
        <?php endif; ?>
        
        <a href="track.php" class="btn">Submit Another Entry</a>
        <a href="view_submissions.php" class="btn btn-secondary">View All Submissions</a>
        <a href="welcome.php" class="btn btn-secondary">Back to Home</a>
    </div>
</body>
</html>