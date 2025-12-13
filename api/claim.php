<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'error' => 'Only POST method allowed'));
    exit;
}

require_once 'config.php';
$conn = getConnection();

$data = json_decode(file_get_contents("php://input"));
$reward_level = $data->reward_level;

// Mendapatkan user level sekarang
$sql = "SELECT SUM(points) AS total_points FROM tasks WHERE is_completed = 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_points = $row['total_points'] ?? 0;
$current_level = floor($total_points / 100);

// Validate & INSERT
if ($current_level >= $reward_level) {
    $sql_insert = "INSERT INTO reward_claims (reward_level) VALUES (?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("i", $reward_level);
    $stmt->execute();
    $stmt->close();

    echo json_encode(array('success' => true, 'message' => 'Reward claimed!'));
} else {
    echo json_encode(array('success' => false, 'error' => 'Level tidak cukup'));
}

$conn->close();
