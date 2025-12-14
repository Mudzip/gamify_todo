<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';
$conn = getConnection();

$rewards = array(
    array('level' => 1, 'name' => 'Take a Break', 'duration' => '15 min', 'cost' => 5),
    array('level' => 2, 'name' => 'Read Books', 'duration' => '30 min', 'cost' => 5),
    array('level' => 3, 'name' => 'Read Books', 'duration' => '2 hours', 'cost' => 5),
    array('level' => 4, 'name' => 'Read Manga', 'duration' => '1 Chapter', 'cost' => 5),
    array('level' => 5, 'name' => 'Read Manga', 'duration' => '5 Chapter', 'cost' => 5),
    array('level' => 6, 'name' => 'Read Visual Novel', 'duration' => '1 Hour', 'cost' => 5),
    array('level' => 7, 'name' => 'Watch Movie', 'duration' => '1 Movie', 'cost' => 5),
    array('level' => 8, 'name' => 'Read Visual Novel', 'duration' => '3 Hours', 'cost' => 5),
    array('level' => 9, 'name' => 'Watch Anime', 'duration' => '1 Eps', 'cost' => 5),
    array('level' => 10, 'name' => 'Order Go-Food', 'duration' => '50K Max', 'cost' => 5),
    array('level' => 11, 'name' => 'Anime Binge', 'duration' => '3 Episodes', 'cost' => 5),
    array('level' => 12, 'name' => 'Anime Binge', 'duration' => '6 Episodes', 'cost' => 5),
    array('level' => 13, 'name' => 'Doomscroll', 'duration' => '15 Minutes', 'cost' => 5),
    array('level' => 14, 'name' => 'Doomscroll', 'duration' => '30 Minutes', 'cost' => 5),
    array('level' => 15, 'name' => 'Gaming Session', 'duration' => '1 Hour', 'cost' => 5),
    array('level' => 16, 'name' => 'Gaming Session', 'duration' => '3 Hours', 'cost' => 5),
    array('level' => 17, 'name' => 'Gaming Marathon', 'duration' => '6 Hours', 'cost' => 5),
    array('level' => 18, 'name' => 'Full Day Off', 'duration' => 'Do Whatever', 'cost' => 5),
    array('level' => 19, 'name' => 'Fancy Restaurant', 'duration' => 'Treat Yourself', 'cost' => 5),
    array('level' => 20, 'name' => 'Buy Anything', 'duration' => 'From Savings', 'cost' => 5)
);

// Total tasks
$sql = "SELECT COUNT(*) AS total_tasks FROM tasks WHERE is_completed = 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_tasks = $row['total_tasks'] ?? 0;

// Tokens spent
$sql_spent = "SELECT SUM(tokens_spent) AS spent FROM reward_claims";
$result_spent = $conn->query($sql_spent);
$row_spent = $result_spent->fetch_assoc();
$tokens_spent = $row_spent['spent'] ?? 0;

$level = floor($total_tasks / 5);
$available_tokens = $total_tasks - $tokens_spent;

// Claim history
$sql_claims = "SELECT reward_name, tokens_spent, claimed_at FROM reward_claims ORDER BY claimed_at DESC LIMIT 10";
$result_claims = $conn->query($sql_claims);
$claim_history = array();
while ($row_claim = $result_claims->fetch_assoc()) {
    $claim_history[] = $row_claim;
}

echo json_encode(array(
    'current_level' => $level,
    'available_tokens' => $available_tokens,
    'rewards' => $rewards,
    'claim_history' => $claim_history
));

$conn->close();