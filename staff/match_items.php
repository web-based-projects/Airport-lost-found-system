<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

$lost_search = isset($_GET['lost_search']) ? sanitize($_GET['lost_search']) : '';
$found_search = isset($_GET['found_search']) ? sanitize($_GET['found_search']) : '';

// Fetch unmatched lost items
$lost_query = "SELECT * FROM lost_items WHERE status = 'pending'";
if (!empty($lost_search)) {
    $lost_query .= " AND (claim_code LIKE '%$lost_search%' OR item_name LIKE '%$lost_search%')";
}
$lost_query .= " ORDER BY lost_date DESC LIMIT 50";
$lost_result = mysqli_query($conn, $lost_query);

// Fetch unmatched found items
$found_query = "SELECT * FROM found_items WHERE status = 'unclaimed'";
if (!empty($found_search)) {
    $found_query .= " AND (found_code LIKE '%$found_search%' OR item_name LIKE '%$found_search%')";
}
$found_query .= " ORDER BY found_date DESC LIMIT 50";
$found_result = mysqli_query($conn, $found_query);

$success_msg = isset($_SESSION['match_success']) ? $_SESSION['match_success'] : '';
unset($_SESSION['match_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Items - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .item-list { max-height: 500px; overflow-y: auto; }
        .match-card { border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; }
        .match-card:hover { border-color: var(--secondary); background: #F3F4F6; }
        .match-card.selected { border-color: var(--success); border-width: 2px; background: #ECFDF5; }
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + label .match-card { border-color: var(--success); border-width: 2px; background: #ECFDF5; box-shadow: 0 0 0 1px var(--success); }
    </style>
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
                <li><a href="match_items.php" class="active">Match Items</a></li>
                <li><a href="logout.php" class="btn btn-danger text-white" style="color:white; padding: 5px 15px;">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h2 class="mb-4">Manual Item Matching</h2>
    
    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php endif; ?>

    <form action="process_match.php" method="POST" id="matchForm" onsubmit="return validateMatch()">
        <div class="d-flex gap-1" style="flex-wrap: wrap;">
            
            <!-- Left Column: Lost Items -->
            <div class="card" style="flex: 1; min-width: 300px;">
                <h3 class="mb-2">1. Select Lost Item</h3>
                <div class="mb-2">
                    <input type="text" id="filterLost" class="form-control" placeholder="Filter lost items..." onkeyup="filterDivs('filterLost', 'lostList')">
                </div>
                <div class="item-list" id="lostList">
                    <?php if (mysqli_num_rows($lost_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($lost_result)): ?>
                            <div class="item-div">
                                <input type="radio" name="lost_id" id="lost_<?= $row['id'] ?>" value="<?= $row['id'] ?>">
                                <label for="lost_<?= $row['id'] ?>" style="display:block; width: 100%;">
                                    <div class="match-card">
                                        <div class="d-flex justify-between">
                                            <strong><?= $row['claim_code'] ?></strong>
                                            <span class="text-gray text-sm"><?= date('M d', strtotime($row['lost_date'])) ?></span>
                                        </div>
                                        <div style="font-weight: 600; margin-top: 5px;"><?= htmlspecialchars($row['item_name']) ?></div>
                                        <div class="text-sm text-gray" style="font-size: 0.85rem; margin-top: 5px;">
                                            Color: <?= htmlspecialchars($row['item_color']) ?> | Loc: <?= htmlspecialchars($row['lost_location']) ?>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray p-4">No pending lost items.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Found Items -->
            <div class="card" style="flex: 1; min-width: 300px;">
                <h3 class="mb-2">2. Select Found Item</h3>
                <div class="mb-2">
                    <input type="text" id="filterFound" class="form-control" placeholder="Filter found items..." onkeyup="filterDivs('filterFound', 'foundList')">
                </div>
                <div class="item-list" id="foundList">
                    <?php if (mysqli_num_rows($found_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($found_result)): ?>
                            <div class="item-div">
                                <input type="radio" name="found_id" id="found_<?= $row['id'] ?>" value="<?= $row['id'] ?>">
                                <label for="found_<?= $row['id'] ?>" style="display:block; width: 100%;">
                                    <div class="match-card">
                                        <div class="d-flex justify-between">
                                            <strong><?= $row['found_code'] ?></strong>
                                            <span class="text-gray text-sm"><?= date('M d', strtotime($row['found_date'])) ?></span>
                                        </div>
                                        <div style="font-weight: 600; margin-top: 5px;"><?= htmlspecialchars($row['item_name']) ?></div>
                                        <div class="text-sm text-gray" style="font-size: 0.85rem; margin-top: 5px;">
                                            Color: <?= htmlspecialchars($row['item_color']) ?> | Loc: <?= htmlspecialchars($row['found_location']) ?>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray p-4">No unclaimed found items.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card mt-4 text-center">
            <h3 class="mb-2">3. Confirm Match</h3>
            <p class="text-gray mb-4">Please select one item from each list above to match them.</p>
            <button type="submit" class="btn btn-success" style="font-size: 1.2rem; padding: 15px 40px;">Confirm Match</button>
        </div>
    </form>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
<script>
function filterDivs(inputId, listId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const list = document.getElementById(listId);
    const divs = list.getElementsByClassName('item-div');

    for (let i = 0; i < divs.length; i++) {
        let textValue = divs[i].textContent || divs[i].innerText;
        if (textValue.toUpperCase().indexOf(filter) > -1) {
            divs[i].style.display = "";
        } else {
            divs[i].style.display = "none";
        }
    }
}

function validateMatch() {
    const lostSelected = document.querySelector('input[name="lost_id"]:checked');
    const foundSelected = document.querySelector('input[name="found_id"]:checked');
    
    if (!lostSelected || !foundSelected) {
        alert("Please select ONE Lost Item and ONE Found Item to match.");
        return false;
    }
    
    return confirm("Are you sure you want to match these items? This action will notify the passenger.");
}
</script>
</body>
</html>
