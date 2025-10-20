<?php
// ajax/scan_vehicle.php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controller/config.php';

// Get the scanned data from multiple possible sources
$scanned_data = '';

// Try JSON input first
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['qr_data'])) {
    $scanned_data = trim($input['qr_data']);
} elseif (isset($input['qr_token'])) {
    $scanned_data = trim($input['qr_token']);
} elseif (isset($input['token'])) {
    $scanned_data = trim($input['token']);
}

// Try POST data
if (empty($scanned_data)) {
    $scanned_data = isset($_POST['qr_data']) ? trim($_POST['qr_data']) : 
                    (isset($_POST['qr_token']) ? trim($_POST['qr_token']) : 
                    (isset($_POST['token']) ? trim($_POST['token']) : ''));
}

// Try GET data
if (empty($scanned_data)) {
    $scanned_data = isset($_GET['qr_data']) ? trim($_GET['qr_data']) : 
                    (isset($_GET['qr_token']) ? trim($_GET['qr_token']) : 
                    (isset($_GET['token']) ? trim($_GET['token']) : ''));
}

// Log for debugging
error_log("Received QR data: " . $scanned_data);
error_log("POST data: " . print_r($_POST, true));
error_log("JSON input: " . file_get_contents('php://input'));

if (empty($scanned_data)) {
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
    
    $stmt->bind_param("ss", $scanned_data, $scanned_data);
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
    
    // Return vehicle information in the format expected by qr_scanner.js
    echo json_encode([
        'success' => true,
        'vehicle' => [
            'vehicle_id' => (int)$vehicle['vehicle_id'],
            'plate_number' => $vehicle['plate_number'],
            'vehicle_type' => $vehicle['vehicle_type'],
            'driver_name' => $vehicle['driver_name'],
            'user_type' => $vehicle['user_type'],
            'user_type_id' => (int)$vehicle['user_type_id'],
            'status' => $status_display
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>