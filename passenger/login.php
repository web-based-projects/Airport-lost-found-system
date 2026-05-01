<?php
require_once '../config.php';
if (isStaffLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}
$error = isset($_GET['error']) ? 'Invalid username or password' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: var(--light); display: flex; align-items: center; justify-content: center; min-height: 100vh;">

<div class="card" style="width: 100%; max-width: 400px; padding: 2.5rem;">
    <div class="text-center mb-4">
        <div class="logo justify-center mb-4" style="justify-content: center;"><img src="../assets/logo.png" alt="Ethiopian Airlines" style="height: 60px;"></div>
        <h2>Staff Portal</h2>
        <p class="text-gray mt-2">Lost & Found Management System</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error mb-4"><?= $error ?></div>
    <?php endif; ?>

    <form action="authenticate.php" method="POST">
        <div class="form-group">
            <label class="form-label">Username</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 15px; top: 12px;"><i class="fas fa-user text-gray"></i></span>
                <input type="text" name="username" class="form-control" style="padding-left: 40px;" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Password</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 15px; top: 12px;"><i class="fas fa-lock text-gray"></i></span>
                <input type="password" name="password" class="form-control" style="padding-left: 40px;" required>
            </div>
        </div>
        
        <div class="form-group d-flex align-center justify-between">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="remember"> Remember Me
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-2" style="padding: 12px;">Login</button>
        
        <div class="text-center mt-4 text-sm" style="color: var(--gray); font-size: 0.9rem;">
            Hint: Demo credentials: staff / staff123
        </div>
        <div class="text-center mt-2">
            <a href="../index.php" style="font-size: 0.9rem;">← Back to Home</a>
        </div>
    </form>
</div>

</body>
</html>
