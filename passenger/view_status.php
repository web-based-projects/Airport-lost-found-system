<?php
require_once '../config.php';

$code = isset($_GET['code']) ? sanitize($_GET['code']) : '';
$error = '';
$lost_item = null;
$found_item = null;

if (!empty($code)) {
    $query = "SELECT * FROM lost_items WHERE claim_code = '$code'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $lost_item = mysqli_fetch_assoc($result);
        
        // If matched or returned, get the found item details
        if ($lost_item['status'] == 'matched' || $lost_item['status'] == 'returned') {
            $found_query = "SELECT * FROM found_items WHERE matched_to = " . $lost_item['id'];
            $found_result = mysqli_query($conn, $found_query);
            if ($found_result && mysqli_num_rows($found_result) > 0) {
                $found_item = mysqli_fetch_assoc($found_result);
            }
        }
    } else {
        $error = "No report found with this claim code. Please check and try again.";
    }
} else {
    header("Location: check_status.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Details - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header>
    <div class="nav-container">
        <a href="../index.php" class="logo"><img src="../assets/logo.png" alt="Ethiopian Airlines"></a>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
        <nav>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="report.php">Report Lost Item</a></li>
                <li><a href="check_status.php" class="active">Check Status</a></li>
                <li><a href="../staff/login.php" class="btn btn-primary" style="padding: 5px 15px;">Staff Panel</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <?php if ($error): ?>
        <div class="card text-center" style="max-width: 600px; margin: 2rem auto;">
            <div class="mb-4" style="font-size: 4rem; color: var(--danger);"><i class="fas fa-exclamation-circle"></i></div>
            <h2 class="mb-2" style="color: var(--danger);">Not Found</h2>
            <p><?= $error ?></p>
            <a href="check_status.php" class="btn btn-primary mt-4">Try Again</a>
        </div>
    <?php elseif ($lost_item): ?>
        
        <div class="d-flex justify-between align-center mb-4">
            <h2>Status for: <?= htmlspecialchars($code) ?></h2>
            <div><?= getStatusBadge($lost_item['status']) ?></div>
        </div>

        <?php if ($lost_item['status'] == 'pending'): ?>
            <div class="alert alert-warning text-center" style="padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"><i class="fas fa-search"></i></div>
                <h3>Searching for your item</h3>
                <p class="mt-2">Our staff is currently looking for your item. We will update this status once a match is found.</p>
            </div>
        <?php elseif ($lost_item['status'] == 'matched'): ?>
            <div class="alert alert-success text-center" style="padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎉</div>
                <h3>Item Found!</h3>
                <p class="mt-2">Good news! We have found an item that matches your report. Please visit the Lost & Found office to claim it.</p>
                <div class="mt-4">
                    <a href="claim_item.php?code=<?= $code ?>" class="btn btn-success" style="font-size: 1.2rem; padding: 15px 30px;">Claim My Item Now</a>
                </div>
            </div>
        <?php elseif ($lost_item['status'] == 'returned'): ?>
            <div class="alert alert-info text-center" style="padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--info);"><i class="fas fa-box-open"></i></div>
                <h3>Item Successfully Returned</h3>
                <p class="mt-2">This item has been successfully returned to its owner. Case closed.</p>
            </div>
        <?php endif; ?>

        <div class="card-grid mt-4">
            <div class="card">
                <h3 class="mb-4 border-bottom pb-2">Your Report Details</h3>
                <table class="w-100">
                    <tr><td style="color: var(--gray); width: 40%;">Item Name</td><td><strong><?= htmlspecialchars($lost_item['item_name']) ?></strong></td></tr>
                    <tr><td style="color: var(--gray);">Date Lost</td><td><?= date('M d, Y', strtotime($lost_item['lost_date'])) ?></td></tr>
                    <tr><td style="color: var(--gray);">Location Lost</td><td><?= htmlspecialchars($lost_item['lost_location']) ?></td></tr>
                    <tr><td style="color: var(--gray);">Color/Brand</td><td><?= htmlspecialchars($lost_item['item_color']) ?> / <?= htmlspecialchars($lost_item['brand']) ?></td></tr>
                    <tr><td style="color: var(--gray);">Description</td><td><?= nl2br(htmlspecialchars($lost_item['item_description'])) ?></td></tr>
                </table>
            </div>

            <?php if ($found_item): ?>
            <div class="card">
                <h3 class="mb-4 border-bottom pb-2">Found Item Details</h3>
                <table class="w-100">
                    <tr><td style="color: var(--gray); width: 40%;">Item Name</td><td><strong><?= htmlspecialchars($found_item['item_name']) ?></strong></td></tr>
                    <tr><td style="color: var(--gray);">Date Found</td><td><?= date('M d, Y', strtotime($found_item['found_date'])) ?></td></tr>
                    <tr><td style="color: var(--gray);">Location Found</td><td><?= htmlspecialchars($found_item['found_location']) ?></td></tr>
                    <?php if ($lost_item['status'] == 'matched'): ?>
                    <tr><td style="color: var(--gray);">Collection Point</td><td><strong>Main Terminal Lost & Found Office (Counter 3)</strong></td></tr>
                    <tr><td style="color: var(--gray);">Office Hours</td><td>08:00 AM - 10:00 PM (Daily)</td></tr>
                    <?php endif; ?>
                </table>
                <?php if ($lost_item['status'] == 'matched'): ?>
                    <p class="mt-4 text-sm text-gray" style="font-size: 0.9rem;"><em>* Please bring valid ID proof when collecting your item.</em></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
