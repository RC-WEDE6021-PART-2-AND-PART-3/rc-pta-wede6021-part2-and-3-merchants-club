<?php
include 'DBConn.php';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $password = md5($_POST['password']);
    
    $check = "SELECT * FROM tblUser WHERE username='$username' OR email='$email'";
    $checkRes = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($checkRes) > 0) {
        $error = "Username or email already exists.";
    } else {
        $insert = "INSERT INTO tblUser (username, email, password_hash, full_name, verified) 
                   VALUES ('$username', '$email', '$password', '$full_name', 0)";
        if (mysqli_query($conn, $insert)) {
            $message = "✅ Registration successful! Please wait for admin verification before logging in.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Register</span></h1>
            <p>Create your account to start thrifting</p>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Choose a username" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email address" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
                <small style="color: #666; font-size: 0.85em;">Password will be securely hashed (MD5)</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="login.php" style="margin-left: 15px; color: #007A4B;">Already have an account? Login</a>
        </form>
        
        <div style="margin-top: 30px; text-align: center; border-top: 2px solid #e0e0e0; padding-top: 20px;">
            <p style="color: #666; font-size: 0.9em;">🔒 Your account will need admin approval before you can log in.</p>
        </div>
    </div>
</body>
</html>