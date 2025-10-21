<?php
// ajax/exit_vehicle.php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controller/config.php';

// Set timezone
date_default_timezone_set('Asia/Manila');
$conn->query("SET time_zone = '+08:00'");

// Check if admin is authenticated
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get action type
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

/**
 * Calculate duration between two times
 */
function calculateDuration($time_in, $time_out = null) {
    if ($time_out === null) {
        $time_out = date('Y-m-d H:i:s');
    }
    
    $start = strtotime($time_in);
    $end = strtotime($time_out);
    $diff_seconds = $end - $start;
    
    // Handle negative duration
    if ($diff_seconds < 0) {
        return 'Invalid duration';
    }
    
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

        $conn->begin_transaction();

        try {
            // Use MySQL NOW() to ensure timezone consistency
            $stmt = $conn->prepare("
                UPDATE parking_logs 
                SET time_out = NOW(), 
                    status = 'outside',
                    updated_at = CURRENT_TIMESTAMP
                WHERE log_id = ?
            ");
            $stmt->bind_param("i", $log_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update parking log: ' . $stmt->error);
            }

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

            if ($parking_space_id) {
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
                    if ($vehicle_info['vehicle_type_id'] == 1) {
                        $stmt = $conn->prepare("
                            UPDATE parking_spaces 
                            SET parked_car_count = GREATEST(0, parked_car_count - 1)
                            WHERE id = ?
                        ");
                    } else {
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

            $conn->commit();

            // Get exit details
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
            $conn->rollback();
            throw $e;
        }
        
        exit();
    }

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