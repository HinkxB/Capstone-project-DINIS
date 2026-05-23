<?php
// 1. Get the raw JSON payload sent by the FireFly webhook
$jsonPayload = file_get_contents('php://input');
$eventData = json_decode($jsonPayload, true);

// 2. Validate that we received the expected data
if (!isset($eventData['personId']) || !isset($eventData['recordHash'])) {
    http_response_code(400);
    die("Invalid payload");
}

$personId = $eventData['personId'];
$recordHash = $eventData['recordHash'];

// 3. Database connection configuration
$host = '127.0.0.1';
$db   = 'your_database_name';
$user = 'your_db_user';
$pass = 'your_db_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Connect to MariaDB
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 4. PREPARE THE SQL STATEMENT (Here is where the '?' placeholders shine!)
    $sql = "UPDATE citizen_nrc_record 
            SET anchor_status = 'ANCHORED', updated_at = NOW() 
            WHERE person_id = ? AND record_hash = ?";
    
    $stmt = $pdo->prepare($sql);
    
    // 5. Execute the query, passing the variables securely into the placeholders
    $stmt->execute([$personId, $recordHash]);

    // Check if the row was actually updated
    if ($stmt->rowCount() > 0) {
        // Success! Send a 200 OK back to FireFly so it knows the webhook succeeded
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Record anchored successfully"]);
    } else {
        // Record not found or already anchored
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Record not found or already anchored"]);
    }

} catch (\PDOException $e) {
    // Log the error and tell FireFly something went wrong (FireFly can retry if configured)
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error"]);
}
