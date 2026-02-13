<?php

require_once 'config.php';

$conn = getDBConnection();

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filter by category if selected
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause
$whereConditions = [];
$params = [];
$types = '';

if (!empty($categoryFilter)) {
    $whereConditions[] = "action_category = ?";
    $params[] = $categoryFilter;
    $types .= 's';
}

if (!empty($searchQuery)) {
    $whereConditions[] = "(full_name LIKE ? OR student_id LIKE ? OR impact_description LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total records count
$countSql = "SELECT COUNT(*) as total FROM sustainability_submissions $whereClause";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $records_per_page);
$countStmt->close();

// Get submissions
$sql = "SELECT * FROM sustainability_submissions $whereClause ORDER BY submission_date DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get category statistics
$statsSql = "SELECT action_category, COUNT(*) as count FROM sustainability_submissions GROUP BY action_category";
$statsResult = $conn->query($statsSql);
$stats = [];
while ($row = $statsResult->fetch_assoc()) {
    $stats[$row['action_category']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions - Eco-Track</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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
            color: #2c5f2d;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #2c5f2d 0%, #4a8f4d 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .stat-card .number {
            font-size: 32px;
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
        .filters input[type="text"],
        .filters button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filters button {
            background-color: #2c5f2d;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
        }
        .filters button:hover {
            background-color: #1e4620;
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
            background-color: #2c5f2d;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .category-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-tree {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .badge-waste {
            background-color: #fff9c4;
            color: #f57f17;
        }
        .badge-energy {
            background-color: #b3e5fc;
            color: #01579b;
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
            color: #2c5f2d;
        }
        .pagination .active {
            background-color: #2c5f2d;
            color: white;
        }
        .pagination a:hover {
            background-color: #e8f5e9;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c5f2d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #1e4620;
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
        <h1>🌱 Sustainability Submissions Database</h1>
        <p style="color: #666;">Kirinyaga University Eco-Track Portal</p>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Submissions</h3>
                <div class="number"><?php echo $totalRecords; ?></div>
            </div>
            <div class="stat-card">
                <h3>Tree Planting</h3>
                <div class="number"><?php echo isset($stats['Tree Planting']) ? $stats['Tree Planting'] : 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Waste Reduction</h3>
                <div class="number"><?php echo isset($stats['Waste Reduction']) ? $stats['Waste Reduction'] : 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Energy Saving</h3>
                <div class="number"><?php echo isset($stats['Energy Saving']) ? $stats['Energy Saving'] : 0; ?></div>
            </div>
        </div>
        
        <!-- Filters -->
        <form method="GET" class="filters">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Tree Planting" <?php echo $categoryFilter === 'Tree Planting' ? 'selected' : ''; ?>>Tree Planting</option>
                <option value="Waste Reduction" <?php echo $categoryFilter === 'Waste Reduction' ? 'selected' : ''; ?>>Waste Reduction</option>
                <option value="Energy Saving" <?php echo $categoryFilter === 'Energy Saving' ? 'selected' : ''; ?>>Energy Saving</option>
            </select>
            <input type="text" name="search" placeholder="Search by name, ID, or description..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Filter</button>
        </form>
        
        <!-- Submissions Table -->
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Student ID</th>
                        <th>Category</th>
                        <th>Impact Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td>
                                <?php
                                $badgeClass = '';
                                switch ($row['action_category']) {
                                    case 'Tree Planting':
                                        $badgeClass = 'badge-tree';
                                        break;
                                    case 'Waste Reduction':
                                        $badgeClass = 'badge-waste';
                                        break;
                                    case 'Energy Saving':
                                        $badgeClass = 'badge-energy';
                                        break;
                                }
                                ?>
                                <span class="category-badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($row['action_category']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(substr($row['impact_description'], 0, 100)) . '...'; ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['submission_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&category=<?php echo $categoryFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">« Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&category=<?php echo $categoryFilter; ?>&search=<?php echo urlencode($searchQuery); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&category=<?php echo $categoryFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">Next »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-records">
                <h3>No submissions found</h3>
                <p>No sustainability actions match your current filters.</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="track.php" class="btn">Submit New Entry</a>
            <a href="welcome.php" class="btn" style="background-color: #666;">Back to Home</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>