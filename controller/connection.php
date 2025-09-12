<?php
$conn = mysqli_connect("localhost", "root", "", "parking_spaces");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
