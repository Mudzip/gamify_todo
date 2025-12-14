<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'error' => 'Only POST allowed'));
    exit;
}

require_once 'config.php';
$conn = getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->reward_name) || !isset($data->reward_cost)) {
    echo json_encode(array('success' => false, 'error' => 'Invalid request data'));
    exit;
}

$reward_name = $data->reward_name;
$reward_cost = (int)$data->reward_cost;

// Get available tokens
$sql = "SELECT COUNT(*) AS total_tasks FROM tasks WHERE is_completed = 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_tasks = $row['total_tasks'] ?? 0;

$sql_spent = "SELECT SUM(tokens_spent) AS spent FROM reward_claims";
$result_spent = $conn->query($sql_spent);
$row_spent = $result_spent->fetch_assoc();
$tokens_spent = $row_spent['spent'] ?? 0;

$available_tokens = $total_tasks - $tokens_spent;

// Check if enough tokens
if ($available_tokens >= $reward_cost) {
    $sql_insert = "INSERT INTO reward_claims (reward_name, tokens_spent) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("si", $reward_name, $reward_cost);
    $stmt->execute();
    $stmt->close();

    echo json_encode(array(
        'success' => true,
        'message' => 'Reward claimed!',
        'new_balance' => $available_tokens - $reward_cost
    ));
} else {
    echo json_encode(array(
        'success' => false,
        'error' => 'Not enough tokens! Need ' . $reward_cost . ', have ' . $available_tokens
    ));
}

$conn->close();