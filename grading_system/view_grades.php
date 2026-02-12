<?php
/**
 * View All Student Grades
 * Grading System - Database Display with Statistics
 */
error_reporting(E_ALL);
require_once 'config.php';

// Get database connection
$conn = getDBConnection();

// Pagination
$records_per_page = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filter by status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause
$whereConditions = [];
$params = [];
$types = '';

if (!empty($statusFilter)) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

if (!empty($searchQuery)) {
    $whereConditions[] = "student_name LIKE ?";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $types .= 's';
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total records
$countSql = "SELECT COUNT(*) as total FROM student_grades $whereClause";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $records_per_page);
$countStmt->close();

// Get student grades
$sql = "SELECT * FROM student_grades $whereClause ORDER BY submission_date DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get class statistics
$statsResult = $conn->query("SELECT * FROM class_statistics");

if ($statsResult) {
    $classStats = $statsResult->fetch_assoc();
} else {
    $classStats = [];
}

$classStats = $statsResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Grades - <?php echo UNIT_CODE; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #43a047 0%, #388e3c 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #fb8c00 0%, #f57c00 100%);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            opacity: 0.9;
        }
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
        }
        .filters {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 10px;
        }
        .filters select,
        .filters input,
        .filters button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filters button {
            background-color: #1976d2;
            color: white;
            border: none;
            cursor: pointer;
        }
        .filters button:hover {
            background-color: #1565c0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #1976d2;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-pass {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .badge-consult {
            background-color: #fff3cd;
            color: #856404;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #1976d2;
        }
        .pagination .active {
            background-color: #1976d2;
            color: white;
        }
        .pagination a:hover {
            background-color: #e3f2fd;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #1565c0;
        }
        .no-records {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Student Grades Database</h1>
        <p style="color: #666;"><?php echo $unitTitle; ?> (<?php echo UNIT_CODE; ?>)</p>
        
        <!-- Class Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="number"><?php echo $classStats['total_students'] ?? 0; ?></div>
            </div>
            <div class="stat-card success">
                <h3>Passing</h3>
                <div class="number"><?php echo $classStats['passing_students'] ?? 0; ?></div>
            </div>
            <div class="stat-card warning">
                <h3>Need Consultation</h3>
                <div class="number"><?php echo $classStats['consulting_students'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Average Score</h3>
                <div class="number"><?php echo $classStats['average_score'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Highest Score</h3>
                <div class="number"><?php echo $classStats['highest_score'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Avg CAT 1</h3>
                <div class="number"><?php echo $classStats['avg_cat1'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Avg CAT 2</h3>
                <div class="number"><?php echo $classStats['avg_cat2'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Avg Assignments</h3>
                <div class="number"><?php echo $classStats['avg_assignment'] ?? 0; ?></div>
            </div>
        </div>
        
        <!-- Filters -->
        <form method="GET" class="filters">
            <select name="status">
                <option value="">All Students</option>
                <option value="Pass" <?php echo $statusFilter === 'Pass' ? 'selected' : ''; ?>>Passing Students</option>
                <option value="Consult" <?php echo $statusFilter === 'Consult' ? 'selected' : ''; ?>>Need Consultation</option>
            </select>
            <input type="text" name="search" placeholder="Search by student name..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Filter</button>
        </form>
        
        <!-- Grades Table -->
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>CAT 1</th>
                        <th>CAT 2</th>
                        <th>Assignments</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo number_format($row['cat1_score'], 1); ?>/15</td>
                            <td><?php echo number_format($row['cat2_score'], 1); ?>/15</td>
                            <td><?php echo number_format($row['assignment_score'], 1); ?>/10</td>
                            <td><strong><?php echo number_format($row['total_score'], 1); ?>/40</strong></td>
                            <td>
                                <span class="status-badge badge-<?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($row['submission_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">« Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">Next »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-records">
                <h3>No grades found</h3>
                <p>No student grades match your current filters.</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="input.php" class="btn">Enter New Grades</a>
            <a href="assessment_info.php" class="btn" style="background-color: #666;">Back to Home</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>