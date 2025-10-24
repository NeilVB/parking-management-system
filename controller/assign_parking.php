<?php
// controller/assign_parking.php
header('Content-Type: application/json');
session_start();

// Check if admin is authenticated
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once __DIR__ . '/config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['parking_space_id']) || !isset($input['plate_number'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$parking_space_id = (int)$input['parking_space_id'];
$plate_number = trim($input['plate_number']);

try {
    // Start transaction
    $conn->begin_transaction();

    // 1. Get vehicle information
    $stmt = $conn->prepare("
        SELECT v.vehicle_id, v.user_id, v.vehicle_type_id, v.status, u.user_type_id
        FROM vehicles_table v
        JOIN users_table u ON v.user_id = u.user_id
        WHERE v.plate_number = ?
    ");
    $stmt->bind_param("s", $plate_number);
    $stmt->execute();
    $vehicle_result = $stmt->get_result();
    
    if ($vehicle_result->num_rows === 0) {
        throw new Exception('Vehicle not found');
    }
    
    $vehicle = $vehicle_result->fetch_assoc();
    $vehicle_id = $vehicle['vehicle_id'];
    $vehicle_type_id = $vehicle['vehicle_type_id'];
    $user_type_id = $vehicle['user_type_id'];
    
    // Check if vehicle is already parked
    if ($vehicle['status'] === 'parked') {
        throw new Exception('Vehicle is already parked');
    }

    // 2. Get parking space information
    $stmt = $conn->prepare("
        SELECT supports_car, supports_motorcycle, parked_car_count, parked_motorcycle_count
        FROM parking_spaces
        WHERE id = ?
    ");
    $stmt->bind_param("i", $parking_space_id);
    $stmt->execute();
    $space_result = $stmt->get_result();
    
    if ($space_result->num_rows === 0) {
        throw new Exception('Parking space not found');
    }
    
    $space = $space_result->fetch_assoc();

    // 3. Check if parking space supports this vehicle type
    if ($vehicle_type_id == 1 && !$space['supports_car']) {
        throw new Exception('This parking space does not support cars');
    }
    if ($vehicle_type_id == 2 && !$space['supports_motorcycle']) {
        throw new Exception('This parking space does not support motorcycles');
    }

    // 4. Get allocated slots for this specific user type and vehicle type
    $stmt = $conn->prepare("
        SELECT allocated_slots
        FROM parking_slot_allocations
        WHERE parking_space_id = ? AND vehicle_type_id = ? AND user_type_id = ?
    ");
    $stmt->bind_param("iii", $parking_space_id, $vehicle_type_id, $user_type_id);
    $stmt->execute();
    $alloc_result = $stmt->get_result();
    
    if ($alloc_result->num_rows === 0) {
        throw new Exception('No allocation found for your user type in this parking space');
    }
    
    $allocation = $alloc_result->fetch_assoc();
    $user_allocated_slots = $allocation['allocated_slots'];
    
    if ($user_allocated_slots <= 0) {
        throw new Exception('No slots allocated for your user type in this parking space');
    }

    // 5. Get TOTAL allocated slots for this vehicle type (all user types combined)
    $stmt = $conn->prepare("
        SELECT SUM(allocated_slots) as total_slots
        FROM parking_slot_allocations
        WHERE parking_space_id = ? AND vehicle_type_id = ?
    ");
    $stmt->bind_param("ii", $parking_space_id, $vehicle_type_id);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_data = $total_result->fetch_assoc();
    $total_allocated_slots = (int)$total_data['total_slots'];

    // 6. Check if there are available slots (compare total parked vs total allocated)
    $current_parked = $vehicle_type_id == 1 ? $space['parked_car_count'] : $space['parked_motorcycle_count'];
    
    if ($current_parked >= $total_allocated_slots) {
        throw new Exception('Parking space is full for this vehicle type');
    }

    // 7. Check how many vehicles of this user type are already parked
    $stmt = $conn->prepare("
        SELECT COUNT(*) as user_type_parked
        FROM parking_logs pl
        JOIN vehicles_table v ON pl.vehicle_id = v.vehicle_id
        JOIN users_table u ON v.user_id = u.user_id
        WHERE pl.parking_space_id = ? 
            AND v.vehicle_type_id = ?
            AND u.user_type_id = ?
            AND pl.status = 'Parked'
            AND pl.time_out IS NULL
    ");
    $stmt->bind_param("iii", $parking_space_id, $vehicle_type_id, $user_type_id);
    $stmt->execute();
    $user_parked_result = $stmt->get_result();
    $user_parked_data = $user_parked_result->fetch_assoc();
    $user_type_parked_count = (int)$user_parked_data['user_type_parked'];

    // Check if this user type has reached its allocation limit
    if ($user_type_parked_count >= $user_allocated_slots) {
        throw new Exception('All slots for your user type are occupied in this parking space');
    }

    // 8. Check if vehicle has an active parking log with status 'Waiting'
    $stmt = $conn->prepare("
        SELECT log_id FROM parking_logs 
        WHERE vehicle_id = ? AND status = 'Waiting' AND time_out IS NULL
        ORDER BY time_in DESC LIMIT 1
    ");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $log_result = $stmt->get_result();
    
    if ($log_result->num_rows > 0) {
        // Update existing log
        $log = $log_result->fetch_assoc();
        $stmt = $conn->prepare("
            UPDATE parking_logs 
            SET parking_space_id = ?, status = 'Parked' 
            WHERE log_id = ?
        ");
        $stmt->bind_param("ii", $parking_space_id, $log['log_id']);
        $stmt->execute();
    } else {
        // Create new parking log with capitalized status
        $stmt = $conn->prepare("
            INSERT INTO parking_logs (vehicle_id, parking_space_id, status, time_in)
            VALUES (?, ?, 'Parked', NOW())
        ");
        $stmt->bind_param("ii", $vehicle_id, $parking_space_id);
        $stmt->execute();
    }

    // 9. Update vehicle status and assigned space
    $stmt = $conn->prepare("
        UPDATE vehicles_table 
        SET status = 'parked', assigned_space = ?
        WHERE vehicle_id = ?
    ");
    $stmt->bind_param("ii", $parking_space_id, $vehicle_id);
    $stmt->execute();

    // 10. Update parking space occupancy count
    if ($vehicle_type_id == 1) {
        $stmt = $conn->prepare("
            UPDATE parking_spaces 
            SET parked_car_count = parked_car_count + 1
            WHERE id = ?
        ");
    } else {
        $stmt = $conn->prepare("
            UPDATE parking_spaces 
            SET parked_motorcycle_count = parked_motorcycle_count + 1
            WHERE id = ?
        ");
    }
    $stmt->bind_param("i", $parking_space_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Vehicle successfully assigned to parking space'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>