<?php
require_once 'config.php';

// Fetch stats for homepage
$stats = [
    'total_lost' => 0,
    'total_found' => 0,
    'returned' => 0,
    'recovery_rate' => '0%'
];

$query_lost = "SELECT COUNT(*) as count FROM lost_items";
$res_lost = mysqli_query($conn, $query_lost);
if($res_lost && $row = mysqli_fetch_assoc($res_lost)) {
    $stats['total_lost'] = $row['count'];
}

$query_found = "SELECT COUNT(*) as count FROM found_items";
$res_found = mysqli_query($conn, $query_found);
if($res_found && $row = mysqli_fetch_assoc($res_found)) {
    $stats['total_found'] = $row['count'];
}

$query_returned = "SELECT COUNT(*) as count FROM lost_items WHERE status = 'returned'";
$res_returned = mysqli_query($conn, $query_returned);
if($res_returned && $row = mysqli_fetch_assoc($res_returned)) {
    $stats['returned'] = $row['count'];
}

if ($stats['total_lost'] > 0) {
    $stats['recovery_rate'] = round(($stats['returned'] / $stats['total_lost']) * 100) . '%';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header>
    <div class="nav-container">
        <a href="index.php" class="logo"><img src="assets/logo.png" alt="Ethiopian Airlines"></a>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="passenger/report.php">Report Lost Item</a></li>
                <li><a href="passenger/check_status.php">Check Status</a></li>
                <li><a href="staff/login.php" class="btn btn-primary" style="padding: 5px 15px;">Staff Panel</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <section class="hero">
        <h1>Lost Something on your Journey?</h1>
        <p>We are here to help you reconnect with your belongings. Report lost items or check if they've been found at the hub of Africa.</p>
        <div class="hero-btns">
            <a href="passenger/report.php" class="btn btn-white">Report Lost Item</a>
            <a href="passenger/check_status.php" class="btn btn-outline-white">Check Status</a>
        </div>
    </section>

    <section class="stats-grid">
        <div class="stat-card">
            <h3>Total Items Lost</h3>
            <div class="value"><?= $stats['total_lost'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Items Found</h3>
            <div class="value"><?= $stats['total_found'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Items Returned</h3>
            <div class="value"><?= $stats['returned'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Recovery Rate</h3>
            <div class="value"><?= $stats['recovery_rate'] ?></div>
        </div>
    </section>

    <section class="card-grid">
        <div class="card text-center">
            <h2><i class="fas fa-bullhorn"></i> Report Lost Item</h2>
            <p class="mb-4 mt-2">File a report for your lost item. Provide details and photos to help us match it quickly.</p>
            <a href="passenger/report.php" class="btn btn-primary w-100">Report Now</a>
        </div>
        <div class="card text-center">
            <h2><i class="fas fa-search"></i> Check Status</h2>
            <p class="mb-4 mt-2">Already filed a report? Track the status of your claim using your unique claim code.</p>
            <a href="passenger/check_status.php" class="btn btn-secondary w-100">Check Status</a>
        </div>
        <div class="card text-center">
            <h2><i class="fas fa-user-shield"></i> Staff Panel</h2>
            <p class="mb-4 mt-2">Airport staff login area for managing found items, matching, and returning to passengers.</p>
            <a href="staff/login.php" class="btn btn-outline w-100">Staff Login</a>
        </div>
    </section>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    <p>Contact Support: support@airport.example.com</p>
</footer>

<script src="script.js"></script>
</body>
</html>
