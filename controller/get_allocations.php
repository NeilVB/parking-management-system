<?php
// controller/get_allocations.php
require_once "../models/Database.php";
header("Content-Type: application/json");

$response = ["success" => false, "message" => "Unknown error occurred."];

try {
    // Ensure request method is GET
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        throw new Exception("Invalid request method.");
    }

    // Validate parking space ID
    $parking_space_id = intval($_GET["parking_space_id"] ?? 0);
    if ($parking_space_id <= 0) {
        throw new Exception("Invalid parking space ID.");
    }

    // Connect to database
    $db = new Database();
    $conn = $db->getConnection();

    // Prepare and execute query
    $query = "
        SELECT 
            allocation_id,
            parking_space_id,
            vehicle_type_id,
            user_type_id,
            allocated_slots
        FROM parking_slot_allocations
        WHERE parking_space_id = ?
        ORDER BY vehicle_type_id, user_type_id
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $parking_space_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all allocations
    $allocations = [];
    while ($row = $result->fetch_assoc()) {
        $allocations[] = [
            'allocation_id' => (int) $row['allocation_id'],
            'parking_space_id' => (int) $row['parking_space_id'],
            'vehicle_type_id' => (int) $row['vehicle_type_id'],
            'user_type_id' => (int) $row['user_type_id'],
            'allocated_slots' => (int) $row['allocated_slots']
        ];
    }

    $stmt->close();
    $db->closeConnection();

    $response = [
        "success" => true,
        "allocations" => $allocations
    ];

} catch (Exception $e) {
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
?>
