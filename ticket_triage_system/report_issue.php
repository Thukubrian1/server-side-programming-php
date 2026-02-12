<?php
/**
 * Issue Report Form
 * Captures technical support tickets from students and staff
 */

require_once 'setup.php';

// Multidimensional array for Issue Categories and Priority Levels
$issueCategories = [
    ['category' => 'Hardware', 'priority' => 'High'],
    ['category' => 'Software', 'priority' => 'Medium'],
    ['category' => 'Network', 'priority' => 'High']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issue - <?php echo SYSTEM_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
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
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        .required {
            color: red;
        }
        .btn-submit {
            background-color: #d32f2f;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #b71c1c;
        }
        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .helper-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Report an Issue</h1>
        <p class="subtitle"><?php echo SYSTEM_NAME; ?></p>
        
        <div class="info-box">
            <strong>⚠️ Important:</strong> Please provide detailed information about your issue to help us resolve it quickly. Tickets are prioritized based on category and urgency.
        </div>
        
        <form action="validate.php" method="POST">
            <div class="form-group">
                <label for="reporterName">Reporter's Name <span class="required">*</span></label>
                <input type="text" id="reporterName" name="reporterName" 
                       placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="department">Department <span class="required">*</span></label>
                <input type="text" id="department" name="department" 
                       placeholder="e.g., Pure & Applied Sciences" required>
            </div>
            
            <div class="form-group">
                <label for="issueCategory">Issue Category <span class="required">*</span></label>
                <select id="issueCategory" name="issueCategory" required>
                    <option value="">-- Select Issue Category --</option>
                    <?php foreach ($issueCategories as $issue): ?>
                        <option value="<?php echo $issue['category']; ?>">
                            <?php echo $issue['category']; ?> (Priority: <?php echo $issue['priority']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="detailedDescription">Detailed Description <span class="required">*</span></label>
                <textarea id="detailedDescription" name="detailedDescription" 
                          placeholder="Describe the issue in detail (minimum 30 characters)" 
                          required></textarea>
                <div class="helper-text">
                    Please provide at least 30 characters to help us understand the issue better. Include:
                    <ul style="margin: 5px 0; padding-left: 20px; font-size: 12px;">
                        <li>What happened?</li>
                        <li>When did it start?</li>
                        <li>Any error messages?</li>
                    </ul>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Submit Ticket</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="categories.php" style="color: #d32f2f;">← View Issue Categories</a>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center;">
            For urgent issues, contact <?php echo IT_SUPPORT_EMAIL; ?> directly
        </div>
    </div>
</body>
</html>