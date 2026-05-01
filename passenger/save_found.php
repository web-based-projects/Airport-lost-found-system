<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: report_found.php");
    exit();
}

$staff_name = sanitize($_SESSION['staff_name']);
$item_name = sanitize($_POST['item_name']);
$item_color = sanitize($_POST['item_color']);
$brand = sanitize($_POST['brand']);
$item_description = sanitize($_POST['item_description']);
$found_location = sanitize($_POST['found_location']);
$found_date = sanitize($_POST['found_date']);
$storage_location = sanitize($_POST['storage_location']);

$found_code = generateFoundCode();
$photo_path = '';

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['photo']['size'] > MAX_FILE_SIZE) {
        die("Error: File size too large. Max 5MB allowed.");
    }
    $upload = uploadFile($_FILES['photo'], 'found');
    if ($upload) {
        $photo_path = $upload;
    }
}

$query = "INSERT INTO found_items (found_code, staff_name, item_name, item_description, item_color, brand, found_location, found_date, storage_location, photo_path) 
          VALUES ('$found_code', '$staff_name', '$item_name', '$item_description', '$item_color', '$brand', '$found_location', '$found_date', '$storage_location', '$photo_path')";

$success = mysqli_query($conn, $query);
$new_found_id = mysqli_insert_id($conn);

// Auto-match algorithm
$potential_matches = [];
if ($success) {
    // Basic auto-match: pending items with similar name or color, lost before or on found date
    $match_query = "SELECT * FROM lost_items 
                    WHERE status = 'pending' 
                    AND lost_date <= '$found_date'
                    AND (item_name LIKE '%$item_name%' OR item_name LIKE '%" . explode(' ', $item_name)[0] . "%' OR item_color = '$item_color')
                    ORDER BY lost_date DESC LIMIT 5";
    $match_result = mysqli_query($conn, $match_query);
    while($row = mysqli_fetch_assoc($match_result)) {
        $potential_matches[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Found Item Saved - <?= SITE_NAME ?></title>
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

<div class="container text-center">
    <?php if ($success): ?>
        <div class="card" style="max-width: 800px; margin: 2rem auto;">
            <div class="mb-4" style="font-size: 4rem; color: var(--success);"><i class="fas fa-check-circle"></i></div>
            <h2 class="mb-2">Found Item Logged Successfully!</h2>
            <p class="mb-4 text-gray">The item has been saved to the database.</p>
            
            <div style="background: var(--light); padding: 1.5rem; border-radius: var(--border-radius-card); border: 2px dashed var(--success); margin-bottom: 2rem; display: inline-block;">
                <p class="mb-2" style="color: var(--gray); font-weight: 600; font-size: 0.9rem;">FOUND ITEM CODE</p>
                <h2 style="letter-spacing: 2px; color: var(--success);"><?= $found_code ?></h2>
            </div>
            
            <?php if (!empty($potential_matches)): ?>
                <div class="alert alert-warning mb-4 text-left">
                    <h3 class="mb-2"><i class="fas fa-exclamation-triangle text-warning"></i> Potential Matches Found!</h3>
                    <p class="mb-2">We found <?= count($potential_matches) ?> lost reports that might match this item:</p>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Lost Code</th>
                                    <th>Passenger</th>
                                    <th>Item</th>
                                    <th>Date Lost</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($potential_matches as $match): ?>
                                <tr>
                                    <td><strong><?= $match['claim_code'] ?></strong></td>
                                    <td><?= htmlspecialchars($match['passenger_name']) ?></td>
                                    <td><?= htmlspecialchars($match['item_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($match['lost_date'])) ?></td>
                                    <td>
                                        <form action="process_match.php" method="POST" style="display:inline;" onsubmit="return confirmAction('Confirm this match?');">
                                            <input type="hidden" name="lost_id" value="<?= $match['id'] ?>">
                                            <input type="hidden" name="found_id" value="<?= $new_found_id ?>">
                                            <button type="submit" class="btn btn-success" style="padding: 5px 10px; font-size: 0.8rem;">Match Now</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-1 justify-center mt-4" style="flex-wrap: wrap;">
                <a href="view_found.php" class="btn btn-primary">View All Found Items</a>
                <a href="report_found.php" class="btn btn-secondary">Log Another Item</a>
                <a href="match_items.php" class="btn btn-outline">Go to Match System</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="max-width: 600px; margin: 2rem auto;">
            <div class="mb-4" style="font-size: 4rem; color: var(--danger);"><i class="fas fa-times-circle"></i></div>
            <h2 class="mb-2" style="color: var(--danger);">Submission Failed</h2>
            <p>Error saving record: <?= mysqli_error($conn) ?></p>
            <a href="report_found.php" class="btn btn-primary mt-4">Try Again</a>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
</body>
</html>
