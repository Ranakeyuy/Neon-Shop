<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    if ($action === 'add') {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
        header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php'));
        exit;
    } elseif ($action === 'buy') {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
        header('Location: checkout.php');
        exit;
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
        header('Location: cart.php');
        exit;
    } elseif ($action === 'decrease') {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]--;
            if ($_SESSION['cart'][$id] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
        header('Location: cart.php');
        exit;
    }
}
$cart_count = 0;
if (is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
$cart_items = [];
$total_sum = 0.00;
if (!empty($_SESSION['cart'])) {
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Neon Tech Shop</title>
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
        <h2>Your Shopping <span>Cart</span></h2>
        <?php if (!empty($cart_items)): ?>
            <div class="cart-table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="table-img">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-cyan"><?php echo htmlspecialchars($item['category']); ?></span>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <a href="cart.php?action=decrease&id=<?php echo $item['id']; ?>" class="btn btn-secondary" style="padding: 0.2rem 0.6rem;">-</a>
                                        <span><?php echo $item['quantity']; ?></span>
                                        <a href="cart.php?action=add&id=<?php echo $item['id']; ?>" class="btn btn-secondary" style="padding: 0.2rem 0.6rem;">+</a>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                <td>
                                    <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn btn-action" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="cart-summary">
                    <div class="cart-total-label">Total Amount:</div>
                    <div class="cart-total-value">$<?php echo number_format($total_sum, 2); ?></div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                    <a href="checkout.php" class="btn btn-primary" style="padding: 0.8rem 2rem; font-size: 1rem;">Proceed to Checkout</a>
                </div>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; margin-top: 1.5rem;">
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Your cart is empty.</p>
                <a href="index.php" class="btn btn-primary">Go Shopping</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
