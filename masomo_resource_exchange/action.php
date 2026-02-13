<?php

date_default_timezone_set('Africa/Nairobi');

require_once 'config.php';

// Initialize variables
$errors = [];
$success = false;
$resourceId = null;

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize form data
    $resourceTitle = isset($_POST['resourceTitle']) ? trim($_POST['resourceTitle']) : '';
    $resourceCategory = isset($_POST['resourceCategory']) ? trim($_POST['resourceCategory']) : '';
    $resourceUrl = isset($_POST['resourceUrl']) ? trim($_POST['resourceUrl']) : '';
    $contributorEmail = isset($_POST['contributorEmail']) ? trim($_POST['contributorEmail']) : '';
    
    // Data Validation
    
    // 1. Validate Email contains @ and ends with .ac.ke
    if (empty($contributorEmail)) {
        $errors[] = "Contributor Email is required.";
    } elseif (strpos($contributorEmail, '@') === false) {
        $errors[] = "Email must contain an @ symbol.";
    } elseif (!preg_match('/\.ac\.ke$/i', $contributorEmail)) {
        $errors[] = "Email must end with .ac.ke (KyU email addresses only).";
    }
    
    // 2. Validate Resource Title is at least 5 characters long
    if (empty($resourceTitle)) {
        $errors[] = "Resource Title is required.";
    } elseif (strlen($resourceTitle) < 5) {
        $errors[] = "Resource Title must be at least 5 characters long. Current length: " . strlen($resourceTitle) . " characters.";
    }
    
    // 3. Additional validations
    if (empty($resourceCategory)) {
        $errors[] = "Please select a Resource Category.";
    }
    
    if (empty($resourceUrl)) {
        $errors[] = "Resource URL is required.";
    } elseif (!filter_var($resourceUrl, FILTER_VALIDATE_URL)) {
        $errors[] = "Please enter a valid URL.";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        $conn = getDBConnection();
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO educational_resources (resource_title, resource_category, resource_url, contributor_email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $resourceTitle, $resourceCategory, $resourceUrl, $contributorEmail);
        
        if ($stmt->execute()) {
            $success = true;
            $resourceId = $conn->insert_id;
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
    <title>Submission Result - Masomo Exchange</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
        }
        .content {
            padding: 30px;
        }
        h2 {
            color: #667eea;
        }
        .error-box {
            background-color: #ffe6e6;
            color: #d32f2f;
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
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .summary-table th {
            background-color: #667eea;
            color: white;
        }
        .summary-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #5568d3;
        }
        .resource-id {
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
        <?php include 'header.php'; ?>
        
        <div class="content">
            <?php if ($success): ?>
                <!-- Success Message -->
                <h2>✅ Submission Successful!</h2>
                <div class="success-box">
                    <p><strong>Thank you for contributing to the Masomo Resource Exchange!</strong></p>
                    <p>Your resource has been successfully saved to the database and is now available to other students.</p>
                </div>
                
                <div class="resource-id">
                    📚 Resource ID: #<?php echo str_pad($resourceId, 6, '0', STR_PAD_LEFT); ?>
                </div>
                
                <!-- Data Summary in HTML Table -->
                <h3>Resource Summary</h3>
                <table class="summary-table">
                    <tr>
                        <th>Field</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td><strong>Resource Title</strong></td>
                        <td><?php echo htmlspecialchars($resourceTitle); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Category</strong></td>
                        <td><?php echo htmlspecialchars($resourceCategory); ?></td>
                    </tr>
                    <tr>
                        <td><strong>URL</strong></td>
                        <td><a href="<?php echo htmlspecialchars($resourceUrl); ?>" target="_blank">
                            <?php echo htmlspecialchars($resourceUrl); ?>
                        </a></td>
                    </tr>
                    <tr>
                        <td><strong>Contributor Email</strong></td>
                        <td><?php echo htmlspecialchars($contributorEmail); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Submission Date</strong></td>
                        <td><?php echo date('F j, Y - g:i A'); ?></td>
                    </tr>
                </table>
                
            <?php elseif (!empty($errors)): ?>
                <!-- Error Messages in Red Text -->
                <h2>❌ Validation Errors</h2>
                <div class="error-box">
                    <p><strong>Please correct the following errors:</strong></p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li style="color: red;"><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
            <?php else: ?>
                <!-- Invalid Access -->
                <h2>Invalid Access</h2>
                <p>This page must be accessed through the resource submission form.</p>
            <?php endif; ?>
            
            <a href="upload_form.php" class="btn">Submit Another Resource</a>
            <a href="browse_resources.php" class="btn" style="background-color: #764ba2; margin-left: 10px;">Browse Resources</a>
            <a href="index.php" class="btn" style="background-color: #666; margin-left: 10px;">Back to Home</a>
        </div>
    </div>
</body>
</html>