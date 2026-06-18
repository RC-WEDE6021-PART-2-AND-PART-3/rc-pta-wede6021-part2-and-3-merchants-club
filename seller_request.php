<?php
session_start();
if (!isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
include 'DBConn.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Check if user already has a pending request
$pending_check = mysqli_query($conn, "SELECT * FROM tblSellerRequest WHERE user_id = $user_id AND status = 'pending'");
$approved_check = mysqli_query($conn, "SELECT * FROM tblSellerRequest WHERE user_id = $user_id AND status = 'approved'");

$has_pending = mysqli_num_rows($pending_check) > 0;
$has_approved = mysqli_num_rows($approved_check) > 0;

if (isset($_POST['submit_request'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    
    // Handle image upload
    $image_filename = '';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_filename = time() . '_' . basename($_FILES['item_image']['name']);
        $target_file = $target_dir . $image_filename;
        
        // Check if image file is valid
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
                // Success
            } else {
                $error = "Error uploading image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        $error = "Please select an image.";
    }
    
    if (empty($error)) {
        $insert = "INSERT INTO tblSellerRequest (user_id, item_name, description, brand, image_filename, status) 
                   VALUES ($user_id, '$item_name', '$description', '$brand', '$image_filename', 'pending')";
        if (mysqli_query($conn, $insert)) {
            $message = "✅ Your seller request has been submitted! Please wait for admin approval.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Seller Request</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Sell Clothes</span></h1>
            <p>Request to sell your pre-loved items</p>
        </div>
        
        <div class="nav">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="shop.php">🛍️ Shop</a>
            <a href="cart.php">🛒 Cart</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($has_approved): ?>
            <div class="alert alert-success">
                ✅ You are already an approved seller! You can list items directly.
                <br><a href="admin_clothes.php">Add your items here</a>
            </div>
        <?php endif; ?>
        
        <?php if($has_pending && !$has_approved): ?>
            <div class="alert alert-info">
                ⏳ You have a pending seller request. Please wait for admin approval.
            </div>
        <?php endif; ?>
        
        <?php if(!$has_pending && !$has_approved): ?>
        <div class="card">
            <p style="color: #666; margin-bottom: 20px;">Fill in the details below to request seller status. An admin will review your request and approve it.</p>
            
            <form method="post" enctype="multipart/form-data" class="request-form">
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" name="item_name" placeholder="e.g., Vintage Denim Jacket" required>
                </div>
                
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="brand" placeholder="e.g., Levi's, Nike, Zara" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5" placeholder="Describe your item in detail..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Upload Image</label>
                    <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                        <span class="upload-icon">📸</span>
                        <p>Click to upload an image of your item</p>
                        <input type="file" name="item_image" id="fileInput" accept="image/*" required>
                    </div>
                    <small style="color: #666;">Accepted formats: JPG, JPEG, PNG, GIF</small>
                </div>
                
                <button type="submit" name="submit_request" class="btn btn-success">Submit Request</button>
                <a href="user_dashboard.php" style="margin-left: 15px;">Cancel</a>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>