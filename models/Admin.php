<?php
// models/Admin.php
class Admin {
    private $conn;
    private $table = "admin_table";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required!'];
        }

        $query = "SELECT admin_id, firstname, lastname, admin_email, admin_password 
                FROM {$this->table} WHERE admin_email = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $stmt->close();

            $input_hash = hash('sha256', $password);
            $stored_hash = $admin['admin_password'];

            // DEBUG OUTPUT
            echo "<pre>";
            echo "Input Email: $email\n";
            echo "Input Password: $password\n";
            echo "Input Hash: $input_hash\n";
            echo "Stored Hash: $stored_hash\n";
            echo "Match: " . ($input_hash === $stored_hash ? "YES" : "NO") . "\n";
            echo "</pre>";

            if ($input_hash === $stored_hash) {
                return [
                    'success' => true,
                    'message' => 'Login successful!',
                    'admin' => [
                        'admin_id' => $admin['admin_id'],
                        'firstname' => $admin['firstname'],
                        'lastname' => $admin['lastname'],
                        'admin_email' => $admin['admin_email']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'Invalid email or password!'];
            }
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Invalid email or password!'];
    }


    public function getAdminById($admin_id) {
        $query = "SELECT admin_id, firstname, lastname, admin_email 
                  FROM {$this->table} WHERE admin_id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $stmt->close();
            return $admin;
        }

        $stmt->close();
        return false;
    }
}
?>
