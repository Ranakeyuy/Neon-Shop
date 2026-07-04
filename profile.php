<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
    $location = mysqli_real_escape_string($conn, trim($_POST['location']));
    if (empty($fullname) || empty($mobile) || empty($location)) {
        $error = 'All fields are required';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET fullname = ?, mobile = ?, location = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $fullname, $mobile, $location, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['fullname'] = $fullname;
            $success = 'Profile updated successfully!';
        } else {
            $error = 'Failed to update profile. Please try again.';
        }
    }
}
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Neon Tech Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"><span>NEON</span>SHOP</a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link">Shop</a>
            <a href="my_orders.php" class="nav-link">My Orders</a>
            <a href="profile.php" class="nav-link active">Profile</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="nav-link">Admin Panel</a>
            <?php endif; ?>
        </nav>
        <div class="nav-user">
            <span class="nav-link">Hi, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong> (<?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?>)</span>
            <a href="cart.php" class="cart-icon">
                Cart
                <span class="cart-badge"><?php echo $cart_count; ?></span>
            </a>
            <a href="logout.php" class="btn btn-auth">Logout</a>
        </div>
    </header>

    <main class="container">
        <div class="auth-card" style="margin: 2rem auto; max-width: 600px;">
            <h2 class="auth-title">My <span>Profile</span></h2>
            <p class="auth-subtitle">Update your personal account details</p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Email Address (Login ID - Read-Only)</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background: rgba(255, 255, 255, 0.03); color: var(--text-secondary);">
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" required value="<?php echo htmlspecialchars($user['fullname']); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" required value="<?php echo htmlspecialchars($user['mobile']); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Delivery Location</label>
                    <textarea name="location" class="form-control" required><?php echo htmlspecialchars($user['location']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-submit">Update Details</button>
            </form>
        </div>
    </main>
</body>
</html>
