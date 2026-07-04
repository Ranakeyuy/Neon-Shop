<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
}
$cart_count = 0;
if (is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
$cart_items = [];
$total_sum = 0.00;
$ids = implode(',', array_keys($_SESSION['cart']));
$clean_ids = preg_replace('/[^0-9,]/', '', $ids);
if (!empty($clean_ids)) {
    $query = "SELECT * FROM products WHERE id IN ($clean_ids)";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $pid = $row['id'];
        $qty = $_SESSION['cart'][$pid];
        $subtotal = $row['price'] * $qty;
        $total_sum += $subtotal;
        $row['quantity'] = $qty;
        $row['subtotal'] = $subtotal;
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Neon Tech Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"><span>NEON</span>SHOP</a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link">Shop</a>
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
        <h2>Secure <span>Checkout</span></h2>
        <div class="checkout-grid">
            <div class="checkout-card">
                <form action="bill.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Name on Card</label>
                        <input type="text" name="card_name" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Card Number (16 Digits)</label>
                        <input type="text" name="card_number" class="form-control" placeholder="1234567890123456" pattern="\d{16}" title="Please enter exactly 16 digits" required maxlength="16">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Expiry (MM/YY)</label>
                            <input type="text" name="card_expiry" class="form-control" placeholder="12/28" pattern="(0[1-9]|1[0-2])\/?([0-9]{2})" title="Please enter MM/YY" required maxlength="5">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CVV (3 Digits)</label>
                            <input type="password" name="card_cvv" class="form-control" placeholder="123" pattern="\d{3}" title="Please enter exactly 3 digits" required maxlength="3">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-submit" style="margin-top: 1.5rem;">Pay & Generate Invoice</button>
                </form>
            </div>
            
            <div class="order-summary-box">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Order Summary</h3>
                <div style="max-height: 250px; overflow-y: auto; margin-bottom: 1rem; padding-right: 0.5rem;">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div>
                                <span style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($item['name']); ?></span>
                                <br>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">Qty: <?php echo $item['quantity']; ?></span>
                            </div>
                            <span style="font-weight: 500;">$<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-item summary-total">
                    <span>Total Amount:</span>
                    <span>$<?php echo number_format($total_sum, 2); ?></span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
