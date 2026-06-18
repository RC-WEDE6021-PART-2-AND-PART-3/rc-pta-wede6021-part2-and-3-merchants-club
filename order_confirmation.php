<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'DBConn.php';

$order_id = (int)$_GET['order_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get order details
$query = "SELECT * FROM tblOrder WHERE order_id = $order_id AND user_id = $user_id";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header('Location: user_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Order Confirmation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Order Confirmed!</span></h1>
            <p>Thank you for your purchase</p>
        </div>
        
        <div class="nav">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="shop.php">🛍️ Continue Shopping</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <div class="alert alert-success" style="text-align: center; padding: 30px; font-size: 1.1em;">
            <h2 style="color: #007A4B;">✅ Your order has been placed successfully!</h2>
            <p style="margin: 10px 0;">Order #<?php echo $order_id; ?></p>
            <p>Total: R<?php echo number_format($order['total_amount'], 2); ?></p>
            <p style="color: #666; font-size: 0.9em;">Status: <?php echo ucfirst($order['status']); ?></p>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <p style="color: #666;">You will receive a confirmation email shortly.</p>
            <p style="color: #666; margin-top: 5px;">The seller will be notified to prepare your items for shipping.</p>
            <a href="shop.php" class="btn btn-primary" style="margin-top: 20px;">🛍️ Continue Shopping</a>
        </div>
    </div>
</body>
</html>