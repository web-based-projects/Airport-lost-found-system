<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

$found_id = isset($_GET['found_id']) ? (int)$_GET['found_id'] : 0;
$found_item = null;
$lost_item = null;

if ($found_id > 0) {
    $query = "SELECT f.*, l.claim_code, l.passenger_name, l.phone, l.email, l.id as lost_item_id 
              FROM found_items f 
              JOIN lost_items l ON f.matched_to = l.id 
              WHERE f.id = $found_id AND f.status = 'matched'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $found_item = $data;
    }
}

// Get all matched items for dropdown if no specific ID provided
$matched_items_query = "SELECT f.id, f.found_code, l.claim_code, l.passenger_name, f.item_name 
                        FROM found_items f 
                        JOIN lost_items l ON f.matched_to = l.id 
                        WHERE f.status = 'matched'";
$matched_items = mysqli_query($conn, $matched_items_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Return - <?= SITE_NAME ?></title>
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

<div class="container">
    <div class="card mb-4" style="max-width: 800px; margin: 0 auto;">
        <h2 class="mb-4">Process Item Return</h2>
        
        <?php if (!$found_item): ?>
            <div class="form-group mb-4">
                <label class="form-label">Select Matched Item to Return</label>
                <select class="form-control" onchange="if(this.value) window.location.href='return_item.php?found_id='+this.value">
                    <option value="">-- Select an item --</option>
                    <?php while($row = mysqli_fetch_assoc($matched_items)): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['found_code'] ?> / <?= $row['claim_code'] ?> - <?= htmlspecialchars($row['item_name']) ?> (<?= htmlspecialchars($row['passenger_name']) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <?php if(mysqli_num_rows($matched_items) == 0): ?>
                <div class="alert alert-info">There are currently no items with "Matched" status waiting to be returned.</div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="alert alert-info mb-4">
                You are processing the return for <strong><?= htmlspecialchars($found_item['item_name']) ?></strong> to <strong><?= htmlspecialchars($found_item['passenger_name']) ?></strong>.
            </div>

            <form action="process_return.php" method="POST" id="returnForm" onsubmit="return validateForm('returnForm')">
                <input type="hidden" name="found_id" value="<?= $found_id ?>">
                <input type="hidden" name="lost_id" value="<?= $found_item['lost_item_id'] ?>">
                
                <div class="d-flex gap-1 mb-4" style="flex-wrap: wrap;">
                    <div style="flex: 1; background: #F9FAFB; padding: 15px; border-radius: 8px; border: 1px solid #E5E7EB;">
                        <h4 class="mb-2" style="font-size: 0.9rem; color: var(--gray);">Lost Report</h4>
                        <p><strong>Code:</strong> <?= $found_item['claim_code'] ?></p>
                        <p><strong>Owner:</strong> <?= htmlspecialchars($found_item['passenger_name']) ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($found_item['phone']) ?></p>
                    </div>
                    <div style="flex: 1; background: #F9FAFB; padding: 15px; border-radius: 8px; border: 1px solid #E5E7EB;">
                        <h4 class="mb-2" style="font-size: 0.9rem; color: var(--gray);">Found Record</h4>
                        <p><strong>Code:</strong> <?= $found_item['found_code'] ?></p>
                        <p><strong>Storage:</strong> <?= htmlspecialchars($found_item['storage_location']) ?></p>
                    </div>
                </div>

                <h3 class="mb-2 mt-4">Verification Details</h3>
                <div class="form-group d-flex gap-1" style="flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label">ID Proof Type *</label>
                        <select name="id_type" class="form-control" required>
                            <option value="">Select ID Type</option>
                            <option value="Driver's License">Driver's License</option>
                            <option value="Passport">Passport</option>
                            <option value="National ID">National ID</option>
                            <option value="Other">Other Official ID</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label">ID Proof Number *</label>
                        <input type="text" name="id_number" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group d-flex gap-1" style="flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label">Receiver Name *</label>
                        <input type="text" name="receiver_name" class="form-control" value="<?= htmlspecialchars($found_item['passenger_name']) ?>" required>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label class="form-label">Staff Processing *</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['staff_name']) ?>" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Receiver Signature (Type Full Name) *</label>
                    <input type="text" name="signature" class="form-control" required placeholder="I confirm receipt of the item">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Additional Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any comments regarding condition upon return..."></textarea>
                </div>

                <div class="mt-4 d-flex gap-1">
                    <a href="return_item.php" class="btn btn-outline" style="flex: 1; text-align: center;">Cancel</a>
                    <button type="submit" class="btn btn-info" style="flex: 2;">Confirm Handover & Close Case</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
