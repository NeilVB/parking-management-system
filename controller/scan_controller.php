<?php
// controllers/scan_controller.php
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/ParkingSpace.php';

class ScanController {
    private $conn;
    private $vehicleModel;
    private $parkingSpaceModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->vehicleModel = new Vehicle($db);
        $this->parkingSpaceModel = new ParkingSpace($db);
    }

    /**
     * Step 1: Scan QR Code → Fetch vehicle + owner info, then show spaces
     */
    public function scanQRCode($qr_token, $scanned_by = null) {
        if (empty($qr_token)) {
            return ['success' => false, 'message' => 'QR code token is missing.'];
        }

        // ✅ 1. Fetch vehicle info (includes driver/user details)
        $vehicleData = $this->vehicleModel->getVehicleWithUserByToken($qr_token);
        if (!$vehicleData) {
            return ['success' => false, 'message' => 'Invalid or unregistered QR code.'];
        }

        // ✅ 2. Get vehicle’s last known status
        $status = $this->getVehicleStatus($qr_token);

        // ✅ 3. Normalize and clean the vehicle data for the frontend
        $vehicle = [
            'vehicle_id'   => $vehicleData['vehicle_id'] ?? $vehicleData['id'] ?? null,
            'plate_number' => $vehicleData['plate_number'] ?? $vehicleData['plate_no'] ?? 'Unknown',
            'vehicle_type' => $vehicleData['vehicle_type_name'] 
                                ?? $vehicleData['vehicle_type'] 
                                ?? $vehicleData['type'] 
                                ?? 'Unknown',
            'driver_name'  => $vehicleData['user_fullname'] 
                                ?? $vehicleData['owner_name'] 
                                ?? $vehicleData['user_name'] 
                                ?? 'N/A',
            'status'       => $status ?? 'outside'
        ];

        // ✅ 4. Fetch available parking spaces filtered by vehicle/user type
        $availableSpaces = $this->getAvailableSpaces(
            $vehicleData['vehicle_type_id'] ?? null,
            $vehicleData['user_type_id'] ?? null
        );

        return [
            'success' => true,
            'message' => 'QR code scanned successfully.',
            'vehicle' => $vehicle,
            'available_spaces' => $availableSpaces
        ];
    }

    /**
     * Helper: Get last vehicle log status
     */
    private function getVehicleStatus($qr_token) {
        $query = "
            SELECT status 
            FROM parking_logs 
            WHERE vehicle_id = (
                SELECT vehicle_id 
                FROM vehicles_table 
                WHERE qr_token = ?
            )
            ORDER BY log_id DESC 
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $qr_token);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result['status'] ?? 'outside';
    }

    /**
     * Helper: Get available spaces filtered by vehicle/user type
     */
    private function getAvailableSpaces($vehicleTypeId, $userTypeId) {
        if (!$vehicleTypeId || !$userTypeId) {
            return [];
        }

        $spaces = $this->parkingSpaceModel->getAllSpaces();
        $available = [];

        foreach ($spaces as $space) {
            if ($this->parkingSpaceModel->canPark($space['id'], $vehicleTypeId, $userTypeId)) {
                $available[] = [
                    'slot_id'   => $space['id'],
                    'slot_name' => $space['slot_name'],
                    'location'  => $space['location'] ?? 'N/A'
                ];
            }
        }

        return $available;
    }

    /**
     * Step 2: Mark vehicle as entered
     */
    public function markEntered($vehicle_id, $scanned_by) {
        return $this->createOrUpdateLog($vehicle_id, null, 'entered', $scanned_by);
    }

    /**
     * Step 3: Park vehicle in a space
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
        $query = "
            UPDATE parking_logs 
            SET status = 'outside', exit_time = NOW(), scanned_by = ? 
            WHERE vehicle_id = ? AND status != 'outside'
            ORDER BY log_id DESC 
            LIMIT 1
        ";

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

        $check = $this->conn->prepare("
            SELECT log_id, status 
            FROM parking_logs 
            WHERE vehicle_id = ? 
            ORDER BY log_id DESC 
            LIMIT 1
        ");
        $check->bind_param("i", $vehicle_id);
        $check->execute();
        $log = $check->get_result()->fetch_assoc();

        if ($log) {
            $query = "
                UPDATE parking_logs 
                SET space_id = ?, status = ?, scanned_by = ? 
                WHERE log_id = ?
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isii", $space_id, $status, $scanned_by, $log['log_id']);
        } else {
            $query = "
                INSERT INTO parking_logs (vehicle_id, space_id, status, scanned_by)
                VALUES (?, ?, ?, ?)
            ";
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
