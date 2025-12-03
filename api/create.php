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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'message' => 'Only POST method allowed'));
    exit;
}

$title = $_POST['task-title'];
$description = $_POST['task-description'];
$points = $_POST['task-points'];

if (empty($title) || empty($points)) {
    echo json_encode(array('success' => false, 'message' => 'Title and points required'));
    exit;
}

require_once 'config.php';
$conn = getConnection();

$sql = "INSERT INTO tasks (title, description, points, is_completed, created_at) VALUES (?, ?, ?, 0, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $title, $description, $points);

if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'Task created successfully'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed: ' . $stmt->error));
}

$stmt->close();
$conn->close();
?>