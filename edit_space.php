<?php
include 'controller/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $max_space = intval($_POST['max_space']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));

    if ($id <= 0 || empty($name) || $max_space <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    // Check if another row already has this slug
    $check = $conn->prepare("SELECT id FROM parking_spaces WHERE slug = ? AND id != ?");
    $check->bind_param("si", $slug, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Another parking space with this name already exists!"]);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Safe to update
    $stmt = $conn->prepare("UPDATE parking_spaces SET name=?, slug=?, max_space=? WHERE id=?");
    $stmt->bind_param("ssii", $name, $slug, $max_space, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Parking space updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
