<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
include 'DBConn.php';

// Handle Verify User
if (isset($_GET['verify'])) {
    $uid = (int)$_GET['verify'];
    mysqli_query($conn, "UPDATE tblUser SET verified=1 WHERE user_id=$uid");
    header('Location: admin_dashboard.php');
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tblUser WHERE user_id=$uid");
    header('Location: admin_dashboard.php');
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $password = md5($_POST['password']);
    $verified = (int)$_POST['verified'];
    
    $insert = "INSERT INTO tblUser (username, email, password_hash, full_name, verified) 
               VALUES ('$username', '$email', '$password', '$full_name', $verified)";
    mysqli_query($conn, $insert);
    header('Location: admin_dashboard.php');
}

// Handle Update User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $uid = (int)$_POST['user_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $verified = (int)$_POST['verified'];
    
    $update = "UPDATE tblUser SET full_name='$full_name', email='$email', verified=$verified WHERE user_id=$uid";
    mysqli_query($conn, $update);
    header('Location: admin_dashboard.php');
}

// Get stats
$total_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblUser"));
$pending_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblUser WHERE verified=0"));
$total_products = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblClothes"));
$pending_requests = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblSellerRequest WHERE status='pending'"));
$total_messages = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tblMessage WHERE is_read=0"));

// Get all users
$users = mysqli_query($conn, "SELECT * FROM tblUser ORDER BY user_id DESC");
$pending = mysqli_query($conn, "SELECT * FROM tblUser WHERE verified=0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Admin</span> <span>Dashboard</span></h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
        </div>
        
        <div class="nav">
            <a href="admin_dashboard.php" class="active">Dashboard</a>
            <a href="shop.php">🛍️ View Shop</a>
            <a href="admin_clothes.php">👕 Manage Clothes</a>
            <a href="admin_seller_requests.php">📦 Seller Requests <?php if($pending_requests > 0) echo "(<span style='color:#FFB612;font-weight:bold;'>$pending_requests</span>)"; ?></a>
            <a href="admin_messages.php">💬 Messages <?php if($total_messages > 0) echo "(<span style='color:#DE3831;font-weight:bold;'>$total_messages</span>)"; ?></a>
            <a href="logout.php">Logout</a>
        </div>
        
        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="card" style="border-left-color: #007A4B; text-align: center; margin-bottom: 0;">
                <h2 style="color: #007A4B;"><?php echo $total_users; ?></h2>
                <p style="color: #666;">Total Users</p>
            </div>
            <div class="card" style="border-left-color: #FFB612; text-align: center; margin-bottom: 0;">
                <h2 style="color: #FFB612;"><?php echo $pending_users; ?></h2>
                <p style="color: #666;">Pending Users</p>
            </div>
            <div class="card" style="border-left-color: #007A4B; text-align: center; margin-bottom: 0;">
                <h2 style="color: #007A4B;"><?php echo $total_products; ?></h2>
                <p style="color: #666;">Products</p>
            </div>
            <div class="card" style="border-left-color: #DE3831; text-align: center; margin-bottom: 0;">
                <h2 style="color: #DE3831;"><?php echo $pending_requests; ?></h2>
                <p style="color: #666;">Pending Seller Requests</p>
            </div>
        </div>
        
        <!-- Pending Verifications -->
        <div class="card">
            <h2>⏳ Pending User Verifications</h2>
            <?php if(mysqli_num_rows($pending) > 0): ?>
                <table class="table">
                    <tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Action</th></tr>
                    <?php while($row = mysqli_fetch_assoc($pending)): ?>
                    <tr>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><a href="?verify=<?php echo $row['user_id']; ?>" class="btn btn-success">Verify</a></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>✅ No pending verifications.</p>
            <?php endif; ?>
        </div>
        
        <!-- Add New User Form -->
        <div class="card">
            <h2>➕ Add New User</h2>
            <form method="post">
                <div style="display: grid; grid-template-columns: repeat(2,1fr); gap: 15px;">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="verified">
                        <option value="0">Pending</option>
                        <option value="1">Verified</option>
                    </select>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
        
        <!-- All Users List -->
        <div class="card">
            <h2>📋 All Users</h2>
            <table class="table">
                <tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Status</th><th>Actions</th></tr>
                <?php while($row = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" style="width: 100%;"></td>
                        <td><input type="text" name="full_name" value="<?php echo htmlspecialchars($row['full_name']); ?>" style="width: 100%;"></td>
                        <td>
                            <select name="verified">
                                <option value="0" <?php echo $row['verified']==0?'selected':''; ?>>Pending</option>
                                <option value="1" <?php echo $row['verified']==1?'selected':''; ?>>Verified</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="update_user" class="btn btn-sm btn-edit">Update</button>
                            <a href="?delete=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete this user?')">Delete</a>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>