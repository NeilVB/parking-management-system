<?php
// models/ParkingLog.php

class ParkingLog {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get latest parking log by vehicle_id
     */
    public function getLatestLog($vehicle_id) {
        $query = "SELECT * FROM parking_logs WHERE vehicle_id = ? ORDER BY log_id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create a new log (usually used for first-time entry)
     */
    public function createLog($vehicle_id, $scanned_by, $status = 'outside') {
        $query = "INSERT INTO parking_logs (vehicle_id, scanned_by, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iis", $vehicle_id, $scanned_by, $status);
        $stmt->execute();
        return $stmt->insert_id;
    }

    /**
     * Mark vehicle as entered (at the gate)
     */
    public function markEntered($vehicle_id, $scanned_by) {
        $query = "
            INSERT INTO parking_logs (vehicle_id, scanned_by, status, time_in)
            VALUES (?, ?, 'entered', NOW())
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $vehicle_id, $scanned_by);
        return $stmt->execute();
    }

    /**
     * Mark vehicle as parked in a specific space
     */
    public function markParked($vehicle_id, $space_id, $scanned_by) {
        $query = "
            UPDATE parking_logs
            SET status = 'parked', parking_space_id = ?, updated_at = CURRENT_TIMESTAMP
            WHERE vehicle_id = ? AND status = 'entered'
            ORDER BY log_id DESC LIMIT 1
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $space_id, $vehicle_id);
        return $stmt->execute();
    }

    /**
     * Mark vehicle as exiting (leaving the parking but still inside school)
     */
    public function markExiting($vehicle_id, $scanned_by) {
        $query = "
            UPDATE parking_logs
            SET status = 'exiting', time_out = NOW(), updated_at = CURRENT_TIMESTAMP
            WHERE vehicle_id = ? AND status = 'parked'
            ORDER BY log_id DESC LIMIT 1
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $vehicle_id);
        return $stmt->execute();
    }

    /**
     * Mark vehicle as outside (completely left the school)
     */
    public function markOutside($vehicle_id, $scanned_by) {
        $query = "
            UPDATE parking_logs
            SET status = 'outside', time_out = NOW(), updated_at = CURRENT_TIMESTAMP
            WHERE vehicle_id = ? AND status IN ('entered', 'exiting', 'parked')
            ORDER BY log_id DESC LIMIT 1
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $vehicle_id);
        return $stmt->execute();
    }
}
?>
