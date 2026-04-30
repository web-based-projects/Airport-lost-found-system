<?php
require_once '../config.php';

$code = isset($_GET['code']) ? sanitize($_GET['code']) : '';
$lost_item = null;

if (!empty($code)) {
    $query = "SELECT * FROM lost_items WHERE claim_code = '$code' AND status = 'matched'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $lost_item = mysqli_fetch_assoc($result);
    } else {
        die("Invalid claim code or item is not ready for claim.");
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
    <title>Claim Item - <?= SITE_NAME ?></title>
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
                <li><a href="../staff/login.php" class="btn btn-primary" style="padding: 5px 15px;">Staff Panel</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 class="mb-2">Item Claim Form</h2>
        <p class="mb-4 text-gray">Please fill this form before visiting the Lost & Found office to speed up the process.</p>
        
        <div class="alert alert-info mb-4">
            <strong>Claim Code:</strong> <?= htmlspecialchars($code) ?> <br>
            <strong>Item:</strong> <?= htmlspecialchars($lost_item['item_name']) ?>
        </div>

        <form action="process_claim_passenger.php" method="POST" id="claimForm" onsubmit="alert('Claim information saved! Please visit the office with your ID proof.'); return false;">
            <input type="hidden" name="claim_code" value="<?= htmlspecialchars($code) ?>">
            
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($lost_item['passenger_name']) ?>" disabled>
            </div>
            
            <div class="form-group">
                <label class="form-label">ID Proof Type *</label>
                <select name="id_type" class="form-control" required>
                    <option value="">Select ID Type</option>
                    <option value="Driver's License">Driver's License</option>
                    <option value="Passport">Passport</option>
                    <option value="National ID">National ID</option>
                    <option value="Other">Other Official ID</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">ID Proof Number *</label>
                <input type="text" name="id_number" class="form-control" required placeholder="e.g. A1234567">
            </div>
            
            <div class="form-group">
                <label class="form-label">Owner Signature (Type Full Name) *</label>
                <input type="text" name="signature" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100" style="padding: 15px; font-size: 1.1rem;">Generate Claim Pass</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
