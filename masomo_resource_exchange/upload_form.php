<?php
/**
 * Resource Upload Form
 * KyU Masomo Resource Exchange
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Resource - Masomo Exchange</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: white;
        }
        .content {
            padding: 30px;
        }
        h2 {
            color: #667eea;
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
        input[type="url"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .required {
            color: red;
        }
        .btn-submit {
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #5568d3;
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
        <?php include 'header.php'; ?>
        
        <div class="content">
            <h2>📤 Submit an Educational Resource</h2>
            <p>Share helpful learning materials with your fellow students!</p>
            
            <form action="action.php" method="POST">
                <div class="form-group">
                    <label for="resourceTitle">Resource Title <span class="required">*</span></label>
                    <input type="text" id="resourceTitle" name="resourceTitle" 
                           placeholder="e.g., PHP Arrays Tutorial" required>
                    <div class="helper-text">Minimum 5 characters required</div>
                </div>
                
                <div class="form-group">
                    <label for="resourceCategory">Resource Category <span class="required">*</span></label>
                    <select id="resourceCategory" name="resourceCategory" required>
                        <option value="">-- Select Category --</option>
                        <option value="Notes">Notes</option>
                        <option value="Video">Video</option>
                        <option value="Source Code">Source Code</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="resourceUrl">URL <span class="required">*</span></label>
                    <input type="url" id="resourceUrl" name="resourceUrl" 
                           placeholder="https://example.com/resource" required>
                    <div class="helper-text">Enter the full URL of the resource</div>
                </div>
                
                <div class="form-group">
                    <label for="contributorEmail">Contributor Email <span class="required">*</span></label>
                    <input type="email" id="contributorEmail" name="contributorEmail" 
                           placeholder="student@kyu.ac.ke" required>
                    <div class="helper-text">Must be a valid KyU email address ending with @kyu.ac.ke</div>
                </div>
                
                <button type="submit" class="btn-submit">Submit Resource</button>
                <a href="index.php" style="margin-left: 15px; color: #667eea;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>