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
$allowed_categories = ['mobile', 'watch', 'tv', 'laptop'];
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";
if (in_array($category, $allowed_categories)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}
if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}
$query .= " ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$products_result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Neon Tech Shop</title>
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
        <div style="margin-bottom: 2rem; display: flex; justify-content: center;">
            <form action="index.php" method="GET" style="display: flex; gap: 0.5rem; width: 100%; max-width: 500px;">
                <?php if (in_array($category, $allowed_categories)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <?php endif; ?>
                <input type="text" name="search" class="form-control" placeholder="Search premium gear..." value="<?php echo htmlspecialchars($search); ?>" style="border-radius: 20px 0 0 20px;">
                <button type="submit" class="btn btn-primary" style="border-radius: 0 20px 20px 0; padding: 0.6rem 1.5rem;">Search</button>
            </form>
        </div>

        <div class="category-menu">
            <a href="index.php?category=all<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="category-btn <?php echo $category === 'all' ? 'active' : ''; ?>">All Products</a>
            <a href="index.php?category=mobile<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="category-btn <?php echo $category === 'mobile' ? 'active' : ''; ?>">Mobiles</a>
            <a href="index.php?category=watch<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="category-btn <?php echo $category === 'watch' ? 'active' : ''; ?>">Watches</a>
            <a href="index.php?category=tv<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="category-btn <?php echo $category === 'tv' ? 'active' : ''; ?>">TVs</a>
            <a href="index.php?category=laptop<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="category-btn <?php echo $category === 'laptop' ? 'active' : ''; ?>">Laptops</a>
        </div>

        <div class="product-grid">
            <?php if (mysqli_num_rows($products_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                            <div class="action-cluster">
                                <a href="view.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">View</a>
                                <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-primary">Add Cart</a>
                                <a href="cart.php?action=buy&id=<?php echo $product['id']; ?>" class="btn btn-action">Buy Now</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);">
                    No products found matching your criteria.
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
