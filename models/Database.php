<?php
/**
 * models/Database.php
 * Handles database connection and provides reusable connection methods
 */
class Database {
    private $host = 'localhost:3307';
    private $username = 'root';
    private $password = '';
    private $database = 'pmsdb';
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    /**
     * Create database connection
     * @return void
     */
    private function connect() {
        try {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Set charset to utf8mb4 for proper character encoding
            $this->connection->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Sorry, we're experiencing technical difficulties. Please try again later.");
        }
    }
    
    /**
     * Get database connection
     * @return mysqli
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Close database connection
     * @return void
     */
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Execute a prepared statement query
     * @param string $query - SQL query with placeholders
     * @param string $types - Parameter types (e.g., "ss" for two strings)
     * @param array $params - Parameters to bind
     * @return mysqli_result|bool
     */
    public function executeQuery($query, $types = '', $params = []) {
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->connection->error);
            return false;
        }
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        
        if ($stmt->error) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
}
?>