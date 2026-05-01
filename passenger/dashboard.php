<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Fetch dashboard stats
$stats = ['total_lost' => 0, 'total_found' => 0, 'matched' => 0, 'returned' => 0];

$queries = [
    'total_lost' => "SELECT COUNT(*) as count FROM lost_items",
    'total_found' => "SELECT COUNT(*) as count FROM found_items",
    'matched' => "SELECT COUNT(*) as count FROM lost_items WHERE status = 'matched'",
    'returned' => "SELECT COUNT(*) as count FROM lost_items WHERE status = 'returned'"
];

foreach ($queries as $key => $query) {
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $stats[$key] = $row['count'];
    }
}

// Fetch recent lost items
$recent_lost = mysqli_query($conn, "SELECT * FROM lost_items ORDER BY reported_at DESC LIMIT 5");

// Fetch recent found items
$recent_found = mysqli_query($conn, "SELECT * FROM found_items ORDER BY found_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header>
    <div class="nav-container">
        <a href="dashboard.php" class="logo"><img src="../assets/logo.png" alt="Ethiopian Airlines"></a>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
        <nav>
            <ul class="nav-links">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="view_lost.php">Lost Items</a></li>
                <li><a href="view_found.php">Found Items</a></li>
                <li><a href="match_items.php">Match Items</a></li>
                <li><a href="logout.php" class="btn btn-danger" style="padding: 5px 15px;">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="d-flex justify-between align-center mb-4" style="flex-wrap: wrap; gap: 1rem;">
        <h2>Welcome back, <?= htmlspecialchars($_SESSION['staff_name']) ?>!</h2>
        <div style="flex: 1; max-width: 400px; min-width: 250px;">
            <form action="search.php" method="GET" class="d-flex">
                <input type="text" name="q" class="form-control" placeholder="Search by claim code, name, item..." style="border-radius: 8px 0 0 8px;">
                <button type="submit" class="btn btn-primary" style="border-radius: 0 8px 8px 0;">Search</button>
            </form>
        </div>
    </div>

    <section class="stats-grid">
        <div class="stat-card" style="border-top: 4px solid var(--primary);">
            <h3>Total Lost Items</h3>
            <div class="value"><?= $stats['total_lost'] ?></div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--warning);">
            <h3>Total Found Items</h3>
            <div class="value"><?= $stats['total_found'] ?></div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--success);">
            <h3>Items Matched</h3>
            <div class="value"><?= $stats['matched'] ?></div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--info);">
            <h3>Items Returned</h3>
            <div class="value"><?= $stats['returned'] ?></div>
        </div>
    </section>

    <h3 class="mb-2 mt-4">Quick Actions</h3>
    <div class="card-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <a href="report_found.php" class="card text-center" style="text-decoration: none; padding: 1.5rem;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--primary);"><i class="fas fa-plus-circle"></i></div>
            <h4 style="color: var(--dark);">Log Found Item</h4>
        </a>
        <a href="match_items.php" class="card text-center" style="text-decoration: none; padding: 1.5rem;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--success);"><i class="fas fa-handshake"></i></div>
            <h4 style="color: var(--dark);">Match Items</h4>
        </a>
        <a href="return_item.php" class="card text-center" style="text-decoration: none; padding: 1.5rem;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--info);"><i class="fas fa-box"></i></div>
            <h4 style="color: var(--dark);">Process Return</h4>
        </a>
        <a href="view_lost.php" class="card text-center" style="text-decoration: none; padding: 1.5rem;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--gray);"><i class="fas fa-clipboard-list"></i></div>
            <h4 style="color: var(--dark);">View All Lost</h4>
        </a>
    </div>

    <div class="d-flex gap-1" style="flex-wrap: wrap;">
        <div class="card" style="flex: 1; min-width: 300px; overflow-x: auto;">
            <div class="d-flex justify-between align-center mb-2">
                <h3>Recent Lost Reports</h3>
                <a href="view_lost.php" style="font-size: 0.9rem;">View All →</a>
            </div>
            <table class="w-100">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recent_lost)): ?>
                    <tr>
                        <td><strong><?= $row['claim_code'] ?></strong></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= date('M d', strtotime($row['lost_date'])) ?></td>
                        <td><?= getStatusBadge($row['status']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card" style="flex: 1; min-width: 300px; overflow-x: auto;">
            <div class="d-flex justify-between align-center mb-2">
                <h3>Recent Found Items</h3>
                <a href="view_found.php" style="font-size: 0.9rem;">View All →</a>
            </div>
            <table class="w-100">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recent_found)): ?>
                    <tr>
                        <td><strong><?= $row['found_code'] ?></strong></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['found_location']) ?></td>
                        <td><?= getStatusBadge($row['status']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
