<?php
// controller/parking_manager.php
// Single-file CRUD for parking_spaces (add / edit / delete / list)
// Returns JSON responses.

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

header('Content-Type: application/json; charset=utf-8');

// Register shutdown handler to ensure JSON is returned on fatal error
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err !== null) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Fatal PHP error: ' . ($err['message'] ?? 'unknown')
        ]);
    }
});

// include db config (this file should create $conn as mysqli object)
require_once __DIR__ . '/config.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection not available.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$allowed_types = ['general', 'faculty', 'executive', 'guest'];

$uploadDirRel = '/../uploads/parking/';                 // relative path saved in DB (web-accessible)
$uploadDirAbs = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'parking' . DIRECTORY_SEPARATOR;

// helper: consistent JSON response + exit
function respond($success, $message, $data = [], $http_code = 200) {
    http_response_code($http_code);
    $payload = array_merge(['success' => $success, 'message' => $message], $data);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

// helper: sanitize filename (very minimal — we use uniqid anyway)
function sanitize_filename($name) {
    return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $name);
}

// handle file upload, returns relative path or null, throws Exception on error
function handle_upload($fileKey, $uploadDirAbs, $uploadDirRel) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fileKey];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error code: ' . $file['error']);
    }

    // Limit size to 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File too large. Max 5MB.');
    }

    // Detect mime type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowed_mimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($mime, $allowed_mimes)) {
        throw new Exception('Invalid image type. Allowed: jpg, png, gif, webp.');
    }

    // Ensure upload dir exists
    if (!is_dir($uploadDirAbs)) {
        if (!mkdir($uploadDirAbs, 0755, true) && !is_dir($uploadDirAbs)) {
            throw new Exception('Failed to create upload directory.');
        }
    }

    $ext = $allowed_mimes[$mime];
    $base = uniqid('parking_', true);
    $filename = sanitize_filename($base . '.' . $ext);
    $dest = rtrim($uploadDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new Exception('Failed to move uploaded file.');
    }

    // Return web-accessible relative path (no leading slash)
    return $uploadDirRel . $filename;
}

try {
    if ($action === 'add') {
        $name = trim((string)($_POST['name'] ?? ''));
        $max_space = (int)($_POST['max_space'] ?? 0);
        $space_type = (string)($_POST['space_type'] ?? '');

        if ($name === '' || $max_space <= 0) {
            respond(false, 'Name and maximum slots are required.', [], 400);
        }

        if (!in_array($space_type, $allowed_types, true)) {
            respond(false, 'Invalid space type.', [], 400);
        }

        // handle optional image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagePath = handle_upload('image', $uploadDirAbs, $uploadDirRel);
        }

        $sql = "INSERT INTO parking_spaces (name, max_space, space_type, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            respond(false, 'DB prepare failed: ' . $conn->error, [], 500);
        }

        if ($imagePath !== null) {
            $stmt->bind_param('siss', $name, $max_space, $space_type, $imagePath);
        } else {
            $stmt->bind_param('sis', $name, $max_space, $space_type);
            // adjust SQL to not expect image_path column - but easier: set image_path to NULL in query explicitly
            $stmt->close();
            $sql = "INSERT INTO parking_spaces (name, max_space, space_type, image_path) VALUES (?, ?, ?, NULL)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) respond(false, 'DB prepare failed: ' . $conn->error, [], 500);
            $stmt->bind_param('sis', $name, $max_space, $space_type);
        }

        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            $stmt->close();
            respond(true, 'Parking space added successfully.', ['id' => $newId]);
        } else {
            $err = $stmt->error;
            $stmt->close();
            respond(false, 'DB execute failed: ' . $err, [], 500);
        }

    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $max_space = (int)($_POST['max_space'] ?? 0);
        $space_type = (string)($_POST['space_type'] ?? '');

        if ($id <= 0 || $name === '' || $max_space <= 0) {
            respond(false, 'Invalid input for edit.', [], 400);
        }

        if (!in_array($space_type, $allowed_types, true)) {
            respond(false, 'Invalid space type.', [], 400);
        }

        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image_path = handle_upload('image', $uploadDirAbs, $uploadDirRel);
        }

        if ($image_path !== null) {
            $sql = "UPDATE parking_spaces SET name = ?, max_space = ?, space_type = ?, image_path = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) respond(false, 'DB prepare failed: ' . $conn->error, [], 500);
            $stmt->bind_param('sissi', $name, $max_space, $space_type, $image_path, $id);
        } else {
            $sql = "UPDATE parking_spaces SET name = ?, max_space = ?, space_type = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) respond(false, 'DB prepare failed: ' . $conn->error, [], 500);
            $stmt->bind_param('sisi', $name, $max_space, $space_type, $id);
        }

        if ($stmt->execute()) {
            $stmt->close();
            respond(true, 'Parking space updated successfully.');
        } else {
            $err = $stmt->error;
            $stmt->close();
            respond(false, 'DB execute failed: ' . $err, [], 500);
        }

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) respond(false, 'Invalid ID for delete.', [], 400);

        // get image_path so we can remove the file
        $sel = $conn->prepare("SELECT image_path FROM parking_spaces WHERE id = ?");
        if (!$sel) respond(false, 'DB prepare failed: ' . $conn->error, [], 500);
        $sel->bind_param('i', $id);
        $sel->execute();
        $res = $sel->get_result();
        $row = $res->fetch_assoc();
        $sel->close();

        if ($row && !empty($row['image_path'])) {
            $fileToDelete = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $row['image_path']);
            if ($fileToDelete && file_exists($fileToDelete)) {
                @unlink($fileToDelete);
            }
        }

        $stmt = $conn->prepare("DELETE FROM parking_spaces WHERE id = ?");
        if (!$stmt) respond(false, 'DB prepare failed: ' . $conn->error, [], 500);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $stmt->close();
            respond(true, 'Parking space deleted successfully.');
        } else {
            $err = $stmt->error;
            $stmt->close();
            respond(false, 'DB execute failed: ' . $err, [], 500);
        }

    } elseif ($action === 'list') {
        $result = $conn->query("SELECT id, name, max_space, space_type, image_path, created_at FROM parking_spaces ORDER BY id DESC");
        if (!$result) respond(false, 'DB query failed: ' . $conn->error, [], 500);
        $rows = [];
        while ($r = $result->fetch_assoc()) $rows[] = $r;
        respond(true, 'List retrieved.', ['spaces' => $rows]);

    } else {
        respond(false, 'No valid action provided.', [], 400);
    }

} catch (Exception $e) {
    respond(false, 'Exception: ' . $e->getMessage(), [], 500);
}
