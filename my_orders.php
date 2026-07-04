<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
$orders_query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Neon Tech Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"><span>NEON</span>SHOP</a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link">Shop</a>
            <a href="my_orders.php" class="nav-link active">My Orders</a>
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
        <h2>Your Historic <span>Orders</span></h2>
        <?php if (mysqli_num_rows($orders_query) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($orders_query)): ?>
                <div class="cart-table-container" style="margin-bottom: 2.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">Order ID:</span>
                            <strong style="color: var(--accent-cyan);">#<?php echo str_pad($order['id'], 6, "0", STR_PAD_LEFT); ?></strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">Date:</span>
                            <strong><?php echo htmlspecialchars($order['order_date']); ?></strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">Paid with card:</span>
                            <strong><?php echo htmlspecialchars($order['card_number']); ?></strong>
                        </div>
                        <div>
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">Total Paid:</span>
                            <strong style="color: var(--accent-pink); font-size: 1.2rem; text-shadow: var(--neon-pink);">$<?php echo number_format($order['total_price'], 2); ?></strong>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $order_id = $order['id'];
                            $items_query = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = '$order_id'");
                            while ($item = mysqli_fetch_assoc($items_query)):
                                $subtotal = $item['price'] * $item['quantity'];
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; margin-top: 1.5rem;">
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">You haven't placed any orders yet.</p>
                <a href="index.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
