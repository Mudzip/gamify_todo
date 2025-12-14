<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';
$conn = getConnection();

$sql = "SELECT * FROM tasks ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(array('error' => $conn->error));
    exit;
}

$tasks = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($tasks);

$conn->close();