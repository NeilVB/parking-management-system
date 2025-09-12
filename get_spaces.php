<?php
include 'controller/connection.php';

header('Content-Type: application/json');

$result = $conn->query("SELECT id, name, slug, max_space, used_space FROM parking_spaces");

$spaces = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $spaces[] = $row;
    }
}

echo json_encode($spaces);

$conn->close();
?>
