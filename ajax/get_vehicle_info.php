<?php
// ajax/get_vehicle_info.php
header('Content-Type: application/json');
session_start();

// Check if admin is authenticated
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once __DIR__ . '/../controller/config.php';

// Get plate number from POST or GET
$plate_number = isset($_POST['plate_number']) ? trim($_POST['plate_number']) : 
                (isset($_GET['plate_number']) ? trim($_GET['plate_number']) : '');

if (empty($plate_number)) {
    echo json_encode(['success' => false, 'message' => 'Plate number is required']);
    exit();
}

try {
    // Get vehicle information with owner details
    $stmt = $conn->prepare("
        SELECT 
            v.vehicle_id,
            v.plate_number,
            v.color,
            v.make,
            v.model,
            v.year,
            v.status,
            v.assigned_space,
            vt.vehicle_type,
            u.firstname,
            u.lastname,
            u.email,
            ut.user_type
        FROM vehicles_table v
        JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
        JOIN users_table u ON v.user_id = u.user_id
        JOIN user_type_table ut ON u.user_type_id = ut.user_type_id
        WHERE v.plate_number = ?
    ");
    
    $stmt->bind_param("s", $plate_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Vehicle not found in the system'
        ]);
        exit();
    }
    
    $vehicle = $result->fetch_assoc();
    
    // Format owner name
    $owner_name = $vehicle['firstname'] . ' ' . $vehicle['lastname'];
    
    // Get parking space name if assigned
    $parking_space_name = null;
    if ($vehicle['assigned_space']) {
        $stmt = $conn->prepare("SELECT name FROM parking_spaces WHERE id = ?");
        $stmt->bind_param("i", $vehicle['assigned_space']);
        $stmt->execute();
        $space_result = $stmt->get_result();
        if ($space_result->num_rows > 0) {
            $space = $space_result->fetch_assoc();
            $parking_space_name = $space['name'];
        }
    }
    
    // Prepare response
    echo json_encode([
        'success' => true,
        'vehicle' => [
            'plate_number' => $vehicle['plate_number'],
            'owner' => $owner_name,
            'email' => $vehicle['email'],
            'vehicle_type' => $vehicle['vehicle_type'],
            'user_type' => $vehicle['user_type'],
            'color' => $vehicle['color'],
            'make' => $vehicle['make'],
            'model' => $vehicle['model'],
            'year' => $vehicle['year'],
            'status' => $vehicle['status'],
            'assigned_space' => $parking_space_name,
            'full_info' => $vehicle['year'] . ' ' . $vehicle['color'] . ' ' . 
                          $vehicle['make'] . ' ' . $vehicle['model']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving vehicle information: ' . $e->getMessage()
    ]);
}

$conn->close();
?>