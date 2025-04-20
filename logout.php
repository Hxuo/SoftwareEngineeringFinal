<?php
session_start();
session_destroy();

// Check if there's a return URL
$returnUrl = isset($_GET['returnUrl']) ? $_GET['returnUrl'] : 'index.php';

// Redirect back to the page where the user logged out
header("Location: $returnUrl");
exit();
?>
