<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: report.php");
    exit();
}

$passenger_name = sanitize($_POST['passenger_name']);
$email = sanitize($_POST['email']);
$phone = sanitize($_POST['phone']);
$item_name = sanitize($_POST['item_name']);
$item_color = sanitize($_POST['item_color']);
$brand = sanitize($_POST['brand']);
$item_description = sanitize($_POST['item_description']);
$lost_location = sanitize($_POST['lost_location']);
$lost_date = sanitize($_POST['lost_date']);

$claim_code = generateClaimCode();
$photo_path = '';

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['photo']['size'] > MAX_FILE_SIZE) {
        die("Error: File size too large. Max 5MB allowed.");
    }
    $upload = uploadFile($_FILES['photo'], 'lost');
    if ($upload) {
        $photo_path = $upload;
    }
}

$query = "INSERT INTO lost_items (claim_code, passenger_name, email, phone, item_name, item_description, item_color, brand, lost_location, lost_date, photo_path) 
          VALUES ('$claim_code', '$passenger_name', '$email', '$phone', '$item_name', '$item_description', '$item_color', '$brand', '$lost_location', '$lost_date', '$photo_path')";

if (mysqli_query($conn, $query)) {
    $success = true;
} else {
    $success = false;
    $error = mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Saved - <?= SITE_NAME ?></title>
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
                <li><a href="check_status.php">Check Status</a></li>
                <li><a href="../staff/login.php" class="btn btn-primary text-white" style="color:white; padding: 5px 15px;">Staff Panel</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container text-center">
    <?php if ($success): ?>
        <div class="card" style="max-width: 600px; margin: 2rem auto;">
            <div class="mb-4" style="font-size: 4rem; color: var(--success);"><i class="fas fa-check-circle"></i></div>
            <h2 class="mb-2">Report Submitted Successfully!</h2>
            <p class="mb-4">We have received your lost item report. Please keep the claim code below to check your status.</p>
            
            <div style="background: var(--light); padding: 2rem; border-radius: var(--border-radius-card); border: 2px dashed var(--secondary); margin-bottom: 2rem;">
                <p class="mb-2" style="color: var(--gray); font-weight: 600;">YOUR CLAIM CODE</p>
                <h1 id="claimCodeDisplay" style="font-size: 2.5rem; letter-spacing: 2px; color: var(--primary);"><?= $claim_code ?></h1>
                <button onclick="copyToClipboard('claimCodeDisplay')" class="btn btn-outline mt-2"><i class="fas fa-copy"></i> Copy Code</button>
            </div>
            
            <div class="d-flex gap-1 justify-center" style="justify-content: center; flex-wrap: wrap;">
                <a href="view_status.php?code=<?= $claim_code ?>" class="btn btn-primary">Check Status Now</a>
                <a href="report.php" class="btn btn-secondary">Report Another Item</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="max-width: 600px; margin: 2rem auto;">
            <div class="mb-4" style="font-size: 4rem; color: var(--danger);"><i class="fas fa-times-circle"></i></div>
            <h2 class="mb-2" style="color: var(--danger);">Submission Failed</h2>
            <p>Sorry, there was an error submitting your report: <?= $error ?></p>
            <a href="report.php" class="btn btn-primary mt-4">Try Again</a>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
