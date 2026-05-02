<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: return_item.php");
    exit();
}

$found_id = (int)$_POST['found_id'];
$lost_id = (int)$_POST['lost_id'];
$id_type = sanitize($_POST['id_type']);
$id_number = sanitize($_POST['id_number']);
$receiver_name = sanitize($_POST['receiver_name']);
$signature = sanitize($_POST['signature']);
$notes = sanitize($_POST['notes']);

mysqli_begin_transaction($conn);

try {
    // We would typically store return logs in a separate table, but for this simple version
    // we'll just update the status in both tables and maybe store notes in description
    
    // Update found item
    $update_found = "UPDATE found_items SET status = 'returned' WHERE id = $found_id";
    if (!mysqli_query($conn, $update_found)) throw new Exception("Error updating found item.");

    // Update lost item
    $update_lost = "UPDATE lost_items SET status = 'returned' WHERE id = $lost_id";
    if (!mysqli_query($conn, $update_lost)) throw new Exception("Error updating lost item.");

    mysqli_commit($conn);
    $success = true;
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    $success = false;
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Processed - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <div class="nav-container">
        <a href="dashboard.php" class="logo">👨‍✈️ Staff Panel</a>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        <nav>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_lost.php">Lost Items</a></li>
                <li><a href="view_found.php">Found Items</a></li>
                <li><a href="match_items.php">Match Items</a></li>
                <li><a href="logout.php" class="btn btn-danger text-white" style="color:white; padding: 5px 15px;">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container text-center">
    <?php if ($success): ?>
        <div class="card" style="max-width: 600px; margin: 2rem auto;">
            <div class="mb-4" style="font-size: 4rem;">📦✅</div>
            <h2 class="mb-2">Item Successfully Returned!</h2>
            <p class="mb-4">The item has been handed over to <strong><?= htmlspecialchars($receiver_name) ?></strong> and the case is now closed.</p>
            
            <div class="d-flex gap-1 justify-center mt-4">
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                <a href="return_item.php" class="btn btn-outline">Process Another Return</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="max-width: 600px; margin: 2rem auto;">
            <div class="mb-4" style="font-size: 4rem;">❌</div>
            <h2 class="mb-2" style="color: var(--danger);">Process Failed</h2>
            <p>Error saving return record: <?= $error ?></p>
            <a href="return_item.php?found_id=<?= $found_id ?>" class="btn btn-primary mt-4">Try Again</a>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
