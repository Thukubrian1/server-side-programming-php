<?php
/**
 * Grade Input Form
 * Collects student scores for internal assessments
 */

require_once 'constants.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Evaluation Tool - <?php echo UNIT_CODE; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1976d2;
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
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .score-range {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
        .btn-submit {
            background-color: #1976d2;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn-navigate{

        background-color: #1976d2;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;

        }

        .btn-submit:hover {
            background-color: #1565c0;
        }
        .info-box {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Lab Evaluation Tool</h1>
        <p class="subtitle"><?php echo $unitTitle; ?> (<?php echo UNIT_CODE; ?>)</p>
        
        <div class="info-box">
            <strong>Calculate Your Current Internal Marks</strong>
            <p style="margin: 10px 0 0 0; font-size: 14px;">
                Enter your scores for CAT 1, CAT 2, and Assignments to see your current standing.
            </p>
        </div>
        
        <form action="compute.php" method="POST">
            <div class="form-group">
                <label for="studentName">Student Name <span class="required">*</span></label>
                <input type="text" id="studentName" name="studentName" 
                       placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="cat1Score">CAT 1 Score <span class="required">*</span></label>
                <input type="number" id="cat1Score" name="cat1Score" 
                       min="0" max="15" step="0.1" 
                       placeholder="0 - 15" required>
                <div class="score-range">Maximum: <?php echo CAT1_WEIGHT; ?> marks</div>
            </div>
            
            <div class="form-group">
                <label for="cat2Score">CAT 2 Score <span class="required">*</span></label>
                <input type="number" id="cat2Score" name="cat2Score" 
                       min="0" max="15" step="0.1" 
                       placeholder="0 - 15" required>
                <div class="score-range">Maximum: <?php echo CAT2_WEIGHT; ?> marks</div>
            </div>
            
            <div class="form-group">
                <label for="assignmentScore">Assignment Score <span class="required">*</span></label>
                <input type="number" id="assignmentScore" name="assignmentScore" 
                       min="0" max="10" step="0.1" 
                       placeholder="0 - 10" required>
                <div class="score-range">Maximum: <?php echo ASSIGNMENT_WEIGHT; ?> marks</div>
            </div>
            
            <button type="submit" class="btn-submit">Calculate Total Score</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">

        <button type="submit" class="btn-navigate"><a href="view_grades.php" >View All Grades</a></button><br><br>

            <a href="assessment_info.php" style="color: #1976d2;">View Assessment Requirements</a>
        </div>
    </div>
</body>
</html>