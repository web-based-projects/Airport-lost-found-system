<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$query = "SELECT * FROM lost_items WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (claim_code LIKE '%$search%' OR passenger_name LIKE '%$search%' OR item_name LIKE '%$search%' OR lost_location LIKE '%$search%')";
}
if (!empty($status_filter)) {
    $query .= " AND status = '$status_filter'";
}

$query .= " ORDER BY reported_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lost Items - <?= SITE_NAME ?></title>
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
                <li><a href="view_lost.php" class="active">Lost Items</a></li>
                <li><a href="view_found.php">Found Items</a></li>
                <li><a href="match_items.php">Match Items</a></li>
                <li><a href="logout.php" class="btn btn-danger" style="padding: 5px 15px;">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="card mb-4">
        <h2 class="mb-4">Lost Items Database</h2>
        
        <form action="" method="GET" class="d-flex gap-1" style="flex-wrap: wrap;">
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search by name, code, item..." value="<?= htmlspecialchars($search) ?>" onkeyup="filterTable('searchInput', 'lostTable')">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="matched" <?= $status_filter == 'matched' ? 'selected' : '' ?>>Matched</option>
                    <option value="returned" <?= $status_filter == 'returned' ? 'selected' : '' ?>>Returned</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view_lost.php" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>

    <div class="card table-responsive">
        <table id="lostTable">
            <thead>
                <tr>
                    <th>Claim Code</th>
                    <th>Passenger</th>
                    <th>Item Name</th>
                    <th>Lost Location</th>
                    <th>Date Lost</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?= $row['claim_code'] ?></strong></td>
                        <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                        <td><?= htmlspecialchars($row['item_name']) ?><br><small class="text-gray"><?= htmlspecialchars($row['item_color']) ?></small></td>
                        <td><?= htmlspecialchars($row['lost_location']) ?></td>
                        <td><?= date('M d, Y', strtotime($row['lost_date'])) ?></td>
                        <td><?= getStatusBadge($row['status']) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if($row['status'] == 'pending'): ?>
                                    <a href="match_items.php?lost_search=<?= $row['claim_code'] ?>" class="btn btn-success" style="padding: 4px 8px; font-size: 0.8rem;">Match</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 2rem;">No lost items found matching your criteria.</td>
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
