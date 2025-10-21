<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_user':
        $id = $_GET['id'] ?? 0;
        $query = "SELECT * FROM users_table WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'user' => $user]);
        break;

    case 'get_vehicles':
        $userId = $_GET['user_id'] ?? 0;
        $query = "SELECT v.*, vt.vehicle_type, ut.user_type 
                FROM vehicles_table v
                JOIN vehicle_type_table vt ON v.vehicle_type_id = vt.vehicle_type_id
                JOIN users_table u ON v.user_id = u.user_id
                JOIN user_type_table ut ON u.user_type_id = ut.user_type_id
                WHERE v.user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicles = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'vehicles' => $vehicles]);
        break;

    case 'update_user':
        $id = $_POST['user_id'];
        $query = "UPDATE users_table SET 
                  firstname = ?, 
                  lastname = ?, 
                  email = ?, 
                  phone_number = ?,
                  user_type_id = ?
                  WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssii', 
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['user_type_id'],
            $id
        );
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;

    case 'delete_user':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['user_id'];
        $query = "DELETE FROM users_table WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;

    /* FOR VEHICLE MANAGEMENT */

    case 'get_vehicle':
        $id = $_GET['id'] ?? 0;
        $query = "SELECT * FROM vehicles_table WHERE vehicle_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicle = $result->fetch_assoc();
        echo json_encode(['success' => true, 'vehicle' => $vehicle]);
        break;

    case 'update_vehicle':
        $query = "UPDATE vehicles_table SET 
                  plate_number = ?,
                  make = ?,
                  model = ?,
                  year = ?,
                  color = ?,
                  vehicle_type_id = ?
                  WHERE vehicle_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssissi', 
            $_POST['plate_number'],
            $_POST['make'],
            $_POST['model'],
            $_POST['year'],
            $_POST['color'],
            $_POST['vehicle_type_id'],
            $_POST['vehicle_id']
        );
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;

    case 'delete_vehicle':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['vehicle_id'];
        $query = "DELETE FROM vehicles_table WHERE vehicle_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
}