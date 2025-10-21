<?php
// ajax/exit_vehicle.php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controller/config.php';

// Check if admin is authenticated
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get action type
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

/**
 * Simple duration calculation: just subtract timestamps
 */
function calculateDuration($time_in, $time_out = null) {
    // If no time_out provided, use current time
    if ($time_out === null) {
        $time_out = date('Y-m-d H:i:s');
    }
    
    // Simple subtraction of timestamps
    $start = strtotime($time_in);
    $end = strtotime($time_out);
    $diff_seconds = $end - $start;
    
    // Convert seconds to days, hours, minutes
    $days = floor($diff_seconds / 86400);
    $hours = floor(($diff_seconds % 86400) / 3600);
    $minutes = floor(($diff_seconds % 3600) / 60);
    
    $parts = [];
    if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
    if ($hours > 0) $parts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
    if ($minutes > 0) $parts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    
    return !empty($parts) ? implode(' ', $parts) : 'Less than a minute';
}

try {
    // =====================================================
    // ACTION: Get exit information for confirmation modal
    // =====================================================
    if ($action === 'get_exit_info') {
        $vehicle_id = isset($_POST['vehicle_id']) ? intval($_POST['vehicle_id']) : 0;
        
        if ($vehicle_id === 0) {
            echo json_encode(['success' => false, 'message' => 'Vehicle ID is required']);
            exit();
        }

        // Get the latest active parking log for this vehicle
        $stmt = $conn->prepare("
            SELECT 
                pl.log_id,
                pl.vehicle_id,
                pl.parking_space_id,
                pl.status,
                pl.time_in,
                v.plate_number,
                vt.vehicle_type,
                CONCAT(u.firstname, ' ', u.lastname) AS driver_name,
                ps.name AS parking_space_name
            FROM parking_logs pl
            JOIN vehicles_table v ON pl.vehicle_id = v.vehicle_id
            JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
            JOIN users_table u ON v.user_id = u.user_id
            LEFT JOIN parking_spaces ps ON pl.parking_space_id = ps.id
            WHERE pl.vehicle_id = ? 
                AND pl.status IN ('entered', 'parked')
                AND pl.time_out IS NULL
            ORDER BY pl.time_in DESC
            LIMIT 1
        ");
        
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No active parking session found for this vehicle'
            ]);
            exit();
        }
        
        $log = $result->fetch_assoc();
        
        // Simple: subtract time_in from NOW
        $duration_text = calculateDuration($log['time_in']);
        
        echo json_encode([
            'success' => true,
            'exit_info' => [
                'log_id' => $log['log_id'],
                'vehicle_id' => $log['vehicle_id'],
                'parking_space_id' => $log['parking_space_id'],
                'driver_name' => $log['driver_name'],
                'plate_number' => $log['plate_number'],
                'vehicle_type' => $log['vehicle_type'],
                'parking_space' => $log['parking_space_name'] ?? 'Not assigned',
                'entry_time' => date('M d, Y h:i A', strtotime($log['time_in'])),
                'duration' => $duration_text,
                'status' => $log['status']
            ]
        ]);
        exit();
    }
    
    // =====================================================
    // ACTION: Confirm exit and update database
    // =====================================================
    if ($action === 'confirm_exit') {
        $log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;
        $vehicle_id = isset($_POST['vehicle_id']) ? intval($_POST['vehicle_id']) : 0;
        $parking_space_id = isset($_POST['parking_space_id']) ? intval($_POST['parking_space_id']) : null;
        
        if ($log_id === 0 || $vehicle_id === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid exit data']);
            exit();
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            $exit_time = date('Y-m-d H:i:s');
            
            // Step 1: Update parking log - set time_out and status to 'outside'
            $stmt = $conn->prepare("
                UPDATE parking_logs 
                SET time_out = ?, 
                    status = 'outside',
                    updated_at = CURRENT_TIMESTAMP
                WHERE log_id = ?
            ");
            $stmt->bind_param("si", $exit_time, $log_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update parking log: ' . $stmt->error);
            }

            // Step 2: Update vehicle status to 'outside' and clear assigned_space
            $stmt = $conn->prepare("
                UPDATE vehicles_table 
                SET status = 'outside',
                    assigned_space = NULL,
                    updated_at = CURRENT_TIMESTAMP
                WHERE vehicle_id = ?
            ");
            $stmt->bind_param("i", $vehicle_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update vehicle status: ' . $stmt->error);
            }

            // Step 3: Decrement parking space count if space was assigned
            if ($parking_space_id) {
                // Get vehicle type to know which counter to decrement
                $stmt = $conn->prepare("
                    SELECT vt.vehicle_type_id, vt.vehicle_type
                    FROM vehicles_table v
                    JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
                    WHERE v.vehicle_id = ?
                ");
                $stmt->bind_param("i", $vehicle_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $vehicle_info = $result->fetch_assoc();
                
                if ($vehicle_info) {
                    // Decrement the appropriate counter based on vehicle type
                    if ($vehicle_info['vehicle_type_id'] == 1) {
                        // Car - decrement car count
                        $stmt = $conn->prepare("
                            UPDATE parking_spaces 
                            SET parked_car_count = GREATEST(0, parked_car_count - 1)
                            WHERE id = ?
                        ");
                    } else {
                        // Motorcycle - decrement motorcycle count
                        $stmt = $conn->prepare("
                            UPDATE parking_spaces 
                            SET parked_motorcycle_count = GREATEST(0, parked_motorcycle_count - 1)
                            WHERE id = ?
                        ");
                    }
                    
                    $stmt->bind_param("i", $parking_space_id);
                    
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to update parking space count: ' . $stmt->error);
                    }
                }
            }

            // Commit transaction
            $conn->commit();

            // Get exit details for success modal
            $stmt = $conn->prepare("
                SELECT 
                    v.plate_number,
                    pl.time_in,
                    pl.time_out
                FROM parking_logs pl
                JOIN vehicles_table v ON pl.vehicle_id = v.vehicle_id
                WHERE pl.log_id = ?
            ");
            $stmt->bind_param("i", $log_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $exit_data = $result->fetch_assoc();

            // Simple: subtract time_out from time_in
            $duration_text = calculateDuration($exit_data['time_in'], $exit_data['time_out']);

            echo json_encode([
                'success' => true,
                'message' => 'Vehicle exited successfully',
                'exit_summary' => [
                    'plate_number' => $exit_data['plate_number'],
                    'exit_time' => date('M d, Y h:i A', strtotime($exit_data['time_out'])),
                    'total_duration' => $duration_text
                ]
            ]);

        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            throw $e;
        }
        
        exit();
    }

    // Invalid action
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action specified'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>