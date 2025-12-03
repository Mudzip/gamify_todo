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
// TODO: Day 4 - Implement DELETE task endpoint
// Logic:
// 1. Get POST data (id)
// 2. Validate input
// 3. DELETE FROM tasks WHERE id = ?
// 4. Return success/error JSON
echo json_encode(['messege' =>'update endpoint - Coming in Day 4']);
$conn->close();
?>