<?php
/**
 * Browse Educational Resources
 * Masomo Exchange - Database Display
 */

require_once 'config.php';

// Get database connection
$conn = getDBConnection();

// Pagination setup
$records_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filter by category
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause
$whereConditions = [];
$params = [];
$types = '';

if (!empty($categoryFilter)) {
    $whereConditions[] = "resource_category = ?";
    $params[] = $categoryFilter;
    $types .= 's';
}

if (!empty($searchQuery)) {
    $whereConditions[] = "(resource_title LIKE ? OR contributor_email LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total records
$countSql = "SELECT COUNT(*) as total FROM educational_resources $whereClause";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $records_per_page);
$countStmt->close();

// Get resources
$sql = "SELECT * FROM educational_resources $whereClause ORDER BY submission_date DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get statistics
$statsSql = "SELECT resource_category, COUNT(*) as count FROM educational_resources GROUP BY resource_category";
$statsResult = $conn->query($statsSql);
$stats = [];
while ($row = $statsResult->fetch_assoc()) {
    $stats[$row['resource_category']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Resources - Masomo Exchange</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
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
            color: #667eea;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .filters input,
        .filters button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filters button {
            background-color: #667eea;
            color: white;
            border: none;
            cursor: pointer;
        }
        .filters button:hover {
            background-color: #5568d3;
        }
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .resource-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .resource-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .badge-notes {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .badge-video {
            background-color: #fce4ec;
            color: #c2185b;
        }
        .badge-code {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        .resource-meta {
            font-size: 12px;
            color: #666;
            margin: 10px 0;
        }
        .resource-link {
            display: inline-block;
            padding: 8px 15px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 10px;
        }
        .resource-link:hover {
            background-color: #5568d3;
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
            color: #667eea;
        }
        .pagination .active {
            background-color: #667eea;
            color: white;
        }
        .pagination a:hover {
            background-color: #e8f4f8;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #5568d3;
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
        <h1>📚 Browse Educational Resources</h1>
        <p style="color: #666;">KyU Masomo Resource Exchange</p>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Resources</h3>
                <div class="number"><?php echo $totalRecords; ?></div>
            </div>
            <div class="stat-card">
                <h3>Notes</h3>
                <div class="number"><?php echo isset($stats['Notes']) ? $stats['Notes'] : 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Videos</h3>
                <div class="number"><?php echo isset($stats['Video']) ? $stats['Video'] : 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Source Code</h3>
                <div class="number"><?php echo isset($stats['Source Code']) ? $stats['Source Code'] : 0; ?></div>
            </div>
        </div>
        
        <!-- Filters -->
        <form method="GET" class="filters">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Notes" <?php echo $categoryFilter === 'Notes' ? 'selected' : ''; ?>>Notes</option>
                <option value="Video" <?php echo $categoryFilter === 'Video' ? 'selected' : ''; ?>>Videos</option>
                <option value="Source Code" <?php echo $categoryFilter === 'Source Code' ? 'selected' : ''; ?>>Source Code</option>
            </select>
            <input type="text" name="search" placeholder="Search resources..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
        
        <!-- Resources Grid -->
        <?php if ($result->num_rows > 0): ?>
            <div class="resources-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="resource-card">
                        <?php
                        $badgeClass = '';
                        switch ($row['resource_category']) {
                            case 'Notes':
                                $badgeClass = 'badge-notes';
                                $icon = '📄';
                                break;
                            case 'Video':
                                $badgeClass = 'badge-video';
                                $icon = '🎥';
                                break;
                            case 'Source Code':
                                $badgeClass = 'badge-code';
                                $icon = '💻';
                                break;
                        }
                        ?>
                        <span class="category-badge <?php echo $badgeClass; ?>">
                            <?php echo $icon . ' ' . htmlspecialchars($row['resource_category']); ?>
                        </span>
                        
                        <div class="resource-title">
                            <?php echo htmlspecialchars($row['resource_title']); ?>
                        </div>
                        
                        <div class="resource-meta">
                            <strong>Contributor:</strong> <?php echo htmlspecialchars($row['contributor_email']); ?><br>
                            <strong>Added:</strong> <?php echo date('M j, Y', strtotime($row['submission_date'])); ?><br>
                            <strong>Views:</strong> <?php echo $row['views']; ?>
                        </div>
                        
                        <a href="<?php echo htmlspecialchars($row['resource_url']); ?>" 
                           target="_blank" 
                           class="resource-link"
                           onclick="recordView(<?php echo $row['id']; ?>)">
                            Access Resource →
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
            
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
                <h3>No resources found</h3>
                <p>No educational resources match your current filters.</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="upload_form.php" class="btn">Submit Resource</a>
            <a href="index.php" class="btn" style="background-color: #666;">Back to Home</a>
        </div>
    </div>
    
    <script>
    function recordView(resourceId) {
        // Optional: Record view via AJAX
        fetch('record_view.php?id=' + resourceId)
            .catch(err => console.log('View tracking failed'));
    }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>