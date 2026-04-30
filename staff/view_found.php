<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$query = "SELECT * FROM found_items WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (found_code LIKE '%$search%' OR item_name LIKE '%$search%' OR found_location LIKE '%$search%' OR storage_location LIKE '%$search%')";
}
if (!empty($status_filter)) {
    $query .= " AND status = '$status_filter'";
}

$query .= " ORDER BY found_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Found Items - <?= SITE_NAME ?></title>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_lost.php">Lost Items</a></li>
                <li><a href="view_found.php" class="active">Found Items</a></li>
                <li><a href="match_items.php">Match Items</a></li>
                <li><a href="logout.php" class="btn btn-danger" style="padding: 5px 15px;">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="card mb-4">
        <div class="d-flex justify-between align-center mb-4" style="flex-wrap: wrap;">
            <h2>Found Items Database</h2>
            <a href="report_found.php" class="btn btn-primary"><i class="fas fa-plus"></i> Log New Found Item</a>
        </div>
        
        <form action="" method="GET" class="d-flex gap-1" style="flex-wrap: wrap;">
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search by code, item, location..." value="<?= htmlspecialchars($search) ?>" onkeyup="filterTable('searchInput', 'foundTable')">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="unclaimed" <?= $status_filter == 'unclaimed' ? 'selected' : '' ?>>Unclaimed</option>
                    <option value="matched" <?= $status_filter == 'matched' ? 'selected' : '' ?>>Matched</option>
                    <option value="returned" <?= $status_filter == 'returned' ? 'selected' : '' ?>>Returned</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view_found.php" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <div class="card table-responsive">
        <table id="foundTable">
            <thead>
                <tr>
                    <th>Found Code</th>
                    <th>Item Name</th>
                    <th>Location Found</th>
                    <th>Storage Location</th>
                    <th>Date Found</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?= $row['found_code'] ?></strong></td>
                        <td><?= htmlspecialchars($row['item_name']) ?><br><small class="text-gray"><?= htmlspecialchars($row['item_color']) ?></small></td>
                        <td><?= htmlspecialchars($row['found_location']) ?></td>
                        <td><?= htmlspecialchars($row['storage_location']) ?></td>
                        <td><?= date('M d, Y', strtotime($row['found_date'])) ?></td>
                        <td><?= getStatusBadge($row['status']) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if($row['status'] == 'unclaimed'): ?>
                                    <a href="match_items.php?found_search=<?= $row['found_code'] ?>" class="btn btn-success" style="padding: 4px 8px; font-size: 0.8rem;">Match</a>
                                <?php elseif($row['status'] == 'matched'): ?>
                                    <a href="return_item.php?found_id=<?= $row['id'] ?>" class="btn btn-info" style="padding: 4px 8px; font-size: 0.8rem;">Return</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 2rem;">No found items matching your criteria.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
