<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Found Item - <?= SITE_NAME ?></title>
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
                <li><a href="view_found.php">Found Items</a></li>
                <li><a href="match_items.php">Match Items</a></li>
                <li><a href="logout.php" class="btn btn-danger" style="padding: 5px 15px;">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <h2 class="mb-4">Log Found Item</h2>
        <form id="foundForm" action="save_found.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm('foundForm')">
            
            <div class="form-group">
                <label class="form-label">Logged By (Staff)</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['staff_name']) ?>" disabled>
            </div>

            <h3 class="mb-2 mt-4">1. Item Details</h3>
            <div class="form-group d-flex gap-1" style="flex-wrap: wrap;">
                <div style="flex: 2; min-width: 250px;">
                    <label class="form-label">Item Name *</label>
                    <input type="text" name="item_name" class="form-control" required>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label class="form-label">Color *</label>
                    <input type="text" name="item_color" class="form-control" required>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Detailed Description *</label>
                <textarea name="item_description" class="form-control" rows="4" required placeholder="Describe condition, unique marks..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Upload Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/jpeg, image/png, image/gif" onchange="previewImage(this, 'photoPreview')">
                <img id="photoPreview" src="#" alt="Preview" style="display:none; max-width: 200px; margin-top: 10px; border-radius: 8px;">
            </div>

            <h3 class="mb-2 mt-4">2. Recovery Details</h3>
            <div class="form-group d-flex gap-1" style="flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label">Found Location *</label>
                    <select name="found_location" class="form-control" required>
                        <option value="">Select a location</option>
                        <option value="Terminal 1 Gate A">Terminal 1 Gate A</option>
                        <option value="Terminal 1 Gate B">Terminal 1 Gate B</option>
                        <option value="Terminal 2 Gate C">Terminal 2 Gate C</option>
                        <option value="Food Court">Food Court</option>
                        <option value="Security Check">Security Check</option>
                        <option value="Baggage Claim">Baggage Claim</option>
                        <option value="Gate A waiting area">Gate A waiting area</option>
                        <option value="Gate C waiting area">Gate C waiting area</option>
                        <option value="Parking Area">Parking Area</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label">Date Found *</label>
                    <input type="date" name="found_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Storage Location in Office *</label>
                <input type="text" name="storage_location" class="form-control" required placeholder="e.g., Rack 5, Shelf B or Safe Box 1">
            </div>

            <div class="mt-4 d-flex justify-between gap-1">
                <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Found Item & Check Matches</button>
            </div>
        </form>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
