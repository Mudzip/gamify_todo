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

$id = $_POST['task-id'];


if (empty($id)) {
    echo json_encode(array('success' => false, 'message' => 'Task ID required'));
    exit;
}

require_once 'config.php';
$conn = getConnection();

$sql = "DELETE FROM tasks WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'Task deleted successfully'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed: ' . $stmt->error));
}

$stmt->close();
$conn->close();
?>