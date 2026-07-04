<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$card_name = mysqli_real_escape_string($conn, trim($_POST['card_name']));
$card_number = mysqli_real_escape_string($conn, trim($_POST['card_number']));
$masked_card = '************' . substr($card_number, -4);
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
$user_query = mysqli_query($conn, "SELECT fullname, location FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$customer_name = $user_data['fullname'];
$customer_location = $user_data['location'];
$insert_order = "INSERT INTO orders (user_id, card_name, card_number, total_price) VALUES ('$user_id', '$card_name', '$masked_card', '$total_sum')";
if (mysqli_query($conn, $insert_order)) {
    $order_id = mysqli_insert_id($conn);
    foreach ($cart_items as $item) {
        $pid = $item['id'];
        $pname = mysqli_real_escape_string($conn, $item['name']);
        $pprice = $item['price'];
        $pqty = $item['quantity'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES ('$order_id', '$pid', '$pname', '$pprice', '$pqty')");
    }
    $_SESSION['cart'] = [];
} else {
    die("Order processing failed");
}
$invoice = "=========================================\n";
$invoice .= "            NEON SHOP INVOICE            \n";
$invoice .= "=========================================\n";
$invoice .= "Order ID:    #" . str_pad($order_id, 6, "0", STR_PAD_LEFT) . "\n";
$invoice .= "Date:        " . date('Y-m-d H:i:s') . "\n";
$invoice .= "Customer:    " . str_pad($customer_name, 28) . "\n";
$invoice .= "Delivery:    " . str_pad(substr($customer_location, 0, 28), 28) . "\n";
$invoice .= "-----------------------------------------\n";
$invoice .= "ITEM                 QTY   PRICE   TOTAL\n";
$invoice .= "-----------------------------------------\n";
foreach ($cart_items as $item) {
    $name_part = str_pad(substr($item['name'], 0, 20), 20);
    $qty_part = str_pad($item['quantity'], 5, " ", STR_PAD_LEFT);
    $price_part = str_pad("$" . number_format($item['price'], 2), 8, " ", STR_PAD_LEFT);
    $sub_part = str_pad("$" . number_format($item['subtotal'], 2), 8, " ", STR_PAD_LEFT);
    $invoice .= "{$name_part}{$qty_part}{$price_part}{$sub_part}\n";
}
$invoice .= "-----------------------------------------\n";
$total_formatted = "$" . number_format($total_sum, 2);
$invoice .= "TOTAL AMOUNT:           " . str_pad($total_formatted, 17, " ", STR_PAD_LEFT) . "\n";
$invoice .= "=========================================\n";
$invoice .= "      THANK YOU FOR YOUR PURCHASE!       \n";
$invoice .= "=========================================\n";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Neon Tech Shop</title>
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
                <span class="cart-badge">0</span>
            </a>
            <a href="logout.php" class="btn btn-auth">Logout</a>
        </div>
    </header>

    <main class="container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Order <span>Successful</span></h2>
        <div class="invoice-box"><?php echo htmlspecialchars($invoice); ?></div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="index.php" class="btn btn-primary" style="padding: 0.8rem 2rem;">Continue Shopping</a>
        </div>
    </main>
</body>
</html>
