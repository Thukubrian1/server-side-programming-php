<?php
/**
 * Display Assessment Requirements
 * Shows assessment types and their weights using arrays and foreach loop
 */

require_once 'constants.php';

// Array to store assessment types and their maximum weights
$assessmentTypes = [
    'CAT 1' => 15,
    'CAT 2' => 15,
    'Assignments' => 10
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $unitTitle; ?> - Assessment Requirements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
            border-bottom: 3px solid #1976d2;
            padding-bottom: 10px;
        }
        .info-section {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            background-color: #f5f5f5;
            margin: 10px 0;
            padding: 15px;
            border-left: 4px solid #1976d2;
            font-size: 16px;
        }
        .weight {
            float: right;
            font-weight: bold;
            color: #1976d2;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $unitTitle; ?> (<?php echo UNIT_CODE; ?>)</h1>
        
        <div class="info-section">
            <h3>📊 Course Evaluation Criteria</h3>
            <p><strong>Total Internal Marks:</strong> <?php echo TOTAL_INTERNAL_MARKS; ?> marks</p>
            <p><strong>Passmark:</strong> <?php echo PASSMARK; ?>%</p>
        </div>
        
        <h2>Assessment Components</h2>
        <ul>
            <?php foreach ($assessmentTypes as $assessmentName => $maxWeight): ?>
                <li>
                    <?php echo $assessmentName; ?>
                    <span class="weight"><?php echo $maxWeight; ?> marks</span>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
            <strong>Note:</strong> To pass this unit, you need to score at least <?php echo (PASSMARK / 100) * TOTAL_INTERNAL_MARKS; ?> marks out of <?php echo TOTAL_INTERNAL_MARKS; ?> (<?php echo PASSMARK; ?>%).
        </div>
        
        <a href="input.php" class="btn">Calculate Your Grade</a>
    </div>
</body>
</html>