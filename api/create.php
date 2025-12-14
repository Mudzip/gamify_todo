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
    echo json_encode(array('success' => false, 'message' => 'Only POST allowed'));
    exit;
}

$title = $_POST['task-title'] ?? '';
$description = $_POST['task-description'] ?? '';

if (empty($title)) {
    echo json_encode(array('success' => false, 'message' => 'Title required'));
    exit;
}

require_once 'config.php';
$conn = getConnection();

$sql = "INSERT INTO tasks (title, description, is_completed, created_at) VALUES (?, ?, 0, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $title, $description);

if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'Task created!'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed: ' . $stmt->error));
}

$stmt->close();
$conn->close();