<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
include 'DBConn.php';

// Handle Approve Request
if (isset($_GET['approve'])) {
    $request_id = (int)$_GET['approve'];
    $update = "UPDATE tblSellerRequest SET status = 'approved' WHERE request_id = $request_id";
    mysqli_query($conn, $update);
    header('Location: admin_seller_requests.php');
}

// Handle Reject Request
if (isset($_GET['reject'])) {
    $request_id = (int)$_GET['reject'];
    $update = "UPDATE tblSellerRequest SET status = 'rejected' WHERE request_id = $request_id";
    mysqli_query($conn, $update);
    header('Location: admin_seller_requests.php');
}

// Get all seller requests
$requests = mysqli_query($conn, "SELECT r.*, u.username, u.email, u.full_name 
                                  FROM tblSellerRequest r 
                                  JOIN tblUser u ON r.user_id = u.user_id 
                                  ORDER BY r.submitted_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Seller Requests</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Seller Requests</span></h1>
            <p>Approve or reject seller applications</p>
        </div>
        
        <div class="nav">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_clothes.php">Manage Clothes</a>
            <a href="admin_seller_requests.php" class="active">Seller Requests</a>
            <a href="admin_messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if(mysqli_num_rows($requests) > 0): ?>
            <table class="table">
                <tr>
                    <th>Request ID</th>
                    <th>User</th>
                    <th>Item Name</th>
                    <th>Brand</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($requests)): ?>
                <tr>
                    <td><?php echo $row['request_id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['full_name']); ?></strong><br>
                        <small style="color: #666;">@<?php echo htmlspecialchars($row['username']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                    <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)); ?>...</td>
                    <td>
                        <?php if($row['image_filename']): ?>
                            <img src="uploads/<?php echo $row['image_filename']; ?>" width="60" height="60" style="object-fit: cover; border-radius: 5px;" onerror="this.style.display='none'">
                        <?php else: ?>
                            No image
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($row['status'] == 'pending'): ?>
                            <span class="badge badge-pending">⏳ Pending</span>
                        <?php elseif($row['status'] == 'approved'): ?>
                            <span class="badge badge-approved">✅ Approved</span>
                        <?php else: ?>
                            <span class="badge badge-rejected">❌ Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($row['status'] == 'pending'): ?>
                            <a href="?approve=<?php echo $row['request_id']; ?>" class="btn btn-sm btn-success">Approve</a>
                            <a href="?reject=<?php echo $row['request_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Reject this request?')">Reject</a>
                        <?php else: ?>
                            <span style="color: #666;">No action</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No seller requests pending.</div>
        <?php endif; ?>
    </div>
</body>
</html>