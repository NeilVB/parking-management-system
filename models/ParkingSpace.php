<?php
// models/ParkingSpace.php

class ParkingSpace {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get total available slots grouped by vehicle type.
     * Returns: ['car' => int, 'motorcycle' => int]
     */
    public function getVehicleCounts() {
        $counts = ['car' => 0, 'motorcycle' => 0];

        $query = "SELECT vehicle_type, max_space FROM parking_spaces";
        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $type = strtolower($row['vehicle_type']);
                $slots = (int)$row['max_space'];

                if (isset($counts[$type])) {
                    $counts[$type] += $slots;
                }
            }
        }

        return $counts;
    }

    /**
     * Get distribution of parking slots by location (if location column exists).
     * Returns: array of ['location' => string, 'slots' => int]
     */
    public function getSlotDistribution() {
        $distribution = [];

        $query = "SELECT name, SUM(max_space) as total_slots FROM parking_spaces GROUP BY name";
        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $distribution[] = [
                    'location' => $row['name'],
                    'slots' => (int)$row['total_slots']
                ];
            }
        }

        return $distribution;
    }
    /**
     * Get all parking spaces (for listing or admin view)
     */
    public function getAllSpaces() {
        $query = "SELECT * FROM parking_spaces ORDER BY id DESC";
        $result = $this->conn->query($query);
        $spaces = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $spaces[] = $row;
            }
        }

        return $spaces;
    }
}
