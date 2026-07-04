<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'shopping_db';
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql_db = "CREATE DATABASE IF NOT EXISTS $db";
mysqli_query($conn, $sql_db);
if (!mysqli_select_db($conn, $db)) {
    die("Database selection failed");
}
$table_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    location TEXT NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
)";
mysqli_query($conn, $table_users);
$table_products = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL
)";
mysqli_query($conn, $table_products);
$table_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_name VARCHAR(100) NOT NULL,
    card_number VARCHAR(16) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
mysqli_query($conn, $table_orders);
$table_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";
mysqli_query($conn, $table_order_items);
$check_products = mysqli_query($conn, "SELECT id FROM products LIMIT 1");
if (mysqli_num_rows($check_products) == 0) {
    mysqli_query($conn, "INSERT INTO products (name, price, category, image, description) VALUES 
    ('CyberPhone X1', 999.00, 'mobile', 'phone.png', 'Next-generation quantum-dots smartphone with holographic projector, hyper-speed neural processing chip, and cyan luminescence frame.'),
    ('Quantum Pro 14', 1299.00, 'mobile', 'phone.png', 'Flagship cyber phone boasting 200MP bio-sensor camera, liquid metal frame, and quantum encryption standard.'),
    ('Neo Lite S26', 599.00, 'mobile', 'phone.png', 'Premium mid-range phone with sleek borderless design, 120Hz display, and rapid induction charge.'),
    ('Matrix Fold Z', 1799.00, 'mobile', 'phone.png', 'Revolutionary flexible screen smartphone with dual-display interface, reinforced titanium hinge, and neural assistance OS.'),
    ('Prism Fusion', 849.00, 'mobile', 'phone.png', 'Sleek design phone utilizing dynamic color-shifting rear chassis glass, advanced spatial audio, and thermal vapor chamber.'),
    ('Vapor Mini 12', 449.00, 'mobile', 'phone.png', 'Compact tech phone with pocket-friendly scale, full AMOLED screen, robust scratch resistance, and water-tight design.'),
    ('AeroWatch Neon', 299.00, 'watch', 'watch.png', 'Premium smart watch featuring integrated health diagnostics, cyber pink neon ring lighting, organic OLED face screen, and 7-day battery.'),
    ('Chrono Glow v3', 399.00, 'watch', 'watch.png', 'High-end timepiece featuring holographic hands, integrated wireless communicator, sapphire scratch-proof lens, and neon accents.'),
    ('Cyber Tracker S', 149.00, 'watch', 'watch.png', 'Lightweight fitness tracker with real-time biometric tracking, smart alerts, and sweat-resistant cybernetic strap.'),
    ('Pulsar Gold', 899.00, 'watch', 'watch.png', 'Limited edition smart luxury watch featuring real gold-plated bezel, encrypted hardware wallet, and biometric login interface.'),
    ('Cosmos Horizon', 499.00, 'watch', 'watch.png', 'Tactical outdoor watch with solar charging, altimeter, barometer, compass, and offline satellite grid navigation.'),
    ('Matrix Sync', 249.00, 'watch', 'watch.png', 'Minimalist watch with touch gesture control, seamless cellular integration, and custom cyan-glowing watch faces.'),
    ('QuantumTV 8K', 2499.00, 'tv', 'tv.png', 'Ultra-slim 8K smart television powered by neural upscaling, HDR-Extreme contrast mapping, and customizable ambient light syncing.'),
    ('OLED Spectra 4K', 1499.00, 'tv', 'tv.png', 'Flawless black-level OLED screen featuring wide color gamut, integrated dolby surround system, and game latency optimization.'),
    ('Lumina Slim 55', 799.00, 'tv', 'tv.png', 'Elegant 55-inch smart display with thin borders, smart home control panel, and premium voice command system.'),
    ('Aura Screen 75', 1999.00, 'tv', 'tv.png', 'Cinematic 75-inch display featuring quantum dot color array, high refresh rate, and anti-reflective screen coating.'),
    ('Neon Projector X', 1199.00, 'tv', 'tv.png', 'Ultra-short throw laser projector displaying up to 150 inches of crystal clear visual field with built-in soundbar.'),
    ('Horizon Flat', 649.00, 'tv', 'tv.png', 'Budget-friendly premium screen featuring smart OS hub, full array local dimming, and quick console connections.'),
    ('Nebula Gamer Pro', 1899.00, 'laptop', 'laptop.png', 'State-of-the-art gaming laptop loaded with liquid-cooling hardware, mechanical neon-backlit keys, and multi-core graphics array.'),
    ('Cyberbook Air', 999.00, 'laptop', 'laptop.png', 'Ultra-portable productivity laptop with full day battery life, silent fanless structure, and razor-sharp retina display.'),
    ('Titan Workstation', 2899.00, 'laptop', 'laptop.png', 'Heavy-duty developer workstation loaded with massive system memory, dedicated tensor chipsets, and dual screen display output.'),
    ('Zen Book Neo', 1399.00, 'laptop', 'laptop.png', 'Designer laptop boasting OLED touchscreen, active stylus support, aluminum chassis, and calibrated digital workspace.'),
    ('Carbon Stealth', 1699.00, 'laptop', 'laptop.png', 'Strong carbon fiber construct laptop featuring high security features, military-grade durability, and fast cellular band access.'),
    ('Pixel Book Lite', 749.00, 'laptop', 'laptop.png', 'Affordable lightweight laptop with responsive touch interface, streamlined secure OS, and cloud sync storage.')");
}
$admin_email = 'admin@shopping.com';
$check_admin = mysqli_query($conn, "SELECT * FROM users WHERE email = '$admin_email'");
if (mysqli_num_rows($check_admin) == 0) {
    $admin_pass = md5('admin123');
    mysqli_query($conn, "INSERT INTO users (fullname, email, password, mobile, location, role) VALUES ('Admin User', '$admin_email', '$admin_pass', '1234567890', 'Headquarters', 'admin')");
}
$user_email = 'user@shopping.com';
$check_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$user_email'");
if (mysqli_num_rows($check_user) == 0) {
    $user_pass = md5('user123');
    mysqli_query($conn, "INSERT INTO users (fullname, email, password, mobile, location, role) VALUES ('Demo Customer', '$user_email', '$user_pass', '0987654321', '123 Cyber Avenue, Neo City', 'user')");
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
