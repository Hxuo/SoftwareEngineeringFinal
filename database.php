<?php
$servername = "localhost";
$username = "root"; // Palitan kung may ibang user ka
$password = ""; // Palitan kung may password ka
$dbname = "softengfinal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
