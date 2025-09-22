<?php
class User {
    private $conn;
    private $table = "users_table";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register new user
     */
    public function register($userData) {
        // Extract user data
        $firstName = trim($userData['FirstName']);
        $lastName = trim($userData['LastName']);
        $plateNumber = trim($userData['PlateNumber']);
        $phoneNumber = trim($userData['PhoneNumber']);
        $userType = $userData['Type'];
        $vehicleType = $userData['VehicleType'];
        $email = trim($userData['Email']);
        $password = $userData['password'];
        
        // Validate input data
        $validation = $this->validateRegistrationData($userData);
        if (!$validation['success']) {
            return $validation;
        }
        
        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already exists! Please use a different email.'];
        }
        
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Map user type to user_type_id
        $user_type_id = $this->getUserTypeId($userType);
        if (!$user_type_id) {
            return ['success' => false, 'message' => 'Invalid user type selected!'];
        }
        
        // Map vehicle type to vehicle_type_id
        $vehicle_type_id = $this->getVehicleTypeId($vehicleType);
        if (!$vehicle_type_id) {
            return ['success' => false, 'message' => 'Invalid vehicle type selected!'];
        }
        
        // Prepare SQL statement to insert new user
        $insert_query = "INSERT INTO users_table (firstname, lastname, phone_number, user_type_id, vehicle_type_id, plate_number, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $this->conn->prepare($insert_query);
        if (!$insert_stmt) {
            return ['success' => false, 'message' => 'Database error: ' . $this->conn->error];
        }
        
        $insert_stmt->bind_param("ssiissss", $firstName, $lastName, $phoneNumber, $user_type_id, $vehicle_type_id, $plateNumber, $email, $hashed_password);
        
        // Execute the insert statement
        if ($insert_stmt->execute()) {
            $insert_stmt->close();
            return ['success' => true, 'message' => 'Registration successful! You can now log in.'];
        } else {
            $error_message = $insert_stmt->error;
            $insert_stmt->close();
            return ['success' => false, 'message' => 'Registration failed: ' . $error_message];
        }
    }

    /**
     * Authenticate user login
     * @param string $email - User email
     * @param string $password - User password
     * @return array - Result with success/error status, message, and user data
     */
    public function login($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required!'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format!'];
        }
        
        // Prepare SQL statement to find user by email
        $login_query = "SELECT u.user_id, u.firstname, u.lastname, u.email, u.password, ut.user_type 
                       FROM users_table u 
                       JOIN user_type_table ut ON u.user_type_id = ut.user_type_id 
                       WHERE u.email = ?";
        
        $login_stmt = $this->conn->prepare($login_query);
        if (!$login_stmt) {
            return ['success' => false, 'message' => 'Database error occurred'];
        }
        
        $login_stmt->bind_param("s", $email);
        $login_stmt->execute();
        $result = $login_stmt->get_result();
        
        // Check if user exists
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $login_stmt->close();
                return [
                    'success' => true,
                    'message' => 'Login successful!',
                    'user' => [
                        'user_id' => $user['user_id'],
                        'firstname' => $user['firstname'],
                        'lastname' => $user['lastname'],
                        'email' => $user['email'],
                        'user_type' => $user['user_type']
                    ]
                ];
            } else {
                $login_stmt->close();
                return ['success' => false, 'message' => 'Invalid email or password!'];
            }
        } else {
            $login_stmt->close();
            return ['success' => false, 'message' => 'Invalid email or password!'];
        }
    }
    
    /**
     * Get user by ID (for remember me functionality)
     * @param int $user_id - User ID
     * @return array|false - User data or false if not found
     */
    public function getUserById($user_id) {
        $query = "SELECT u.user_id, u.firstname, u.lastname, u.email, ut.user_type 
                 FROM users_table u 
                 JOIN user_type_table ut ON u.user_type_id = ut.user_type_id 
                 WHERE u.user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Private helper methods
     */
    
    private function validateRegistrationData($data) {
        $required_fields = ['FirstName', 'LastName', 'PlateNumber', 'PhoneNumber', 'Type', 'VehicleType', 'Email', 'password'];
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => 'All fields are required!'];
            }
        }
        
        if (!filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format!'];
        }
        
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters long!'];
        }
        
        return ['success' => true];
    }
    
    private function emailExists($email) {
        $query = "SELECT email FROM users_table WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    private function getUserTypeId($userType) {
        $user_type_map = [
            'Student' => 1,
            'Faculty' => 2,
            'Executive' => 3,
            'Guest' => 4
        ];
        
        return isset($user_type_map[$userType]) ? $user_type_map[$userType] : false;
    }
    
    private function getVehicleTypeId($vehicleType) {
        $vehicle_type_map = [
            'Car' => 1,
            'Motorcycle' => 2
        ];
        
        return isset($vehicle_type_map[$vehicleType]) ? $vehicle_type_map[$vehicleType] : false;
    }
}
?>
