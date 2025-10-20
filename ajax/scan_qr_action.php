<?php
// ajax/scan_qr_action.php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controller/config.php';

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$qr_data = isset($input['qr_data']) ? trim($input['qr_data']) : 
           (isset($_POST['qr_data']) ? trim($_POST['qr_data']) : '');

if (empty($qr_data)) {
    echo json_encode([
        'success' => false,
        'message' => 'No QR data provided'
    ]);
    exit();
}

try {
    // Query to get vehicle info with user type
    $stmt = $conn->prepare("
        SELECT 
            v.vehicle_id,
            v.plate_number,
            v.status,
            vt.vehicle_type,
            CONCAT(u.firstname, ' ', u.lastname) AS driver_name,
            ut.user_type,
            u.user_type_id
        FROM vehicles_table v
        JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
        JOIN users_table u ON v.user_id = u.user_id
        JOIN user_type_table ut ON u.user_type_id = ut.user_type_id
        WHERE v.plate_number = ? OR v.qr_token = ?
    ");
    
    $stmt->bind_param("ss", $qr_data, $qr_data);
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
    
    // Format status for display
    $status_display = ucfirst($vehicle['status']);
    
    // Return vehicle information with user_type included
    echo json_encode([
        'success' => true,
        'vehicle_id' => (int)$vehicle['vehicle_id'],
        'plate_number' => $vehicle['plate_number'],
        'vehicle_type' => $vehicle['vehicle_type'],
        'driver_name' => $vehicle['driver_name'],
        'user_type' => $vehicle['user_type'],
        'user_type_id' => (int)$vehicle['user_type_id'],
        'status' => $status_display
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>