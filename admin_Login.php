<?php
session_start();
include 'DBConn.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM tblAdmin WHERE email='$email' AND password_hash='$password'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_email'] = $row['email'];
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = "Invalid admin credentials. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Admin</span> <span>Portal</span></h1>
            <p>🔐 Administrator Login</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="email" placeholder="admin@zathrift.co.za" required>
                <small style="color: #666; font-size: 0.85em;">Default: admin@zathrift.co.za</small>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                <small style="color: #666; font-size: 0.85em;">Default password: admin123</small>
            </div>
            
            <button type="submit" class="btn btn-success">🔐 Admin Login</button>
            <a href="login.php" style="margin-left: 15px; color: #007A4B;">← User Login</a>
        </form>
        
        <div style="margin-top: 30px; text-align: center; border-top: 2px solid #e0e0e0; padding-top: 20px;">
            <p style="color: #666; font-size: 0.9em;">⚠️ Only authorized administrators can access this portal.</p>
        </div>
    </div>
</body>
</html>