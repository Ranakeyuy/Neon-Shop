<?php
require_once 'db.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: index.php');
    }
    exit;
}
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
    $location = mysqli_real_escape_string($conn, trim($_POST['location']));
    if (empty($fullname) || empty($email) || empty($password) || empty($mobile) || empty($location)) {
        $error = 'All fields are required';
    } else {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = 'Email is already registered';
        } else {
            $hashed_pass = md5($password);
            $role = ($email === 'admin@shopping.com') ? 'admin' : 'user';
            $insert_query = "INSERT INTO users (fullname, email, password, mobile, location, role) VALUES ('$fullname', '$email', '$hashed_pass', '$mobile', '$location', '$role')";
            if (mysqli_query($conn, $insert_query)) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Neon Tech Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-card">
        <h2 class="auth-title">Create <span>Account</span></h2>
        <p class="auth-subtitle">Join the premium shopping experience</p>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" class="form-control" required value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Delivery Location</label>
                <textarea name="location" class="form-control" required><?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-submit">Sign Up</button>
        </form>
        <div class="auth-footer">
            Already have an account? <a href="login.php">Log In</a>
        </div>
    </div>
</body>
</html>
