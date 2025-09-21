<?php
$conn = mysqli_connect("localhost:3307", "root", "", "parking_spaces");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
