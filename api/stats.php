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

$sql = "SELECT SUM(points) AS total_points from TASKS WHERE is_completed = 1";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(array('error' => $conn->error));
    exit;
}

$row = $result->fetch_assoc();
$total_points = $row['total_points'] ?? 0;
$level = floor($total_points / 100);

echo json_encode(array(
    'total_points' => $total_points,
    'level' => $level,
));

$conn->close();