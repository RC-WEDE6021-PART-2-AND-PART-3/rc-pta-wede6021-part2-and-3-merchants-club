<?php
session_start();
if (!isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
include 'DBConn.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM tblUser WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get cart count
$cart_count = 0;
$cart_result = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM tblCart WHERE user_id = $user_id");
if ($cart_row = mysqli_fetch_assoc($cart_result)) {
    $cart_count = $cart_row['total'] ?? 0;
}

// Check if user has pending seller request
$pending_request = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblSellerRequest WHERE user_id = $user_id AND status='pending'"));
$approved_request = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblSellerRequest WHERE user_id = $user_id AND status='approved'"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
            <p>User <?php echo htmlspecialchars($_SESSION['full_name']); ?> is logged in</p>
        </div>
        
        <div class="nav">
            <a href="user_dashboard.php" class="active">Dashboard</a>
            <a href="shop.php">🛍️ Shop</a>
            <a href="cart.php">🛒 Cart <?php if($cart_count > 0) echo "(<span style='color:#007A4B;font-weight:bold;'>$cart_count</span>)"; ?></a>
            <a href="seller_request.php">📦 Sell Clothes</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <div class="card" style="border-left-color: #007A4B;">
            <h2>👤 Your Profile Information</h2>
            <table class="table">
                <tr><th>User ID</th><td><?php echo $user['user_id']; ?></td></tr>
                <tr><th>Username</th><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
                <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
                <tr><th>Full Name</th><td><?php echo htmlspecialchars($user['full_name']); ?></td></tr>
                <tr><th>Account Status</th>
                    <td><?php echo $user['verified'] ? '<span class="badge badge-verified">✓ Verified</span>' : '<span class="badge badge-unverified">⏳ Pending Verification</span>'; ?>
                    </td>
                </tr>
                <tr><th>Member Since</th><td><?php echo $user['created_at']; ?></td></tr>
            </table>
        </div>
        
        <!-- Seller Request Status -->
        <?php if($pending_request > 0): ?>
            <div class="alert alert-info">
                📦 You have a <strong>pending seller request</strong>. Please wait for admin approval.
            </div>
        <?php elseif($approved_request > 0): ?>
            <div class="alert alert-success">
                ✅ Your seller request has been <strong>approved</strong>! You can now sell clothes on ZA Thrift.
            </div>
        <?php endif; ?>
        
        <!-- Quick Actions -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px;">
            <div class="card" style="border-left-color: #FFB612; text-align: center; margin-bottom: 0;">
                <h3>🛍️ Browse</h3>
                <p style="font-size: 0.9em; color: #666;">Find pre-loved items</p>
                <a href="shop.php" class="btn btn-primary" style="margin-top: 10px;">Shop Now</a>
            </div>
            
            <div class="card" style="border-left-color: #007A4B; text-align: center; margin-bottom: 0;">
                <h3>🛒 Cart</h3>
                <p style="font-size: 0.9em; color: #666;"><?php echo $cart_count; ?> item(s) in your cart</p>
                <a href="cart.php" class="btn btn-success" style="margin-top: 10px;">View Cart</a>
            </div>
            
            <div class="card" style="border-left-color: #DE3831; text-align: center; margin-bottom: 0;">
                <h3>📦 Sell</h3>
                <p style="font-size: 0.9em; color: #666;">Request to sell clothes</p>
                <a href="seller_request.php" class="btn btn-warning" style="margin-top: 10px;">Sell Now</a>
            </div>
        </div>
    </div>
</body>
</html>