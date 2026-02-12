<?php
/**
 * View All Support Tickets
 * Ticket System - Database Display with Filtering
 */

require_once 'config.php';

// Get database connection
$conn = getDBConnection();

// Pagination
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filters
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$priorityFilter = isset($_GET['priority']) ? $_GET['priority'] : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause
$whereConditions = [];
$params = [];
$types = '';

if (!empty($categoryFilter)) {
    $whereConditions[] = "issue_category = ?";
    $params[] = $categoryFilter;
    $types .= 's';
}

if (!empty($statusFilter)) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

if (!empty($priorityFilter)) {
    $whereConditions[] = "priority = ?";
    $params[] = $priorityFilter;
    $types .= 's';
}

if (!empty($searchQuery)) {
    $whereConditions[] = "(ticket_number LIKE ? OR reporter_name LIKE ? OR detailed_description LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total records
$countSql = "SELECT COUNT(*) as total FROM support_tickets $whereClause";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $records_per_page);
$countStmt->close();

// Get tickets
$sql = "SELECT * FROM support_tickets $whereClause ORDER BY submission_date DESC LIMIT ? OFFSET ?";
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
$statsResult = $conn->query("SELECT * FROM ticket_statistics");
$ticketStats = $statsResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Support Tickets - <?php echo SYSTEM_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d32f2f;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card.open {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        }
        .stat-card.progress {
            background: linear-gradient(135deg, #fb8c00 0%, #f57c00 100%);
        }
        .stat-card.resolved {
            background: linear-gradient(135deg, #43a047 0%, #388e3c 100%);
        }
        .stat-card h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
            opacity: 0.9;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
        }
        .filters {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .filters select,
        .filters input,
        .filters button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .filters button {
            background-color: #d32f2f;
            color: white;
            border: none;
            cursor: pointer;
        }
        .filters button:hover {
            background-color: #b71c1c;
        }
        .tickets-grid {
            display: grid;
            gap: 15px;
            margin: 20px 0;
        }
        .ticket-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .ticket-number {
            font-size: 16px;
            font-weight: bold;
            color: #1976d2;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin: 0 5px;
        }
        .badge-hardware { background-color: #ffcdd2; color: #c62828; }
        .badge-software { background-color: #fff9c4; color: #f57f17; }
        .badge-network { background-color: #b3e5fc; color: #01579b; }
        .badge-high { background-color: #ffcdd2; color: #c62828; }
        .badge-medium { background-color: #fff9c4; color: #f57f17; }
        .badge-low { background-color: #c8e6c9; color: #2e7d32; }
        .badge-open { background-color: #e3f2fd; color: #1976d2; }
        .badge-progress { background-color: #fff3cd; color: #f57f17; }
        .badge-resolved { background-color: #c8e6c9; color: #2e7d32; }
        .badge-closed { background-color: #e0e0e0; color: #616161; }
        .ticket-content {
            margin: 10px 0;
        }
        .ticket-content strong {
            color: #666;
            font-size: 12px;
        }
        .ticket-description {
            margin: 10px 0;
            color: #333;
            line-height: 1.5;
        }
        .ticket-footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
            font-size: 12px;
            color: #999;
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
            color: #d32f2f;
        }
        .pagination .active {
            background-color: #d32f2f;
            color: white;
        }
        .pagination a:hover {
            background-color: #ffebee;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #d32f2f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #b71c1c;
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
        <h1>🎫 Support Tickets Database</h1>
        <p style="color: #666;"><?php echo SYSTEM_NAME; ?></p>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Tickets</h3>
                <div class="number"><?php echo $ticketStats['total_tickets'] ?? 0; ?></div>
            </div>
            <div class="stat-card open">
                <h3>Open</h3>
                <div class="number"><?php echo $ticketStats['open_tickets'] ?? 0; ?></div>
            </div>
            <div class="stat-card progress">
                <h3>In Progress</h3>
                <div class="number"><?php echo $ticketStats['in_progress_tickets'] ?? 0; ?></div>
            </div>
            <div class="stat-card resolved">
                <h3>Resolved</h3>
                <div class="number"><?php echo $ticketStats['resolved_tickets'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>High Priority</h3>
                <div class="number"><?php echo $ticketStats['high_priority'] ?? 0; ?></div>
            </div>
        </div>
        
        <!-- Filters -->
        <form method="GET" class="filters">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Hardware" <?php echo $categoryFilter === 'Hardware' ? 'selected' : ''; ?>>Hardware</option>
                <option value="Software" <?php echo $categoryFilter === 'Software' ? 'selected' : ''; ?>>Software</option>
                <option value="Network" <?php echo $categoryFilter === 'Network' ? 'selected' : ''; ?>>Network</option>
            </select>
            <select name="status">
                <option value="">All Statuses</option>
                <option value="Open" <?php echo $statusFilter === 'Open' ? 'selected' : ''; ?>>Open</option>
                <option value="In Progress" <?php echo $statusFilter === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="Resolved" <?php echo $statusFilter === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                <option value="Closed" <?php echo $statusFilter === 'Closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
            <select name="priority">
                <option value="">All Priorities</option>
                <option value="High" <?php echo $priorityFilter === 'High' ? 'selected' : ''; ?>>High</option>
                <option value="Medium" <?php echo $priorityFilter === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="Low" <?php echo $priorityFilter === 'Low' ? 'selected' : ''; ?>>Low</option>
            </select>
            <input type="text" name="search" placeholder="Search tickets..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Filter</button>
        </form>
        
        <!-- Tickets Grid -->
        <?php if ($result->num_rows > 0): ?>
            <div class="tickets-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <div class="ticket-number"><?php echo htmlspecialchars($row['ticket_number']); ?></div>
                            <div>
                                <span class="badge badge-<?php echo strtolower($row['issue_category']); ?>">
                                    <?php echo $row['issue_category']; ?>
                                </span>
                                <span class="badge badge-<?php echo strtolower($row['priority']); ?>">
                                    <?php echo $row['priority']; ?>
                                </span>
                                <span class="badge badge-<?php echo strtolower(str_replace(' ', '', $row['status'])); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="ticket-content">
                            <strong>Reporter:</strong> <?php echo htmlspecialchars($row['reporter_name']); ?><br>
                            <strong>Department:</strong> <?php echo htmlspecialchars($row['department']); ?><br>
                            <strong>Response Time:</strong> <?php echo htmlspecialchars($row['response_time']); ?>
                        </div>
                        
                        <div class="ticket-description">
                            <?php echo htmlspecialchars($row['detailed_description']); ?>
                        </div>
                        
                        <div class="ticket-footer">
                            Submitted: <?php echo date('M j, Y - g:i A', strtotime($row['submission_date'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&category=<?php echo $categoryFilter; ?>&status=<?php echo $statusFilter; ?>&priority=<?php echo $priorityFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">« Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>&category=<?php echo $categoryFilter; ?>&status=<?php echo $statusFilter; ?>&priority=<?php echo $priorityFilter; ?>&search=<?php echo urlencode($searchQuery); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&category=<?php echo $categoryFilter; ?>&status=<?php echo $statusFilter; ?>&priority=<?php echo $priorityFilter; ?>&search=<?php echo urlencode($searchQuery); ?>">Next »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-records">
                <h3>No tickets found</h3>
                <p>No support tickets match your current filters.</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="report_issue.php" class="btn">Submit New Ticket</a>
            <a href="categories.php" class="btn" style="background-color: #666;">Back to Categories</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>