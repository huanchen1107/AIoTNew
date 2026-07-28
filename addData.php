<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$db_file = 'aiotdb.db';

try {
    // 1. Connect to SQLite database (will be created if it does not exist)
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create the sensors table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS sensors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        humidity REAL DEFAULT 0.0,
        temperature REAL DEFAULT 0.0,
        time DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Create update trigger to update time column on update
    $db->exec("CREATE TRIGGER IF NOT EXISTS update_sensor_time 
        AFTER UPDATE ON sensors 
        BEGIN
            UPDATE sensors SET time = CURRENT_TIMESTAMP WHERE id = new.id;
        END;");

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection/initialization failed: " . $e->getMessage()
    ]);
    exit();
}

// 4. Handle incoming request parameters (supporting POST and GET for testing)
$temperature = null;
$humidity = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if JSON body is received
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input !== null) {
        $temperature = isset($input['temperature']) ? floatval($input['temperature']) : null;
        $humidity = isset($input['humidity']) ? floatval($input['humidity']) : null;
    } else {
        $temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : null;
        $humidity = isset($_POST['humidity']) ? floatval($_POST['humidity']) : null;
    }
} else {
    // Fallback to GET parameters
    $temperature = isset($_GET['temperature']) ? floatval($_GET['temperature']) : null;
    $humidity = isset($_GET['humidity']) ? floatval($_GET['humidity']) : null;
}

if ($temperature === null || $humidity === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing parameters. Please provide 'temperature' and 'humidity'."
    ]);
    exit();
}

// 5. Insert data into sensors table
try {
    $stmt = $db->prepare("INSERT INTO sensors (temperature, humidity) VALUES (:temperature, :humidity)");
    $stmt->execute([
        ':temperature' => $temperature,
        ':humidity' => $humidity
    ]);
    
    $last_id = $db->lastInsertId();
    
    echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully",
        "data" => [
            "id" => intval($last_id),
            "temperature" => $temperature,
            "humidity" => $humidity,
            "time" => date("Y-m-d H:i:s")
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to insert data: " . $e->getMessage()
    ]);
}
?>
