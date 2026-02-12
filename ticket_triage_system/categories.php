<?php
/**
 * Issue Categories Display
 * Shows available categories and their priority levels
 */

require_once 'setup.php';

// Multidimensional array to store Issue Categories and Priority Levels
$issueCategories = [
    [
        'category' => 'Hardware',
        'priority' => 'High',
        'description' => 'Physical equipment issues (computers, printers, projectors)'
    ],
    [
        'category' => 'Software',
        'priority' => 'Medium',
        'description' => 'Application and software-related problems'
    ],
    [
        'category' => 'Network',
        'priority' => 'High',
        'description' => 'Wi-Fi connectivity and network access issues'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SYSTEM_NAME; ?> - Categories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .category-card {
            background-color: #fafafa;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .category-name {
            font-size: 20px;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 10px;
        }
        .priority-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .priority-high {
            background-color: #ffcdd2;
            color: #c62828;
        }
        .priority-medium {
            background-color: #fff9c4;
            color: #f57f17;
        }
        .priority-low {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #d32f2f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #b71c1c;
        }
        .info-section {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠️ <?php echo SYSTEM_NAME; ?></h1>
        <p class="subtitle"><?php echo UNIVERSITY_NAME; ?> - <?php echo IT_DEPARTMENT; ?></p>
        
        <div class="info-section">
            <strong>Support Contact:</strong> <?php echo IT_SUPPORT_EMAIL; ?><br>
            <strong>Server Version:</strong> <?php echo $serverVersion; ?>
        </div>
        
        <h2>Available Issue Categories</h2>
        <div class="category-grid">
            <?php foreach ($issueCategories as $issue): ?>
                <div class="category-card">
                    <div class="category-name"><?php echo $issue['category']; ?></div>
                    <span class="priority-badge priority-<?php echo strtolower($issue['priority']); ?>">
                        Priority: <?php echo $issue['priority']; ?>
                    </span>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        <?php echo $issue['description']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <a href="report_issue.php" class="btn">Report an Issue</a>
    </div>
</body>
</html>