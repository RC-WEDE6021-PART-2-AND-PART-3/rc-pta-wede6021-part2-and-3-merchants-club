<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'DBConn.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Update quantity
if (isset($_POST['update_cart'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    mysqli_query($conn, "UPDATE tblCart SET quantity = $quantity WHERE cart_id = $cart_id AND user_id = $user_id");
    $message = "Cart updated successfully!";
}

// Remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM tblCart WHERE cart_id = $cart_id AND user_id = $user_id");
    $message = "Item removed from cart!";
}

// Clear cart
if (isset($_GET['clear'])) {
    mysqli_query($conn, "DELETE FROM tblCart WHERE user_id = $user_id");
    $message = "Cart cleared!";
}

// Get cart items
$query = "SELECT c.cart_id, c.quantity, cl.item_id, cl.item_name, cl.sell_price, cl.image_filename 
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

// Reset pointer for display
mysqli_data_seek($result, 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Shopping Cart</span></h1>
            <p>Review your items before checkout</p>
        </div>
        
        <div class="nav">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="shop.php">🛍️ Continue Shopping</a>
            <?php if(isset($_SESSION['admin_id'])): ?>
                <a href="admin_dashboard.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="cart-container">
            <?php if(count($items) > 0): ?>
                <table class="cart-table">
                    <tr>
                        <th>Image</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td><img src="images/<?php echo $item['image_filename']; ?>" alt="<?php echo $item['item_name']; ?>" class="cart-item-img" onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'"></td>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td>R<?php echo number_format($item['sell_price'], 2); ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99" class="quantity-input">
                                <button type="submit" name="update_cart" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                        <td>R<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td><a href="?remove=<?php echo $item['cart_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Remove this item?')">Remove</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                
                <div class="cart-total">
                    <h2>Total: R<?php echo number_format($total, 2); ?></h2>
                </div>
                
                <div class="cart-actions">
                    <a href="shop.php" class="btn btn-primary">🛍️ Continue Shopping</a>
                    <a href="checkout.php" class="btn btn-success">✅ Proceed to Checkout</a>
                    <a href="?clear=1" class="btn btn-danger" onclick="return confirm('Clear entire cart?')">🗑️ Clear Cart</a>
                </div>
            <?php else: ?>
                <div class="alert alert-info" style="text-align: center; padding: 40px;">
                    <h3 style="color: #1a1a2e;">🛒 Your cart is empty</h3>
                    <p style="color: #666; margin: 10px 0;">Browse our shop and add items you love!</p>
                    <a href="shop.php" class="btn btn-primary" style="margin-top: 15px;">🛍️ Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>