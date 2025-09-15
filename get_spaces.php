<?php
    include 'controller/connection.php';

    header('Content-Type: application/json');

    $result = $conn->query("SELECT id, name, slug, max_space, used_space FROM parking_spaces");

    $spaces = [];
    $totalAvailable = 0;
    $totalTaken = 0;
    $totalReserved = 0; // Placeholder until you add reservations

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $spaces[] = [
                "id" => (int) $row['id'],
                "name" => $row['name'],
                "slug" => $row['slug'],
                "max_space" => (int) $row['max_space'],
                "used_space" => (int) $row['used_space']
            ];

            // Calculate totals
            $available = (int) $row['max_space'] - (int) $row['used_space'];
            $totalAvailable += $available;
            $totalTaken += (int) $row['used_space'];
        }
    }

    echo json_encode([
        "spaces" => $spaces,
        "summary" => [
            "available" => $totalAvailable,
            "taken" => $totalTaken,
            "reserved" => $totalReserved
        ]
    ]);
    $conn->close();
?>