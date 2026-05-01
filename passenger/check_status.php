<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Status - <?= SITE_NAME ?></title>
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

<div class="container d-flex align-center justify-center" style="min-height: 60vh;">
    <div class="card text-center" style="max-width: 500px; width: 100%;">
        <div class="mb-4" style="font-size: 3rem; color: var(--primary);"><i class="fas fa-search"></i></div>
        <h2 class="mb-2">Check Item Status</h2>
        <p class="mb-4 text-gray">Enter your unique claim code to check if your item has been found.</p>
        
        <form action="view_status.php" method="GET">
            <div class="form-group">
                <input type="text" name="code" class="form-control" style="font-size: 1.2rem; text-align: center; text-transform: uppercase; padding: 15px;" placeholder="e.g. LOST-A1B2C3" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" style="padding: 15px; font-size: 1.1rem;">Check Status Now</button>
        </form>
        
        <div class="mt-4 pt-4" style="border-top: 1px solid #E5E7EB;">
            <p>Don't have a code? <a href="report.php">Report Lost Item</a></p>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
