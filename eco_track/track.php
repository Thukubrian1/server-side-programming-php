<?php
/**
 * Data Collection Form for Eco-Track Portal
 */
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Sustainability Impact</title>
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
            margin-bottom: 10px;
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
            min-height: 100px;
        }
        .btn-submit {
            background-color: #2c5f2d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #1e4620;
        }
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌿 Track Your Sustainability Impact</h1>
        <p>Help us make <?php echo $universityName; ?> greener by tracking your eco-friendly actions!</p>
        
        <form action="process.php" method="POST">
            <div class="form-group">
                <label for="fullName">Full Name <span class="required">*</span></label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            
            <div class="form-group">
                <label for="studentId">Student ID <span class="required">*</span></label>
                <input type="text" id="studentId" name="studentId" required>
            </div>
            
            <div class="form-group">
                <label for="actionCategory">Action Category <span class="required">*</span></label>
                <select id="actionCategory" name="actionCategory" required>
                    <option value="">-- Select an Action --</option>
                    <option value="Tree Planting">Tree Planting</option>
                    <option value="Waste Reduction">Waste Reduction</option>
                    <option value="Energy Saving">Energy Saving</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="impactDescription">Impact Description <span class="required">*</span></label>
                <textarea id="impactDescription" name="impactDescription" 
                          placeholder="Describe your sustainability action in detail (minimum 20 characters)" 
                          required></textarea>
                <small style="color: #666;">Please provide at least 20 characters describing your impact.</small>
            </div>
            
            <button type="submit" class="btn-submit">Submit Impact Report</button>
        </form>
        
        <div style="margin-top: 20px;">
            <a href="welcome.php" style="color: #2c5f2d;">← Back to Home</a>
        </div>
    </div>
</body>
</html>