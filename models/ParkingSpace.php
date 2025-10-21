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
     * Get distribution of AVAILABLE parking slots by location with optional filters.
     * Returns: array of ['location' => string, 'slots' => int (available)]
     */
    public function getSlotDistribution($userTypeFilter = 'all', $vehicleTypeFilter = 'all')
    {
        $distribution = [];

        // Build WHERE conditions for filters
        $whereConditions = ["1=1"];
        $params = [];
        $types = "";

        // Apply user type filter
        if ($userTypeFilter !== 'all') {
            $userTypeMap = [
                'general' => 1,
                'faculty' => 2,
                'executive' => 3,
                'guest' => 4
            ];
            
            if (isset($userTypeMap[$userTypeFilter])) {
                $whereConditions[] = "psa.user_type_id = ?";
                $params[] = $userTypeMap[$userTypeFilter];
                $types .= "i";
            }
        }

        // Apply vehicle type filter
        if ($vehicleTypeFilter !== 'all') {
            $vehicleTypeMap = [
                'car' => 1,
                'motorcycle' => 2
            ];
            
            if (isset($vehicleTypeMap[$vehicleTypeFilter])) {
                $whereConditions[] = "psa.vehicle_type_id = ?";
                $params[] = $vehicleTypeMap[$vehicleTypeFilter];
                $types .= "i";
            }
        }

        $whereClause = implode(" AND ", $whereConditions);

        // Query to get total allocated slots and occupied slots per location
        $query = "
            SELECT 
                ps.id,
                ps.name,
                SUM(psa.allocated_slots) AS total_slots,
                COALESCE((
                    SELECT COUNT(*)
                    FROM parking_logs pl
                    JOIN vehicles_table v ON pl.vehicle_id = v.vehicle_id
                    WHERE pl.parking_space_id = ps.id
                    AND pl.status = 'Parked'
                    " . ($vehicleTypeFilter !== 'all' && isset($vehicleTypeMap[$vehicleTypeFilter]) ? 
                        "AND v.vehicle_type_id = " . $vehicleTypeMap[$vehicleTypeFilter] : "") . "
                ), 0) AS occupied_slots
            FROM parking_spaces ps
            LEFT JOIN parking_slot_allocations psa 
                ON ps.id = psa.parking_space_id
            WHERE $whereClause
            GROUP BY ps.id, ps.name
            HAVING total_slots > 0
            ORDER BY ps.name
        ";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($query);
        }

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $totalSlots = (int) $row['total_slots'];
                $occupiedSlots = (int) $row['occupied_slots'];
                $availableSlots = max(0, $totalSlots - $occupiedSlots);

                $distribution[] = [
                    'location' => $row['name'],
                    'slots' => $availableSlots  // Return AVAILABLE slots, not total
                ];
            }
        }

        if (isset($stmt)) {
            $stmt->close();
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

    /**
     * Check if a parking space has any parked vehicles.
     * Returns: ['has_parked' => bool, 'count' => int, 'vehicles' => array]
     */
    public function hasParkedVehicles($parking_space_id)
    {
        $query = "
            SELECT 
                pl.vehicle_id,
                v.plate_number,
                v.color,
                v.make,
                v.model,
                vt.vehicle_type,
                u.firstname,
                u.lastname
            FROM parking_logs pl
            JOIN vehicles_table v ON pl.vehicle_id = v.vehicle_id
            JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
            JOIN users_table u ON v.user_id = u.user_id
            WHERE pl.parking_space_id = ? 
            AND pl.status = 'Parked'
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $parking_space_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $vehicles = [];
        while ($row = $result->fetch_assoc()) {
            $vehicles[] = [
                'plate_number' => $row['plate_number'],
                'vehicle_type' => $row['vehicle_type'],
                'color' => $row['color'],
                'make' => $row['make'],
                'model' => $row['model'],
                'owner' => $row['firstname'] . ' ' . $row['lastname']
            ];
        }

        $stmt->close();

        return [
            'has_parked' => count($vehicles) > 0,
            'count' => count($vehicles),
            'vehicles' => $vehicles
        ];
    }
    
}
?>
