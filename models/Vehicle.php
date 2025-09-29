<?php
class Vehicle {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register a new vehicle
     */
    public function register($vehicleData) {
        $user_id         = $vehicleData['user_id'];
        $plate_number    = trim($vehicleData['plate_number']);
        $vehicle_type_id = $vehicleData['vehicle_type_id'];
        $color           = trim($vehicleData['color']);
        $make            = trim($vehicleData['make']);
        $model           = trim($vehicleData['model']);
        $year            = intval($vehicleData['year']);

        // Validate input
        $validation = $this->validateVehicleData($vehicleData);
        if (!$validation['success']) {
            return $validation;
        }

        // Check if user already has 2 vehicles
        $check_query = "SELECT COUNT(*) FROM vehicles_table WHERE user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count >= 2) {
            return ['success' => false, 'message' => 'You’ve reached the maximum of 2 registered vehicles.'];
        }

        // Insert vehicle
        $insert_query = "INSERT INTO vehicles_table (user_id, vehicle_type_id, plate_number, color, make, model, year, qr_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $this->conn->prepare($insert_query);
        if (!$insert_stmt) {
            return ['success' => false, 'message' => 'Database error: ' . $this->conn->error];
        }

        $qr_token = $vehicleData['qr_token'];

        $insert_stmt->bind_param("iissssis", $user_id, $vehicle_type_id, $plate_number, $color, $make, $model, $year, $qr_token);


        if ($insert_stmt->execute()) {
            $insert_stmt->close();
            return ['success' => true, 'message' => 'Vehicle registered successfully!'];
        } else {
            $error_message = $insert_stmt->error;
            $insert_stmt->close();
            return ['success' => false, 'message' => 'Registration failed: ' . $error_message];
        }
    }

    public function getVehiclesByUser($user_id) {
    $query = "
        SELECT v.*, vt.vehicle_type 
        FROM vehicles_table v
        JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
        WHERE v.user_id = ?
        ORDER BY v.vehicle_id ASC
        LIMIT 2
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }

    $stmt->close();
    return $vehicles;
}



    /**
     * Validate vehicle input
     */
    private function validateVehicleData($data) {
        $required_fields = ['plate_number', 'vehicle_type_id', 'color', 'make', 'model', 'year'];

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => 'All fields are required!'];
            }
        }

        if (!preg_match('/^[A-Za-z0-9\-]+$/', $data['plate_number'])) {
            return ['success' => false, 'message' => 'Invalid plate number format!'];
        }

        if ($data['year'] < 1900 || $data['year'] > 2099) {
            return ['success' => false, 'message' => 'Year must be between 1900 and 2099!'];
        }

        return ['success' => true];
    }

    // Temporary Viewing

    public function getVehicleWithUserByToken($token) {
    $stmt = $this->conn->prepare("
        SELECT 
            v.plate_number, v.color, v.make, v.model, v.year,
            vt.vehicle_type,
            u.firstname, u.lastname, u.email, u.phone_number,
            ut.user_type
        FROM vehicles_table v
        JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
        JOIN users_table u ON v.user_id = u.user_id
        JOIN user_type_table ut ON u.user_type_id = ut.user_type_id
        WHERE v.qr_token = ?
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
}
?>
