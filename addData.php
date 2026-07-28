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
        comfort_rule_status INTEGER DEFAULT NULL,
        time DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Check if comfort_rule_status column exists, if not, ALTER TABLE
    $q = $db->query("PRAGMA table_info(sensors)");
    $cols = $q->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('comfort_rule_status', $cols)) {
        $db->exec("ALTER TABLE sensors ADD COLUMN comfort_rule_status INTEGER DEFAULT NULL");
    }

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

$rule_active = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if JSON body is received
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input !== null) {
        $temperature = isset($input['temperature']) ? floatval($input['temperature']) : null;
        $humidity = isset($input['humidity']) ? floatval($input['humidity']) : null;
        $rule_active = !empty($input['rule_active']);
    } else {
        $temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : null;
        $humidity = isset($_POST['humidity']) ? floatval($_POST['humidity']) : null;
        $rule_active = isset($_POST['rule_active']) && ($_POST['rule_active'] === 'true' || $_POST['rule_active'] == 1);
    }
} else {
    // Fallback to GET parameters
    $temperature = isset($_GET['temperature']) ? floatval($_GET['temperature']) : null;
    $humidity = isset($_GET['humidity']) ? floatval($_GET['humidity']) : null;
    $rule_active = isset($_GET['rule_active']) && ($_GET['rule_active'] === 'true' || $_GET['rule_active'] == 1);
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
    $comfort_rule_status = null;
    if ($rule_active) {
        if ($temperature >= 10.0 && $temperature <= 40.0 && $humidity >= 20.0 && $humidity <= 50.0) {
            $comfort_rule_status = 1;
        } else {
            $comfort_rule_status = 0;
        }
    }

    $stmt = $db->prepare("INSERT INTO sensors (temperature, humidity, comfort_rule_status) VALUES (:temperature, :humidity, :comfort_rule_status)");
    $stmt->execute([
        ':temperature' => $temperature,
        ':humidity' => $humidity,
        ':comfort_rule_status' => $comfort_rule_status
    ]);
    
    $last_id = $db->lastInsertId();
    
    echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully",
        "data" => [
            "id" => intval($last_id),
            "temperature" => $temperature,
            "humidity" => $humidity,
            "comfort_rule_status" => $comfort_rule_status,
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
