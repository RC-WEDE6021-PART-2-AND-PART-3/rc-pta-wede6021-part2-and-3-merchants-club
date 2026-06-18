<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'DBConn.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Get cart items
$query = "SELECT c.cart_id, c.quantity, cl.item_id, cl.item_name, cl.sell_price 
          FROM tblCart c 
          JOIN tblClothes cl ON c.item_id = cl.item_id 
          WHERE c.user_id = $user_id";
$result = mysqli_query($conn, $query);

// Calculate total
$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['quantity'] * $row['sell_price'];
    $total += $row['subtotal'];
    $items[] = $row;
}

if (count($items) == 0) {
    header('Location: cart.php');
    exit();
}

// Process checkout
if (isset($_POST['checkout'])) {
    // Insert order
    $insert = "INSERT INTO tblOrder (user_id, total_amount, status) VALUES ($user_id, $total, 'confirmed')";
    if (mysqli_query($conn, $insert)) {
        $order_id = mysqli_insert_id($conn);
        
        // Clear cart
        mysqli_query($conn, "DELETE FROM tblCart WHERE user_id = $user_id");
        
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    } else {
        $error = "Error processing order: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Checkout</span></h1>
            <p>Review and confirm your order</p>
        </div>
        
        <div class="nav">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="cart.php">← Back to Cart</a>
            <a href="shop.php">🛍️ Continue Shopping</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="checkout-summary">
            <h2>Order Summary</h2>
            <?php foreach($items as $item): ?>
            <div class="checkout-item">
                <span><?php echo htmlspecialchars($item['item_name']); ?> x <?php echo $item['quantity']; ?></span>
                <span>R<?php echo number_format($item['subtotal'], 2); ?></span>
            </div>
            <?php endforeach; ?>
            <div class="checkout-total">
                <span>Total</span>
                <span>R<?php echo number_format($total, 2); ?></span>
            </div>
        </div>
        
        <form method="post" style="text-align: center;">
            <p style="color: #666; margin-bottom: 15px;">By placing your order, you agree to our terms and conditions.</p>
            <button type="submit" name="checkout" class="btn btn-success" style="font-size: 1.2em; padding: 15px 50px;">✅ Place Order</button>
            <a href="cart.php" class="btn btn-primary" style="margin-left: 15px;">← Back to Cart</a>
        </form>
    </div>
</body>
</html>