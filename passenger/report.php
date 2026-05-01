<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item - <?= SITE_NAME ?></title>
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
                <li><a href="report.php" class="active">Report Lost Item</a></li>
                <li><a href="check_status.php">Check Status</a></li>
                <li><a href="../staff/login.php" class="btn btn-primary" style="padding: 5px 15px;">Staff Panel</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <h2 class="mb-4">Report Lost Item</h2>
        <form id="reportForm" action="save_report.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm('reportForm')">
            
            <h3 class="mb-2">1. Contact Information</h3>
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="passenger_name" class="form-control" required minlength="3">
            </div>
            <div class="form-group d-flex gap-1" style="flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" name="phone" class="form-control" required minlength="10">
                </div>
            </div>

            <h3 class="mb-2 mt-4">2. Item Details</h3>
            <div class="form-group d-flex gap-1" style="flex-wrap: wrap;">
                <div style="flex: 2; min-width: 250px;">
                    <label class="form-label">Item Name (e.g., Laptop, Wallet) *</label>
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
                <textarea name="item_description" class="form-control" rows="4" required placeholder="Provide any unique identifying features..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Upload Photo (Optional)</label>
                <input type="file" name="photo" class="form-control" accept="image/jpeg, image/png, image/gif" onchange="previewImage(this, 'photoPreview')">
                <img id="photoPreview" src="#" alt="Preview" style="display:none; max-width: 200px; margin-top: 10px; border-radius: 8px;">
            </div>

            <h3 class="mb-2 mt-4">3. Where You Lost It</h3>
            <div class="form-group d-flex gap-1" style="flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label">Location *</label>
                    <select name="lost_location" class="form-control" required>
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
                        <option value="Other">Other (Please mention in description)</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label">Date Lost *</label>
                    <input type="date" name="lost_date" class="form-control" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100" style="font-size: 1.1rem; padding: 15px;">Submit Lost Item Report</button>
            </div>
        </form>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
