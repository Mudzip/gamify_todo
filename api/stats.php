<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';
$conn = getConnection();

// Total tasks completed (for level)
$sql = "SELECT COUNT(*) AS total_tasks FROM tasks WHERE is_completed = 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_tasks = $row['total_tasks'] ?? 0;

// Total tokens spent
$sql_spent = "SELECT SUM(tokens_spent) AS spent FROM reward_claims";
$result_spent = $conn->query($sql_spent);
$row_spent = $result_spent->fetch_assoc();
$tokens_spent = $row_spent['spent'] ?? 0;

// Calculate
$level = floor($total_tasks / 5);
$available_tokens = $total_tasks - $tokens_spent;

echo json_encode(array(
    'total_tasks' => $total_tasks,
    'level' => $level,
    'available_tokens' => $available_tokens,
    'tokens_spent' => $tokens_spent
));

$conn->close();