<?php
// controller/parking_manager.php
require_once "../models/Database.php";
header("Content-Type: application/json");

$response = ["success" => false, "message" => "Unknown error occurred."];

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    $db = new Database();
    $conn = $db->getConnection();

    $action = $_POST["action"] ?? "";

    switch ($action) {

        /* =============================
           ADD PARKING SPACE
        ============================== */
        case "add":
            $name = trim($_POST["name"] ?? "");
            $supports_car = isset($_POST["supports_car"]) ? 1 : 0;
            $supports_motorcycle = isset($_POST["supports_motorcycle"]) ? 1 : 0;

            $allocations_json = $_POST["allocations"] ?? "[]";
            $allocations = json_decode($allocations_json, true);

            if ($name === "") throw new Exception("Please provide a valid parking name.");
            if (!$supports_car && !$supports_motorcycle) throw new Exception("Please select at least one vehicle type.");
            if (empty($allocations)) throw new Exception("Please specify slot allocations for selected vehicle types.");

            // Check if parking already exists
            $check = $conn->prepare("SELECT COUNT(*) FROM parking_spaces WHERE name = ?");
            $check->bind_param("s", $name);
            $check->execute();
            $check->bind_result($count);
            $check->fetch();
            $check->close();

            if ($count > 0) throw new Exception("Parking space '$name' already exists.");

            // Image upload handling
            $image_path = null;
            if (!empty($_FILES["image"]["name"])) {
                $uploadDir = "../uploads/parking/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = "parking_" . uniqid() . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $uploadPath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath)) {
                    throw new Exception("Failed to upload image.");
                }
                $image_path = "/../uploads/parking/" . $fileName;
            }

            // Compute slot totals
            $max_car_slots = 0;
            $max_motorcycle_slots = 0;
            foreach ($allocations as $a) {
                if ($a['vehicle_type_id'] == 1) $max_car_slots += intval($a['allocated_slots']);
                if ($a['vehicle_type_id'] == 2) $max_motorcycle_slots += intval($a['allocated_slots']);
            }

            // Determine access per user type
            $is_general = $is_faculty = $is_executive = $is_guest = 0;
            foreach ($allocations as $a) {
                if ($a['allocated_slots'] > 0) {
                    switch ($a['user_type_id']) {
                        case 1: $is_general = 1; break;
                        case 2: $is_faculty = 1; break;
                        case 3: $is_executive = 1; break;
                        case 4: $is_guest = 1; break;
                    }
                }
            }

            // Begin transaction
            $conn->begin_transaction();

            try {
                // Insert parking space
                $stmt = $conn->prepare("
                    INSERT INTO parking_spaces (
                        name, supports_car, supports_motorcycle,
                        is_general, is_faculty, is_executive, is_guest,
                        image_path, max_car_slots, max_motorcycle_slots,
                        parked_car_count, parked_motorcycle_count
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)
                ");

                $stmt->bind_param(
                    "siiiiissii",
                    $name, $supports_car, $supports_motorcycle,
                    $is_general, $is_faculty, $is_executive, $is_guest,
                    $image_path, $max_car_slots, $max_motorcycle_slots
                );

                if (!$stmt->execute()) throw new Exception("Insert failed: " . $stmt->error);
                $parking_space_id = $conn->insert_id;
                $stmt->close();

                // Insert allocations
                $allocStmt = $conn->prepare("
                    INSERT INTO parking_slot_allocations 
                    (parking_space_id, vehicle_type_id, user_type_id, allocated_slots)
                    VALUES (?, ?, ?, ?)
                ");

                foreach ($allocations as $a) {
                    $allocStmt->bind_param("iiii", $parking_space_id, $a['vehicle_type_id'], $a['user_type_id'], $a['allocated_slots']);
                    if (!$allocStmt->execute()) throw new Exception("Error inserting allocation: " . $allocStmt->error);
                }

                $allocStmt->close();
                $conn->commit();

                $response = ["success" => true, "message" => "Parking space added successfully."];

            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
            break;

        /* =============================
           EDIT PARKING SPACE
        ============================== */
        case "edit":
            $id = intval($_POST["id"] ?? 0);
            $name = trim($_POST["name"] ?? "");
            $supports_car = isset($_POST["supports_car"]) ? 1 : 0;
            $supports_motorcycle = isset($_POST["supports_motorcycle"]) ? 1 : 0;

            $allocations_json = $_POST["allocations"] ?? "[]";
            $allocations = json_decode($allocations_json, true);

            if ($id <= 0) throw new Exception("Invalid parking space ID.");
            if ($name === "") throw new Exception("Please provide a valid parking name.");
            if (!$supports_car && !$supports_motorcycle) throw new Exception("Please select at least one vehicle type.");
            if (empty($allocations)) throw new Exception("Please specify slot allocations for selected vehicle types.");

            // Name conflict check
            $check = $conn->prepare("SELECT COUNT(*) FROM parking_spaces WHERE name = ? AND id != ?");
            $check->bind_param("si", $name, $id);
            $check->execute();
            $check->bind_result($count);
            $check->fetch();
            $check->close();
            if ($count > 0) throw new Exception("Another parking space with the same name already exists.");

            // Optional image update
            $image_path = null;
            if (!empty($_FILES["image"]["name"])) {
                $uploadDir = "../uploads/parking/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = "parking_" . uniqid() . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $uploadPath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath)) {
                    throw new Exception("Failed to upload new image.");
                }
                $image_path = "/../uploads/parking/" . $fileName;
            }

            // Slot calculations
            $max_car_slots = 0;
            $max_motorcycle_slots = 0;
            foreach ($allocations as $a) {
                if ($a['vehicle_type_id'] == 1) $max_car_slots += intval($a['allocated_slots']);
                if ($a['vehicle_type_id'] == 2) $max_motorcycle_slots += intval($a['allocated_slots']);
            }

            // User type access
            $is_general = $is_faculty = $is_executive = $is_guest = 0;
            foreach ($allocations as $a) {
                if ($a['allocated_slots'] > 0) {
                    switch ($a['user_type_id']) {
                        case 1: $is_general = 1; break;
                        case 2: $is_faculty = 1; break;
                        case 3: $is_executive = 1; break;
                        case 4: $is_guest = 1; break;
                    }
                }
            }

            // Begin transaction
            $conn->begin_transaction();

            try {
                if ($image_path !== null) {
                    $stmt = $conn->prepare("
                        UPDATE parking_spaces SET 
                            name=?, supports_car=?, supports_motorcycle=?,
                            is_general=?, is_faculty=?, is_executive=?, is_guest=?,
                            image_path=?, max_car_slots=?, max_motorcycle_slots=?
                        WHERE id=?
                    ");
                    $stmt->bind_param("siiiiissiii", $name, $supports_car, $supports_motorcycle,
                        $is_general, $is_faculty, $is_executive, $is_guest,
                        $image_path, $max_car_slots, $max_motorcycle_slots, $id);
                } else {
                    $stmt = $conn->prepare("
                        UPDATE parking_spaces SET 
                            name=?, supports_car=?, supports_motorcycle=?,
                            is_general=?, is_faculty=?, is_executive=?, is_guest=?,
                            max_car_slots=?, max_motorcycle_slots=?
                        WHERE id=?
                    ");
                    $stmt->bind_param("siiiiiiiii", $name, $supports_car, $supports_motorcycle,
                        $is_general, $is_faculty, $is_executive, $is_guest,
                        $max_car_slots, $max_motorcycle_slots, $id);
                }

                if (!$stmt->execute()) throw new Exception("Error updating: " . $stmt->error);
                $stmt->close();

                // Delete old allocations
                $delete = $conn->prepare("DELETE FROM parking_slot_allocations WHERE parking_space_id = ?");
                $delete->bind_param("i", $id);
                $delete->execute();
                $delete->close();

                // Insert new allocations
                $allocStmt = $conn->prepare("
                    INSERT INTO parking_slot_allocations (parking_space_id, vehicle_type_id, user_type_id, allocated_slots)
                    VALUES (?, ?, ?, ?)
                ");
                foreach ($allocations as $a) {
                    $allocStmt->bind_param("iiii", $id, $a['vehicle_type_id'], $a['user_type_id'], $a['allocated_slots']);
                    if (!$allocStmt->execute()) throw new Exception("Allocation insert failed: " . $allocStmt->error);
                }
                $allocStmt->close();

                $conn->commit();
                $response = ["success" => true, "message" => "Parking space updated successfully."];

            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
            break;

        /* =============================
           DELETE PARKING SPACE
        ============================== */
        case "delete":
            $id = intval($_POST["id"] ?? 0);
            if ($id <= 0) throw new Exception("Invalid parking space ID.");

            $conn->begin_transaction();

            try {
                $deleteAlloc = $conn->prepare("DELETE FROM parking_slot_allocations WHERE parking_space_id = ?");
                $deleteAlloc->bind_param("i", $id);
                $deleteAlloc->execute();
                $deleteAlloc->close();

                $deleteSpace = $conn->prepare("DELETE FROM parking_spaces WHERE id = ?");
                $deleteSpace->bind_param("i", $id);
                $deleteSpace->execute();
                $deleteSpace->close();

                $conn->commit();
                $response = ["success" => true, "message" => "Parking space deleted successfully."];

            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
            break;

        /* =============================
           INVALID ACTION
        ============================== */
        default:
            throw new Exception("Invalid or missing action parameter.");
    }

    $db->closeConnection();

} catch (Exception $e) {
    $response = ["success" => false, "message" => $e->getMessage()];
}

echo json_encode($response);
?>
