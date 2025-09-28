<!-- Currently non functional, need to set up database for parking spaces first -->
<?php
include 'controller/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $max_space = intval($_POST['max_space']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));

    if (empty($name) || $max_space <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    //  Case-insensitive duplicate check (slug OR name)
    $check = $conn->prepare("
        SELECT id 
        FROM parking_spaces 
        WHERE slug = ? OR LOWER(name) = LOWER(?)
    ");
    $check->bind_param("ss", $slug, $name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Parking space already exists!"
        ]);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Insert if not duplicate
    $stmt = $conn->prepare("
        INSERT INTO parking_spaces (name, slug, max_space, used_space) 
        VALUES (?, ?, ?, 0)
    ");
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed: " . $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("ssi", $name, $slug, $max_space);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Parking space added successfully!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Execute failed: " . $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
}
?>