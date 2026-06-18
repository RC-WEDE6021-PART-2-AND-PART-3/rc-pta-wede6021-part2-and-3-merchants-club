<?php
session_start();
include 'DBConn.php';

$error = '';
$message = '';
$entered_username = '';
$entered_email = '';

// Check if user just logged out
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $message = "You have been successfully logged out.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_username = $_POST['username'] ?? '';
    $entered_email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = md5($password);

    $query = "SELECT * FROM tblUser WHERE (username = '$entered_username' OR email = '$entered_email')";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['password_hash'] == $hashed_password) {
            if ($row['verified'] == 1) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                header('Location: user_dashboard.php');
                exit();
            } else {
                $error = "⏳ Your account is pending admin verification. Please wait for approval.";
            }
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "User not found. Please register first.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - User Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Login</span></h1>
            <p>Welcome back! Please login to your account</p>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($entered_username); ?>" placeholder="Enter your username" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($entered_email); ?>" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="register.php" style="margin-left: 15px; color: #007A4B;">New user? Register here</a>
        </form>
        
        <div style="margin-top: 30px; text-align: center; border-top: 2px solid #e0e0e0; padding-top: 20px;">
            <p style="color: #666;">Are you an administrator?</p>
            <a href="admin_login.php" class="btn btn-success">🔐 Admin Login</a>
        </div>
    </div>
</body>
</html>