<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$db_file = 'aiotdb.db';

try {
    // 1. Connect to SQLite database
    if (!file_exists($db_file)) {
        // If DB doesn't exist, return empty data
        echo json_encode([]);
        exit();
    }
    
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if table exists
    $table_check = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='sensors'");
    if (!$table_check->fetch()) {
        echo json_encode([]);
        exit();
    }

    // 2. Fetch the latest 100 readings, ordered by ID ascending (chronological order)
    // We subquery to get the latest 100, then order them chronologically.
    $stmt = $db->query("SELECT * FROM (SELECT id, temperature, humidity, time FROM sensors ORDER BY id DESC LIMIT 100) ORDER BY id ASC");
    $readings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert numeric fields to appropriate types
    foreach ($readings as &$reading) {
        $reading['id'] = intval($reading['id']);
        $reading['temperature'] = floatval($reading['temperature']);
        $reading['humidity'] = floatval($reading['humidity']);
    }

    echo json_encode($readings);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database query failed: " . $e->getMessage()
    ]);
}
?>
