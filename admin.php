<?php
require_once 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$success_msg = '';
$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (in_array($ext, $allowed)) {
        $unique_name = uniqid() . '.' . $ext;
        $target = 'uploads/' . $unique_name;
        if (move_uploaded_file($image_tmp, $target)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, price, category, image, description) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sdsss", $name, $price, $category, $unique_name, $description);
            mysqli_stmt_execute($stmt);
            $success_msg = "Product added successfully!";
        } else {
            $error_msg = "Failed to save uploaded image.";
        }
    } else {
        $error_msg = "Invalid file type. Only JPG, JPEG, PNG, WEBP, and GIF are allowed.";
    }
}
if (isset($_GET['delete_product_id'])) {
    $del_id = intval($_GET['delete_product_id']);
    $query_img = mysqli_query($conn, "SELECT image FROM products WHERE id = '$del_id'");
    if (mysqli_num_rows($query_img) > 0) {
        $prod = mysqli_fetch_assoc($query_img);
        $img_file = 'uploads/' . $prod['image'];
        if (file_exists($img_file)) {
            unlink($img_file);
        }
        mysqli_query($conn, "DELETE FROM products WHERE id = '$del_id'");
        $success_msg = "Product deleted successfully!";
    }
}
$products_result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
$orders_result = mysqli_query($conn, "SELECT orders.*, users.fullname FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.order_date DESC");
$total_sales_res = mysqli_query($conn, "SELECT SUM(total_price) AS total FROM orders");
$total_sales_row = mysqli_fetch_assoc($total_sales_res);
$total_sales = $total_sales_row['total'] ? $total_sales_row['total'] : 0.00;
$total_orders_res = mysqli_query($conn, "SELECT COUNT(id) AS count FROM orders");
$total_orders_row = mysqli_fetch_assoc($total_orders_res);
$total_orders = $total_orders_row['count'];
$total_users_res = mysqli_query($conn, "SELECT COUNT(id) AS count FROM users WHERE role = 'user'");
$total_users_row = mysqli_fetch_assoc($total_users_res);
$total_users = $total_users_row['count'];
$total_products_res = mysqli_query($conn, "SELECT COUNT(id) AS count FROM products");
$total_products_row = mysqli_fetch_assoc($total_products_res);
$total_products = $total_products_row['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Neon Tech Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo"><span>NEON</span>SHOP</a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link">Shop</a>
            <a href="my_orders.php" class="nav-link">My Orders</a>
            <a href="profile.php" class="nav-link">Profile</a>
            <a href="admin.php" class="nav-link active">Admin Panel</a>
        </nav>
        <div class="nav-user">
            <span class="nav-link">Hi, <strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong> (Admin)</span>
            <a href="logout.php" class="btn btn-auth">Logout</a>
        </div>
    </header>

    <main class="container">
        <h2>Admin <span>Control Panel</span></h2>
        
        <?php if ($success_msg): ?>
            <div class="alert alert-success" style="margin-top: 1rem;"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger" style="margin-top: 1rem;"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="stats-grid" style="margin-top: 2rem;">
            <div class="stats-card">
                <span class="stats-title">Total Sales</span>
                <span class="stats-value">$<?php echo number_format($total_sales, 2); ?></span>
            </div>
            <div class="stats-card alternate">
                <span class="stats-title">Total Orders</span>
                <span class="stats-value"><?php echo $total_orders; ?></span>
            </div>
            <div class="stats-card">
                <span class="stats-title">Registered Customers</span>
                <span class="stats-value"><?php echo $total_users; ?></span>
            </div>
            <div class="stats-card alternate">
                <span class="stats-title">Active Products</span>
                <span class="stats-value"><?php echo $total_products; ?></span>
            </div>
        </div>

        <div class="admin-layout">
            <aside class="admin-sidebar">
                <div class="admin-sidebar-title">Management</div>
                <div class="admin-menu">
                    <button class="admin-menu-btn active" data-target="sec-add-product">Add Product</button>
                    <button class="admin-menu-btn" data-target="sec-products">Active Products</button>
                    <button class="admin-menu-btn" data-target="sec-users">User Ledger</button>
                    <button class="admin-menu-btn" data-target="sec-orders">Order Tracking</button>
                </div>
            </aside>

            <div class="admin-content-area">
                <section id="sec-add-product" class="admin-section active">
                    <div class="checkout-card" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                        <h3 style="margin-bottom: 1.5rem;">Add New Product</h3>
                        <form action="admin.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-control" required>
                                        <option value="mobile">Mobile</option>
                                        <option value="watch">Watch</option>
                                        <option value="tv">TV</option>
                                        <option value="laptop">Laptop</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Product Image</label>
                                <input type="file" name="image" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Product Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                            <button type="submit" name="add_product" class="btn btn-submit">Save Product</button>
                        </form>
                    </div>
                </section>

                <section id="sec-products" class="admin-section">
                    <div class="cart-table-container" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                        <h3 style="margin-bottom: 1.5rem;">Active Products</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($products_result) > 0): ?>
                                    <?php while ($prod = mysqli_fetch_assoc($products_result)): ?>
                                        <tr>
                                            <td>
                                                <img src="uploads/<?php echo htmlspecialchars($prod['image']); ?>" alt="" class="table-img">
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($prod['name']); ?></strong></td>
                                            <td><span class="badge badge-cyan"><?php echo htmlspecialchars($prod['category']); ?></span></td>
                                            <td>$<?php echo number_format($prod['price'], 2); ?></td>
                                            <td>
                                                <a href="admin.php?delete_product_id=<?php echo $prod['id']; ?>" class="btn btn-action" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--text-secondary);">No products uploaded.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="sec-users" class="admin-section">
                    <div class="cart-table-container" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                        <h3 style="margin-bottom: 1.5rem;">Customer Ledger</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Account ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($users_result) > 0): ?>
                                    <?php while ($user_row = mysqli_fetch_assoc($users_result)): ?>
                                        <tr>
                                            <td><strong style="color: var(--accent-cyan);">#<?php echo str_pad($user_row['id'], 5, "0", STR_PAD_LEFT); ?></strong></td>
                                            <td><strong><?php echo htmlspecialchars($user_row['fullname']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($user_row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user_row['mobile']); ?></td>
                                            <td><?php echo htmlspecialchars($user_row['location']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--text-secondary);">No customers registered.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="sec-orders" class="admin-section">
                    <div class="cart-table-container" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                        <h3 style="margin-bottom: 1.5rem;">Transaction Tracking Logs</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Purchased Items</th>
                                    <th>Total Paid</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($orders_result) > 0): ?>
                                    <?php while ($ord = mysqli_fetch_assoc($orders_result)): ?>
                                        <tr>
                                            <td><strong style="color: var(--accent-pink);">#<?php echo str_pad($ord['id'], 6, "0", STR_PAD_LEFT); ?></strong></td>
                                            <td><strong><?php echo htmlspecialchars($ord['fullname']); ?></strong></td>
                                            <td>
                                                <ul style="list-style: none; padding: 0;">
                                                    <?php
                                                    $ord_id = $ord['id'];
                                                    $it_res = mysqli_query($conn, "SELECT product_name, quantity FROM order_items WHERE order_id = '$ord_id'");
                                                    while ($item_row = mysqli_fetch_assoc($it_res)):
                                                    ?>
                                                        <li style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo htmlspecialchars($item_row['product_name']); ?> (x<?php echo $item_row['quantity']; ?>)</li>
                                                    <?php endwhile; ?>
                                                </ul>
                                            </td>
                                            <td><strong>$<?php echo number_format($ord['total_price'], 2); ?></strong></td>
                                            <td><span style="font-size: 0.85rem; color: var(--text-secondary);"><?php echo htmlspecialchars($ord['order_date']); ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--text-secondary);">No transaction records yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.admin-menu-btn');
            const sections = document.querySelectorAll('.admin-section');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));
                    tab.classList.add('active');
                    const targetSection = document.getElementById(tab.dataset.target);
                    if (targetSection) {
                        targetSection.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>
