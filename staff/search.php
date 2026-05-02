<?php
require_once '../config.php';
if (!isStaffLoggedIn()) {
    header("Location: login.php");
    exit();
}

$q = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$type = isset($_GET['type']) ? sanitize($_GET['type']) : 'both';

$lost_results = null;
$found_results = null;

if (!empty($q)) {
    if ($type == 'both' || $type == 'lost') {
        $query = "SELECT * FROM lost_items 
                  WHERE claim_code LIKE '%$q%' 
                  OR passenger_name LIKE '%$q%' 
                  OR email LIKE '%$q%' 
                  OR phone LIKE '%$q%' 
                  OR item_name LIKE '%$q%' 
                  OR item_description LIKE '%$q%'
                  ORDER BY lost_date DESC";
        $lost_results = mysqli_query($conn, $query);
    }
    
    if ($type == 'both' || $type == 'found') {
        $query = "SELECT * FROM found_items 
                  WHERE found_code LIKE '%$q%' 
                  OR staff_name LIKE '%$q%' 
                  OR item_name LIKE '%$q%' 
                  OR item_description LIKE '%$q%' 
                  OR storage_location LIKE '%$q%'
                  ORDER BY found_date DESC";
        $found_results = mysqli_query($conn, $query);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Search - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .highlight { background-color: #FEF08A; padding: 2px 4px; border-radius: 2px; }
        .tabs { display: flex; border-bottom: 2px solid #E5E7EB; margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; font-weight: 600; color: var(--gray); }
        .tab.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
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
                <li><a href="match_items.php">Match Items</a></li>
                <li><a href="logout.php" class="btn btn-danger text-white" style="color:white; padding: 5px 15px;">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="card mb-4">
        <h2 class="mb-4 text-center">Global Search</h2>
        
        <form action="" method="GET" style="max-width: 600px; margin: 0 auto;">
            <div class="d-flex mb-2">
                <input type="text" name="q" class="form-control" placeholder="Enter keywords, name, or code..." value="<?= htmlspecialchars($q) ?>" style="border-radius: 8px 0 0 8px; font-size: 1.1rem; padding: 15px;">
                <button type="submit" class="btn btn-primary" style="border-radius: 0 8px 8px 0; padding: 0 25px;">Search</button>
            </div>
            <div class="d-flex justify-center gap-1 mt-2">
                <label><input type="radio" name="type" value="both" <?= $type == 'both' ? 'checked' : '' ?>> Both</label>
                <label><input type="radio" name="type" value="lost" <?= $type == 'lost' ? 'checked' : '' ?>> Lost Items Only</label>
                <label><input type="radio" name="type" value="found" <?= $type == 'found' ? 'checked' : '' ?>> Found Items Only</label>
            </div>
        </form>
    </div>

    <?php if (!empty($q)): ?>
        <?php 
        $lost_count = $lost_results ? mysqli_num_rows($lost_results) : 0;
        $found_count = $found_results ? mysqli_num_rows($found_results) : 0;
        ?>
        
        <h3 class="mb-4">Search Results for "<?= htmlspecialchars($q) ?>" (<?= $lost_count + $found_count ?> total)</h3>

        <div class="tabs">
            <?php if ($type == 'both' || $type == 'lost'): ?>
                <div class="tab <?= ($lost_count > 0 || $type == 'lost') ? 'active' : '' ?>" onclick="switchTab(event, 'lostResults')">
                    Lost Items (<?= $lost_count ?>)
                </div>
            <?php endif; ?>
            <?php if ($type == 'both' || $type == 'found'): ?>
                <div class="tab <?= ($lost_count == 0 && $type != 'lost') ? 'active' : '' ?>" onclick="switchTab(event, 'foundResults')">
                    Found Items (<?= $found_count ?>)
                </div>
            <?php endif; ?>
        </div>

        <?php if ($type == 'both' || $type == 'lost'): ?>
        <div id="lostResults" class="tab-content <?= ($lost_count > 0 || $type == 'lost') ? 'active' : '' ?>">
            <?php if ($lost_count > 0): ?>
                <div class="table-responsive card">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Item</th>
                                <th>Passenger</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($lost_results)): ?>
                            <tr>
                                <td><strong><?= $row['claim_code'] ?></strong></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                                <td><?= getStatusBadge($row['status']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card text-center p-4"><p>No lost items found matching "<?= htmlspecialchars($q) ?>"</p></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($type == 'both' || $type == 'found'): ?>
        <div id="foundResults" class="tab-content <?= ($lost_count == 0 && $type != 'lost') ? 'active' : '' ?>">
            <?php if ($found_count > 0): ?>
                <div class="table-responsive card">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Item</th>
                                <th>Storage</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($found_results)): ?>
                            <tr>
                                <td><strong><?= $row['found_code'] ?></strong></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['storage_location']) ?></td>
                                <td><?= getStatusBadge($row['status']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="card text-center p-4"><p>No found items found matching "<?= htmlspecialchars($q) ?>"</p></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Staff Portal.</p>
</footer>

<script src="../script.js"></script>
<script>
function switchTab(evt, tabId) {
    const tabs = document.getElementsByClassName('tab');
    const contents = document.getElementsByClassName('tab-content');
    
    for (let i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
    }
    for (let i = 0; i < contents.length; i++) {
        contents[i].classList.remove('active');
    }
    
    evt.currentTarget.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}
</script>
</body>
</html>
