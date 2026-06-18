<?php
session_start();
include 'DBConn.php';

$message = '';

// Add to cart - saves to database
if (isset($_GET['add_to_cart']) && isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    $item_id = (int)$_GET['add_to_cart'];
    $user_id = (int)$_SESSION['user_id'];
    
    // Check if item already in cart
    $check = mysqli_query($conn, "SELECT * FROM tblCart WHERE user_id = $user_id AND item_id = $item_id");
    
    if (mysqli_num_rows($check) > 0) {
        // Update quantity
        mysqli_query($conn, "UPDATE tblCart SET quantity = quantity + 1 WHERE user_id = $user_id AND item_id = $item_id");
        $message = "Item quantity updated in cart!";
    } else {
        // Insert new cart item
        mysqli_query($conn, "INSERT INTO tblCart (user_id, item_id, quantity) VALUES ($user_id, $item_id, 1)");
        $message = "Item added to cart successfully!";
    }
}

// Get cart count for badge
$cart_count = 0;
if (isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    $cart_result = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM tblCart WHERE user_id = " . (int)$_SESSION['user_id']);
    $cart_row = mysqli_fetch_assoc($cart_result);
    $cart_count = $cart_row['total'] ?? 0;
}

// Get all products
$result = mysqli_query($conn, "SELECT * FROM tblClothes");
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span>Shop</span></h1>
            <p>Browse our pre-loved collection</p>
        </div>
        
        <div class="nav">
            <?php if(isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
                <a href="user_dashboard.php">Dashboard</a>
                <a href="shop.php" class="active">Shop</a>
                <a href="cart.php">🛒 Cart <?php if($cart_count > 0) echo "(<span style='color:#007A4B;font-weight:bold;'>$cart_count</span>)"; ?></a>
                <a href="seller_request.php">Sell</a>
                <a href="logout.php">Logout</a>
            <?php elseif(isset($_SESSION['admin_id'])): ?>
                <a href="admin_dashboard.php">Admin Panel</a>
                <a href="shop.php" class="active">Shop</a>
                <a href="admin_clothes.php">Manage Clothes</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="product-grid">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <img src="images/<?php echo $row['image_filename']; ?>" alt="<?php echo $row['item_name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?>...</p>
                    <div class="product-price">R<?php echo number_format($row['sell_price'], 2); ?></div>
                    
                    <?php if(isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
                        <a href="?add_to_cart=<?php echo $row['item_id']; ?>" class="btn btn-primary">🛒 Add to Cart</a>
                    <?php elseif(isset($_SESSION['admin_id'])): ?>
                        <span class="badge badge-approved">Admin View</span>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Login to Buy</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
            <div class="alert alert-info">No products available yet. Please check back later.</div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
            <div style="margin-top: 30px; text-align: center;">
                <a href="cart.php" class="btn btn-success">🛒 View My Cart (<?php echo $cart_count; ?> items)</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>