<?php
// models/ParkingSpace.php

class ParkingSpace
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Get total available slots grouped by vehicle type.
     * Returns: ['car' => int, 'motorcycle' => int]
     */
    public function getVehicleCounts()
    {
        $counts = ['car' => 0, 'motorcycle' => 0];

        $query = "
            SELECT 
                vehicle_type_id,
                SUM(allocated_slots) AS total_slots
            FROM parking_slot_allocations
            GROUP BY vehicle_type_id
        ";

        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($row['vehicle_type_id'] == 1) {
                    $counts['car'] = (int) $row['total_slots'];
                } else if ($row['vehicle_type_id'] == 2) {
                    $counts['motorcycle'] = (int) $row['total_slots'];
                }
            }
        }

        return $counts;
    }

    /**
     * Get distribution of parking slots by location.
     * Returns: array of ['location' => string, 'slots' => int]
     */
    public function getSlotDistribution()
    {
        $distribution = [];

        $query = "
            SELECT 
                ps.name,
                SUM(psa.allocated_slots) AS total_slots
            FROM parking_spaces ps
            LEFT JOIN parking_slot_allocations psa 
                ON ps.id = psa.parking_space_id
            GROUP BY ps.id, ps.name
            ORDER BY ps.name
        ";

        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $distribution[] = [
                    'location' => $row['name'],
                    'slots' => (int) $row['total_slots']
                ];
            }
        }

        return $distribution;
    }

    /**
     * Get total slots grouped by driver type.
     * Returns: ['general' => int, 'faculty' => int, 'executive' => int, 'guest' => int]
     */
    public function getDriverTypeCounts()
    {
        $counts = [
            'general' => 0,
            'faculty' => 0,
            'executive' => 0,
            'guest' => 0
        ];

        $query = "
            SELECT 
                user_type_id,
                SUM(allocated_slots) AS total_slots
            FROM parking_slot_allocations
            GROUP BY user_type_id
        ";

        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                switch ($row['user_type_id']) {
                    case 1:
                        $counts['general'] = (int) $row['total_slots'];
                        break;
                    case 2:
                        $counts['faculty'] = (int) $row['total_slots'];
                        break;
                    case 3:
                        $counts['executive'] = (int) $row['total_slots'];
                        break;
                    case 4:
                        $counts['guest'] = (int) $row['total_slots'];
                        break;
                }
            }
        }

        return $counts;
    }

    /**
     * Get all parking spaces with their allocations.
     */
    public function getAllSpaces()
    {
        $spaces = [];
        $query = "SELECT * FROM parking_spaces ORDER BY id DESC";
        $result = $this->conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $spaces[] = $row;
            }
        }

        return $spaces;
    }

    /**
     * Get allocations for a specific parking space.
     */
    public function getAllocations($parking_space_id)
    {
        $allocations = [];

        $query = "
            SELECT 
                psa.allocation_id,
                psa.vehicle_type_id,
                psa.user_type_id,
                psa.allocated_slots,
                vt.vehicle_type,
                ut.user_type
            FROM parking_slot_allocations psa
            JOIN vehicle_type_table vt 
                ON psa.vehicle_type_id = vt.vehicle_type_id
            JOIN user_type_table ut 
                ON psa.user_type_id = ut.user_type_id
            WHERE psa.parking_space_id = ?
            ORDER BY psa.vehicle_type_id, psa.user_type_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $parking_space_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $allocations[] = $row;
            }
        }

        $stmt->close();
        return $allocations;
    }

    /**
     * Get total allocated slots by vehicle type for a parking space.
     */
    public function getTotalSlotsByVehicleType($parking_space_id, $vehicle_type_id)
    {
        $query = "
            SELECT SUM(allocated_slots) AS total
            FROM parking_slot_allocations
            WHERE parking_space_id = ? AND vehicle_type_id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $parking_space_id, $vehicle_type_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Check if a user type can park in a specific parking space with specific vehicle type.
     */
    public function canPark($parking_space_id, $vehicle_type_id, $user_type_id)
    {
        $query = "
            SELECT allocated_slots
            FROM parking_slot_allocations
            WHERE parking_space_id = ? 
              AND vehicle_type_id = ? 
              AND user_type_id = ?
              AND allocated_slots > 0
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $parking_space_id, $vehicle_type_id, $user_type_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $canPark = $result->num_rows > 0;
        $stmt->close();

        return $canPark;
    }
}
?>
