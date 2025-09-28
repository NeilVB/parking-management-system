<!-- Currently non functional, need to set up database for parking spaces first -->
<?php
include 'controller/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid ID"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM parking_spaces WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Parking space deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Delete failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
