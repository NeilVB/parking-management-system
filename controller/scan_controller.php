<?php
// controllers/scan_controller.php
require_once __DIR__ . '/../models/Vehicle.php';

class ScanController {
    private $conn;
    private $vehicleModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->vehicleModel = new Vehicle($db);
    }

    /**
     * Step 1: Scan a QR Code and fetch vehicle + user info
     */
    public function scanQRCode($qr_token, $scanned_by = null) {
        if (empty($qr_token)) {
            return ['success' => false, 'message' => 'QR code token is missing.'];
        }

        // Fetch vehicle & user info
        $vehicleData = $this->vehicleModel->getVehicleWithUserByToken($qr_token);

        if (!$vehicleData) {
            return ['success' => false, 'message' => 'Invalid or unregistered QR code.'];
        }

        // Check if this vehicle currently has an active log
        $query = "SELECT * FROM parking_logs WHERE vehicle_id = (
                        SELECT vehicle_id FROM vehicles_table WHERE qr_token = ?
                  ) ORDER BY log_id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $qr_token);
        $stmt->execute();
        $log = $stmt->get_result()->fetch_assoc();

        $vehicle_status = $log['status'] ?? 'outside';

        return [
            'success' => true,
            'message' => 'QR code scanned successfully.',
            'vehicle' => $vehicleData,
            'status' => $vehicle_status
        ];
    }

    /**
     * Step 2: Mark vehicle as "entered"
     */
    public function markEntered($vehicle_id, $scanned_by) {
        return $this->createOrUpdateLog($vehicle_id, null, 'entered', $scanned_by);
    }

    /**
     * Step 3: Park the vehicle into a space
     */
    public function parkVehicle($vehicle_id, $space_id, $scanned_by) {
        return $this->createOrUpdateLog($vehicle_id, $space_id, 'parked', $scanned_by);
    }

    /**
     * Step 4: Mark as exiting
     */
    public function markExiting($vehicle_id, $scanned_by) {
        return $this->createOrUpdateLog($vehicle_id, null, 'exiting', $scanned_by);
    }

    /**
     * Step 5: Mark as outside
     */
    public function markOutside($vehicle_id, $scanned_by) {
        // Update last log record
        $query = "UPDATE parking_logs 
                  SET status = 'outside', exit_time = NOW(), scanned_by = ? 
                  WHERE vehicle_id = ? AND status != 'outside'
                  ORDER BY log_id DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $scanned_by, $vehicle_id);
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Vehicle marked as outside.'];
        } else {
            return ['success' => false, 'message' => 'Failed to update status.'];
        }
    }

    /**
     * Internal helper — create or update parking_logs entry
     */
    private function createOrUpdateLog($vehicle_id, $space_id, $status, $scanned_by) {
        if (!$vehicle_id) {
            return ['success' => false, 'message' => 'Vehicle ID is missing.'];
        }

        // Check for existing active log
        $check = $this->conn->prepare("
            SELECT log_id, status FROM parking_logs 
            WHERE vehicle_id = ? ORDER BY log_id DESC LIMIT 1
        ");
        $check->bind_param("i", $vehicle_id);
        $check->execute();
        $log = $check->get_result()->fetch_assoc();

        if ($log) {
            // Update the existing log
            $query = "UPDATE parking_logs 
                      SET space_id = ?, status = ?, scanned_by = ? 
                      WHERE log_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isii", $space_id, $status, $scanned_by, $log['log_id']);
        } else {
            // Create a new log
            $query = "INSERT INTO parking_logs (vehicle_id, space_id, status, scanned_by)
                      VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iisi", $vehicle_id, $space_id, $status, $scanned_by);
        }

        if ($stmt->execute()) {
            return ['success' => true, 'message' => "Vehicle status updated to '$status'."];
        } else {
            return ['success' => false, 'message' => 'Database error: ' . $stmt->error];
        }
    }
}
?>