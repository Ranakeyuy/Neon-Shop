<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Neon Tech Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"><span>NEON</span>SHOP</a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link active">Shop</a>
            <a href="my_orders.php" class="nav-link">My Orders</a>
            <a href="profile.php" class="nav-link">Profile</a>
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
        <div class="product-detail-layout">
            <div class="product-detail-image-box">
                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-detail-img">
            </div>
            <div class="product-detail-info">
                <span class="product-detail-category"><?php echo htmlspecialchars($product['category']); ?></span>
                <h1 class="product-detail-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-detail-price">$<?php echo number_format($product['price'], 2); ?></div>
                <div class="product-detail-description">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                <div class="product-detail-actions">
                    <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-primary">Add to Cart</a>
                    <a href="cart.php?action=buy&id=<?php echo $product['id']; ?>" class="btn btn-action">Buy It Now</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
