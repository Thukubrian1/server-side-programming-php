<?php
/**
 * Compute and Display Student Grades with Database Integration
 * Processes scores with validation and MySQL storage
 */

require_once 'config.php';

// Initialize variables
$errors = [];
$totalScore = 0;
$studentName = '';
$gradeId = null;
$success = false;

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve form data
    $studentName = isset($_POST['studentName']) ? trim($_POST['studentName']) : '';
    $cat1Score = isset($_POST['cat1Score']) ? $_POST['cat1Score'] : '';
    $cat2Score = isset($_POST['cat2Score']) ? $_POST['cat2Score'] : '';
    $assignmentScore = isset($_POST['assignmentScore']) ? $_POST['assignmentScore'] : '';
    
    // Server-Side Validation
    
    // 1. Verify all inputs are numeric
    if (!is_numeric($cat1Score)) {
        $errors[] = "CAT 1 Score must be a valid number.";
    }
    if (!is_numeric($cat2Score)) {
        $errors[] = "CAT 2 Score must be a valid number.";
    }
    if (!is_numeric($assignmentScore)) {
        $errors[] = "Assignment Score must be a valid number.";
    }
    
    // 2. Ensure no score exceeds its maximum weight
    if (is_numeric($cat1Score) && $cat1Score > CAT1_WEIGHT) {
        $errors[] = "CAT 1 Score cannot exceed " . CAT1_WEIGHT . " marks. You entered: $cat1Score";
    }
    if (is_numeric($cat2Score) && $cat2Score > CAT2_WEIGHT) {
        $errors[] = "CAT 2 Score cannot exceed " . CAT2_WEIGHT . " marks. You entered: $cat2Score";
    }
    if (is_numeric($assignmentScore) && $assignmentScore > ASSIGNMENT_WEIGHT) {
        $errors[] = "Assignment Score cannot exceed " . ASSIGNMENT_WEIGHT . " marks. You entered: $assignmentScore";
    }
    
    // 3. Validate scores are not negative
    if (is_numeric($cat1Score) && $cat1Score < 0) {
        $errors[] = "CAT 1 Score cannot be negative.";
    }
    if (is_numeric($cat2Score) && $cat2Score < 0) {
        $errors[] = "CAT 2 Score cannot be negative.";
    }
    if (is_numeric($assignmentScore) && $assignmentScore < 0) {
        $errors[] = "Assignment Score cannot be negative.";
    }
    
    // 4. Validate student name
    if (empty($studentName)) {
        $errors[] = "Student Name is required.";
    }
    
    // Calculate total score and save to database if no errors
    if (empty($errors)) {
        $totalScore = floatval($cat1Score) + floatval($cat2Score) + floatval($assignmentScore);
        $passingScore = (PASSMARK / 100) * TOTAL_INTERNAL_MARKS;
        $status = ($totalScore >= $passingScore) ? 'Pass' : 'Consult';
        
        // Save to database
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO student_grades (student_name, cat1_score, cat2_score, assignment_score, total_score, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdddds", $studentName, $cat1Score, $cat2Score, $assignmentScore, $totalScore, $status);
        
        if ($stmt->execute()) {
            $success = true;
            $gradeId = $conn->insert_id;
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
    <title>Grade Calculation Result - <?php echo UNIT_CODE; ?></title>
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
            margin-bottom: 10px;
        }
        .error-box {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #c62828;
            margin: 20px 0;
        }
        .error-box ul {
            margin: 10px 0 0 20px;
        }
        .result-slip {
            border: 2px solid #1976d2;
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
            background-color: #fafafa;
        }
        .result-header {
            text-align: center;
            border-bottom: 2px solid #1976d2;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .result-header h2 {
            margin: 0;
            color: #1976d2;
        }
        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .score-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .score-table td:first-child {
            font-weight: bold;
            width: 60%;
        }
        .score-table td:last-child {
            text-align: right;
        }
        .total-row {
            background-color: #e3f2fd;
            font-weight: bold;
            font-size: 18px;
        }
        .status-box {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }
        .status-pass {
            background-color: #c8e6c9;
            color: #2e7d32;
            border: 2px solid #4caf50;
        }
        .status-consult {
            background-color: #fff3cd;
            color: #856404;
            border: 2px solid #ffc107;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #1565c0;
        }
        .grade-id {
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
        <?php if (!empty($errors)): ?>
            <!-- Display Validation Errors -->
            <h1>❌ Validation Error</h1>
            <div class="error-box">
                <p><strong>Please correct the following errors:</strong></p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="input.php" class="btn">Go Back</a>
            
        <?php elseif ($success): ?>
            <!-- Display Result Slip -->
            <div class="result-slip">
                <div class="result-header">
                    <h2>📋 Grade Computation Result Slip</h2>
                    <p style="margin: 5px 0;"><?php echo $unitTitle; ?> (<?php echo UNIT_CODE; ?>)</p>
                    <p style="margin: 5px 0; font-size: 14px;">Kirinyaga University</p>
                </div>
                
                <div class="grade-id">
                    📝 Record ID: #<?php echo str_pad($gradeId, 6, '0', STR_PAD_LEFT); ?>
                </div>
                
                <div style="margin: 20px 0;">
                    <strong>Student Name:</strong> <?php echo htmlspecialchars($studentName); ?><br>
                    <strong>Date:</strong> <?php echo date('F j, Y - g:i A'); ?>
                </div>
                
                <table class="score-table">
                    <tr>
                        <td>CAT 1 Score (Maximum: <?php echo CAT1_WEIGHT; ?>)</td>
                        <td><?php echo number_format($cat1Score, 1); ?> / <?php echo CAT1_WEIGHT; ?></td>
                    </tr>
                    <tr>
                        <td>CAT 2 Score (Maximum: <?php echo CAT2_WEIGHT; ?>)</td>
                        <td><?php echo number_format($cat2Score, 1); ?> / <?php echo CAT2_WEIGHT; ?></td>
                    </tr>
                    <tr>
                        <td>Assignment Score (Maximum: <?php echo ASSIGNMENT_WEIGHT; ?>)</td>
                        <td><?php echo number_format($assignmentScore, 1); ?> / <?php echo ASSIGNMENT_WEIGHT; ?></td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL INTERNAL MARKS</td>
                        <td><?php echo number_format($totalScore, 1); ?> / <?php echo TOTAL_INTERNAL_MARKS; ?></td>
                    </tr>
                </table>
                
                <?php
                $passingScore = (PASSMARK / 100) * TOTAL_INTERNAL_MARKS;
                if ($totalScore >= $passingScore): ?>
                    <div class="status-box status-pass">
                        ✅ On Track for Passmark
                        <p style="margin: 10px 0 0 0; font-size: 14px; font-weight: normal;">
                            You have scored <?php echo number_format($totalScore, 1); ?> marks, which is above the passmark of <?php echo number_format($passingScore, 1); ?> marks (<?php echo PASSMARK; ?>%).
                        </p>
                    </div>
                <?php else: ?>
                    <div class="status-box status-consult">
                        ⚠️ Consult Lecturer at <?php echo LECTURER_EMAIL; ?>
                        <p style="margin: 10px 0 0 0; font-size: 14px; font-weight: normal;">
                            You have scored <?php echo number_format($totalScore, 1); ?> marks. You need <?php echo number_format($passingScore - $totalScore, 1); ?> more marks to reach the passmark of <?php echo number_format($passingScore, 1); ?> marks.
                        </p>
                    </div>
                <?php endif; ?>
                
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center;">
                    This result has been saved to the database.<br>
                    Note: Final marks include the Main Exam (60 marks) which is not included in this calculation.
                </div>
            </div>
            
            <a href="input.php" class="btn">Calculate Again</a>
            <a href="view_grades.php" class="btn" style="background-color: #43a047;">View All Grades</a>
            <a href="assessment_info.php" class="btn" style="background-color: #666;">View Requirements</a>
            
        <?php else: ?>
            <!-- Invalid Access -->
            <h1>Invalid Access</h1>
            <p>This page must be accessed through the grade input form.</p>
            <a href="input.php" class="btn">Go to Input Form</a>
        <?php endif; ?>
    </div>
</body>
</html>