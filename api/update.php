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
// TODO: Day 3-4 - Implement UPDATE task endpoint
// Logic:
// 1. Get POST data (id, title, description, points, is_completed)
// 2. Validate input
// 3. UPDATE tasks WHERE id = ?
// 4. Return success/error JSON
echo json_encode(['messeage' =>'update endpoint - Coming in Day 3-4']);
$conn->close();
?>