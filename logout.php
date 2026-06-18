<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with logout message
header('Location: login.php?logout=success');
exit();
?>