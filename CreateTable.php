<?php
// createTable.php - Drops, recreates and loads tblUser from text file
include 'DBConn.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>ZA Thrift - Database Setup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZA Thrift <span>Database Setup</span></h1>
            <p>User table recreation and data loading</p>
        </div>';

// Drop table if exists
mysqli_query($conn, "DROP TABLE IF EXISTS tblUser");
echo '<div class="alert alert-info">✓ Dropped existing tblUser (if any)</div>';

// Recreate table
$sql = "CREATE TABLE tblUser (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    verified TINYINT DEFAULT 0,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo '<div class="alert alert-success">✓ Created tblUser table successfully</div>';
} else {
    echo '<div class="alert alert-error">✗ Error creating table: ' . mysqli_error($conn) . '</div>';
}

// Load data from userData.txt
$file = fopen('userData.txt', 'r');
if ($file) {
    $count = 0;
    while (($line = fgets($file)) !== false) {
        $data = explode('|', trim($line));
        if (count($data) == 5) {
            $username = mysqli_real_escape_string($conn, $data[0]);
            $email = mysqli_real_escape_string($conn, $data[1]);
            $hash = mysqli_real_escape_string($conn, $data[2]);
            $full_name = mysqli_real_escape_string($conn, $data[3]);
            $verified = (int)$data[4];
            
            $insert = "INSERT INTO tblUser (username, email, password_hash, full_name, verified) 
                       VALUES ('$username', '$email', '$hash', '$full_name', $verified)";
            if (mysqli_query($conn, $insert)) {
                $count++;
            }
        }
    }
    fclose($file);
    echo '<div class="alert alert-success">✓ Loaded ' . $count . ' users from userData.txt</div>';
} else {
    echo '<div class="alert alert-error">✗ Error opening userData.txt - file not found</div>';
}

// Display current users
$result = mysqli_query($conn, "SELECT * FROM tblUser");
echo '<h2>Current Users in Database</h2>';
echo '<table class="table">';
echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Verified</th></tr>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' . $row['user_id'] . '</td>';
    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
    echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
    echo '<td>' . ($row['verified'] ? '✓ Verified' : '⏳ Pending') . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<div class="nav">
        <a href="login.php">Go to Login Page</a>
        <a href="admin_login.php">Admin Login</a>
      </div>';
      
echo '</div></body></html>';
?>