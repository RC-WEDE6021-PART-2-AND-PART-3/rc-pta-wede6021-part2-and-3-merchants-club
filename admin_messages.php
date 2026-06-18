<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
include 'DBConn.php';

$message = '';
$error = '';

// Send message
if (isset($_POST['send_message'])) {
    $receiver_id = (int)$_POST['receiver_id'];
    $message_text = mysqli_real_escape_string($conn, $_POST['message_text']);
    
    $insert = "INSERT INTO tblMessage (sender_id, receiver_id, message_text, is_admin) 
               VALUES ({$_SESSION['admin_id']}, $receiver_id, '$message_text', 1)";
    if (mysqli_query($conn, $insert)) {
        $message = "Message sent successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get users for messaging
$users = mysqli_query($conn, "SELECT * FROM tblUser WHERE verified = 1 ORDER BY user_id DESC");

// Get conversations
$messages = mysqli_query($conn, "SELECT m.*, u.username, u.full_name 
                                  FROM tblMessage m 
                                  JOIN tblUser u ON m.receiver_id = u.user_id 
                                  WHERE m.sender_id = {$_SESSION['admin_id']} OR m.sender_id != {$_SESSION['admin_id']}
                                  ORDER BY m.sent_date DESC 
                                  LIMIT 20");
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Admin Messages</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span class="gold">Messages</span></h1>
            <p>Communicate with buyers and sellers</p>
        </div>
        
        <div class="nav">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_clothes.php">Manage Clothes</a>
            <a href="admin_seller_requests.php">Seller Requests</a>
            <a href="admin_messages.php" class="active">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
            <!-- Send Message Form -->
            <div class="card">
                <h2>✉️ Send Message</h2>
                <form method="post">
                    <div class="form-group">
                        <label>To User</label>
                        <select name="receiver_id" required>
                            <option value="">Select a user...</option>
                            <?php while($user = mysqli_fetch_assoc($users)): ?>
                            <option value="<?php echo $user['user_id']; ?>">
                                <?php echo htmlspecialchars($user['full_name']); ?> (@<?php echo htmlspecialchars($user['username']); ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message_text" rows="5" placeholder="Type your message here..." required></textarea>
                    </div>
                    
                    <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            
            <!-- Message History -->
            <div class="card">
                <h2>📜 Recent Conversations</h2>
                <div class="message-container">
                    <?php if(mysqli_num_rows($messages) > 0): ?>
                        <?php while($msg = mysqli_fetch_assoc($messages)): ?>
                        <div class="message <?php echo $msg['is_admin'] ? 'message-admin' : 'message-user'; ?>">
                            <strong>
                                <?php if($msg['is_admin']): ?>
                                    Admin
                                <?php else: ?>
                                    <?php echo htmlspecialchars($msg['full_name']); ?>
                                <?php endif; ?>
                            </strong>
                            <p><?php echo htmlspecialchars($msg['message_text']); ?></p>
                            <div class="message-meta"><?php echo $msg['sent_date']; ?></div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #666;">No messages yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>