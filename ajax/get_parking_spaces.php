<?php
// Disable error display, log only
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Set JSON header first
header('Content-Type: application/json');

// Check database file exists
if (!file_exists('../models/Database.php')) {
    echo json_encode(['success' => false, 'message' => 'Database config not found']);
    exit;
}

require_once '../models/Database.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized - Please login']);
//     exit;
// }

try {
    // Check if Database class exists
    if (!class_exists('Database')) {
        echo json_encode(['success' => false, 'message' => 'Database class not found']);
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        echo json_encode(['success' => false, 'message' => 'Failed to connect to database']);
        exit;
    }

    // Get available parking spaces based on capacity
    $query = "SELECT 
                id,
                name,
                supports_car,
                supports_motorcycle,
                is_general,
                is_faculty,
                is_executive,
                is_guest,
                max_car_slots,
                max_motorcycle_slots,
                parked_car_count,
                parked_motorcycle_count,
                (max_car_slots - parked_car_count) as available_car_slots,
                (max_motorcycle_slots - parked_motorcycle_count) as available_motorcycle_slots
              FROM parking_spaces
              WHERE (max_car_slots - parked_car_count > 0) 
                 OR (max_motorcycle_slots - parked_motorcycle_count > 0)
              ORDER BY name";

    $stmt = $db->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
        exit;
    }
    
    $stmt->execute();

    $spaces = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Determine what vehicle types this space supports
        $supported_types = [];
        if ($row['supports_car'] == 1 && $row['available_car_slots'] > 0) {
            $supported_types[] = 'Car';
        }
        if ($row['supports_motorcycle'] == 1 && $row['available_motorcycle_slots'] > 0) {
            $supported_types[] = 'Motorcycle';
        }

        // Only add if there are available slots
        if (count($supported_types) > 0) {
            $spaces[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'vehicle_type' => implode(' / ', $supported_types),
                'is_general' => $row['is_general'] == 1,
                'is_faculty' => $row['is_faculty'] == 1,
                'is_executive' => $row['is_executive'] == 1,
                'is_guest' => $row['is_guest'] == 1,
                'available_car_slots' => (int)$row['available_car_slots'],
                'available_motorcycle_slots' => (int)$row['available_motorcycle_slots']
            ];
        }
    }

    if (count($spaces) > 0) {
        echo json_encode([
            'success' => true,
            'spaces' => $spaces,
            'count' => count($spaces)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No available parking spaces',
            'spaces' => []
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>