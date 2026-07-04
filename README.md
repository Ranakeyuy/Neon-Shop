# Dark Neon Tech E-Commerce Shop

A complete, high-performance E-Commerce platform built in procedural PHP and MySQL, styled with an ultra-modern dark theme and premium neon cyan and pink accents.

## Default Credentials

### 1. Administrator Account
* **Email ID**: `admin@shopping.com`
* **Password**: `admin123`
* **Privileges**: Accesses the Admin Control Panel (`admin.php`), views transactions, lists active products, deletes items, manages files, and reviews the customer ledger.

### 2. Standard Customer Account
* **Email ID**: `user@shopping.com`
* **Password**: `user123`
* **Privileges**: Browses catalog, filters categories, searches items, adds products to cart, purchases goods, edits profile details, and views order histories.

---

## Setup Instructions

1. **Move files**: Ensure all project files are located in your XAMPP web root directory:
   `C:/xampp/htdocs/E-commerce/`
2. **Start XAMPP Control Panel**:
   * Start the **Apache** server module.
   * Start the **MySQL** database module.
3. **Run the Shop**:
   * Open your browser and navigate to:
     [http://localhost/E-commerce/index.php](http://localhost/E-commerce/index.php)
   * The database (`shopping_db`), tables, default admin, default user, and 24 premium product seeds will automatically initialize themselves on the first page load.
4. **Wipe & Reset Database (Optional)**:
   * To erase all transaction telemetry and rebuild default seeds to factory settings, visit:
     [http://localhost/E-commerce/reset.php](http://localhost/E-commerce/reset.php)

---

## Features Guide

* **Pill Search Engine**: Search keywords dynamically matches product names and descriptions, maintaining state across category tabs.
* **Sticky Navbar**: Features user status markers, shopping cart counters, and navigation tabs.
* **Horizontal Filter Menu**: Instantly filter items by category (All, Mobile, Watch, TV, Laptop).
* **Responsive Shopping Cart**: Increment, decrement, or delete selections with live subtotal and total calculations.
* **Payment Form Validation**: Captures card details (16-digit card verification, MM/YY expiry patterns, CVV coordinates).
* **Monospace Text Invoice**: Generates terminal-style monospace receipts upon transaction completion.
* **Admin Dashboard Metrics**: Visualizes total sales, counts users, active stock, and total orders in interactive glowing widgets.
* **Multipart File Uploads**: Allows admins to add new products by moving image uploads directly to the `uploads/` directory.
