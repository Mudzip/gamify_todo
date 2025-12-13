<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';
$conn = getConnection();

$rewards = array (
    array('level' => 1, 'name' => 'Take a Break', 'duration' => '15 min'),
    array('level' => 2, 'name' => 'Read Books', 'duration' => '30 min'),
    array('level' => 3, 'name' => 'Read Books', 'duration' => '2 hours'),
    array('level' => 4, 'name' => 'Read Manga', 'duration' => '1 Chapter'),
    array('level' => 5, 'name' => 'Read Manga', 'duration' => '5 Chapter'),
    array('level' => 6, 'name' => 'Read Visual Novel', 'duration' => '1 Hour'),
    array('level' => 7, 'name' => 'Watch Movie', 'duration' => '1 Movie'),
    array('level' => 8, 'name' => 'Read Visual Novel', 'duration' => '3 Hours'),
    array('level' => 9, 'name' => 'Watch Anime', 'duration' => '1 Eps'),
    array('level' => 10, 'name' => 'Order Go-Food For Treat Yourself', 'duration' => '50K Price Maximum'),
    array('level' => 11, 'name' => 'Anime Binge', 'duration' => '3 Episodes'),
    array('level' => 12, 'name' => 'Anime Binge', 'duration' => '6 Episodes'),
    array('level' => 13, 'name' => 'Doomscroll', 'duration' => '15 Minutes'),
    array('level' => 14, 'name' => 'Doomscroll', 'duration' => '30 Minutes'),
    array('level' => 15, 'name' => 'Gaming Session', 'duration' => '1 Hours'),
    array('level' => 16, 'name' => 'Gaming Session', 'duration' => '3 Hours'),
    array('level' => 17, 'name' => 'Gaming Marathon', 'duration' => '6 Hours / Give Up'),
    array('level' => 18, 'name' => 'Full Day Off', 'duration' => 'Do Whatever'),
    array('level' => 19, 'name' => 'Fancy Restaurant', 'duration' => 'Treat Yourself'),
    array('level' => 20, 'name' => 'Buy Anything', 'duration' => 'From Savings')
);

$sql = "SELECT SUM(points) AS total_points from TASKS WHERE is_completed = 1";
$result = $conn->query($sql);

$row = $result->fetch_assoc();
$total_points = $row['total_points'] ?? 0;
$level = floor ($total_points /100);

$sql_claims = "SELECT reward_level, claimed_at FROM reward_claims ORDER BY claimed_at DESC";
$result_claims = $conn->query($sql_claims);

$claim_history = array();
while ($row_claim = $result_claims->fetch_assoc()) {
    $claim_history[] = $row_claim;
}

echo json_encode(array(
     'current_level' => $level,
     'rewards' => $rewards,
     'claim_history' => $claim_history
));

$conn->close();



