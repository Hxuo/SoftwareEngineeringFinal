<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: Login.php");
    exit();
}

echo "Welcome, " . $_SESSION['username'] . "!";
?>
<a href='logout.php'>Logout</a>
