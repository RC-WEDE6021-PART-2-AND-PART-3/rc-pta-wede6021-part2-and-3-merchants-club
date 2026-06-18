<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
include 'DBConn.php';

$message = '';
$error = '';

// Handle Add Product
if (isset($_POST['add_product'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $sell_price = (float)$_POST['sell_price'];
    $image_filename = mysqli_real_escape_string($conn, $_POST['image_filename']);
    
    $insert = "INSERT INTO tblClothes (item_name, description, sell_price, image_filename) 
               VALUES ('$item_name', '$description', $sell_price, '$image_filename')";
    if (mysqli_query($conn, $insert)) {
        $message = "Product added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $item_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tblClothes WHERE item_id = $item_id");
    $message = "Product deleted!";
}

// Get all products
$products = mysqli_query($conn, "SELECT * FROM tblClothes ORDER BY item_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Manage Clothes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Manage Clothes</span></h1>
            <p>Add, edit or delete products</p>
        </div>
        
        <div class="nav">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_clothes.php" class="active">Manage Clothes</a>
            <a href="admin_seller_requests.php">Seller Requests</a>
            <a href="admin_messages.php">Messages</a>
            <a href="shop.php">🛍️ View Shop</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Add Product Form -->
        <div class="card">
            <h2>➕ Add New Product</h2>
            <form method="post">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <input type="text" name="item_name" placeholder="Item Name" required>
                    <input type="number" name="sell_price" placeholder="Price (R)" step="0.01" required>
                    <input type="text" name="image_filename" placeholder="Image Filename (e.g., jacket.jpg)" required>
                    <div style="grid-column: 1 / -1;">
                        <textarea name="description" placeholder="Description" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-success" style="grid-column: 1 / -1;">Add Product</button>
                </div>
            </form>
        </div>
        
        <!-- Product List -->
        <div class="card">
            <h2>📋 All Products</h2>
            <div class="clothing-grid">
                <?php while($row = mysqli_fetch_assoc($products)): ?>
                <div class="admin-product-card">
                    <img src="images/<?php echo $row['image_filename']; ?>" alt="<?php echo $row['item_name']; ?>" onerror="this.src='https://via.placeholder.com/240x170?text=No+Image'">
                    <div class="info">
                        <h4><?php echo htmlspecialchars($row['item_name']); ?></h4>
                        <p style="color: #666; font-size: 0.9em;"><?php echo htmlspecialchars(substr($row['description'], 0, 60)); ?>...</p>
                        <div class="price">R<?php echo number_format($row['sell_price'], 2); ?></div>
                    </div>
                    <div class="actions">
                        <a href="#" class="btn btn-sm btn-edit" onclick="alert('Edit feature coming soon')">Edit</a>
                        <a href="?delete=<?php echo $row['item_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <?php if(mysqli_num_rows($products) == 0): ?>
                <p style="color: #666;">No products added yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>