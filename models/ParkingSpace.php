<?php
// models/ParkingSpace.php

class ParkingSpace
{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    /**
     * Get total available slots grouped by vehicle type.
     * Returns: ['car' => int, 'motorcycle' => int]
     */
    public function getVehicleCounts(){
        $counts = ['car' => 0, 'motorcycle' => 0];

        // Get total allocated slots
        $query = "
            SELECT 
                vehicle_type_id,
                SUM(allocated_slots) AS total_slots
            FROM parking_slot_allocations
            GROUP BY vehicle_type_id
        ";

        $result = $this->conn->query($query);
        $total = ['car' => 0, 'motorcycle' => 0];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($row['vehicle_type_id'] == 1) {
                    $total['car'] = (int) $row['total_slots'];
                } else if ($row['vehicle_type_id'] == 2) {
                    $total['motorcycle'] = (int) $row['total_slots'];
                }
            }
        }

        // Get currently parked vehicles (status = 'Parked' in parking_logs)
        $occupiedQuery = "
            SELECT 
                v.vehicle_type_id,
                COUNT(*) AS occupied_count
            FROM parking_logs pl
            JOIN vehicles_table v ON pl.vehicle_id = v.vehicle_id
            WHERE pl.status = 'Parked'
            GROUP BY v.vehicle_type_id
        ";

        $occupiedResult = $this->conn->query($occupiedQuery);
        $occupied = ['car' => 0, 'motorcycle' => 0];

        if ($occupiedResult) {
            while ($row = $occupiedResult->fetch_assoc()) {
                if ($row['vehicle_type_id'] == 1) {
                    $occupied['car'] = (int) $row['occupied_count'];
                } else if ($row['vehicle_type_id'] == 2) {
                    $occupied['motorcycle'] = (int) $row['occupied_count'];
                }
            }
        }

        // Calculate available = total - occupied
        $counts['car'] = max(0, $total['car'] - $occupied['car']);
        $counts['motorcycle'] = max(0, $total['motorcycle'] - $occupied['motorcycle']);

        return $counts;
    }

    /**
     * Get distribution of parking slots by location with optional filters.
     * Returns: array of ['location' => string, 'slots' => int]
     */
    public function getSlotDistribution($userTypeFilter = 'all', $vehicleTypeFilter = 'all')
    {
        $distribution = [];

        // Base query
        $query = "
            SELECT 
                ps.name,
                SUM(psa.allocated_slots) AS total_slots
            FROM parking_spaces ps
            LEFT JOIN parking_slot_allocations psa 
                ON ps.id = psa.parking_space_id
            WHERE 1=1
        ";

        // Apply user type filter
        if ($userTypeFilter !== 'all') {
            $userTypeMap = [
                'general' => 1,
                'faculty' => 2,
                'executive' => 3,
                'guest' => 4
            ];
            
            if (isset($userTypeMap[$userTypeFilter])) {
                $query .= " AND psa.user_type_id = " . $userTypeMap[$userTypeFilter];
            }
        }

        // Apply vehicle type filter
        if ($vehicleTypeFilter !== 'all') {
            $vehicleTypeMap = [
                'car' => 1,
                'motorcycle' => 2
            ];
            
            if (isset($vehicleTypeMap[$vehicleTypeFilter])) {
                $query .= " AND psa.vehicle_type_id = " . $vehicleTypeMap[$vehicleTypeFilter];
            }
        }

        $query .= "
            GROUP BY ps.id, ps.name
            HAVING total_slots > 0
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

        /**
     * Get available parking spaces filtered by vehicle type.
     * This is used after scanning a QR code to show where the driver can park.
     */
    public function getAvailableSpaces($vehicle_type)
    {
        $spaces = [];

        $query = "
            SELECT 
                ps.id AS space_id,
                ps.name AS space_name,
                ps.location,
                ps.capacity,
                ps.is_occupied,
                vt.vehicle_type
            FROM parking_spaces ps
            LEFT JOIN vehicle_type_table vt ON ps.vehicle_type_id = vt.vehicle_type_id
            WHERE ps.is_occupied = 0
              AND (vt.vehicle_type = ? OR vt.vehicle_type = 'both')
            ORDER BY ps.name ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $vehicle_type);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $spaces[] = $row;
        }

        $stmt->close();
        return $spaces;
    }

    /**
     * Assign a vehicle to a specific parking space and mark it as occupied.
     */
    public function assignVehicleToSpace($vehicle_id, $space_id)
    {
        try {
            // Start transaction
            $this->conn->begin_transaction();

            // Mark the space as occupied
            $update = $this->conn->prepare("
                UPDATE parking_spaces
                SET is_occupied = 1
                WHERE id = ?
            ");
            $update->bind_param("i", $space_id);
            $update->execute();

            // Log the parking record (optional table: parking_records)
            $insert = $this->conn->prepare("
                INSERT INTO parking_records (vehicle_id, space_id, time_in, status)
                VALUES (?, ?, NOW(), 'parked')
            ");
            $insert->bind_param("ii", $vehicle_id, $space_id);
            $insert->execute();

            // Commit the changes
            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Vehicle successfully parked.'
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

}
?>
