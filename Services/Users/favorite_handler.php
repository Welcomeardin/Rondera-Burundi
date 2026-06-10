<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

$userId = (int) $_SESSION['user_id'];
$adId = (int) ($_POST['ad_id'] ?? $_GET['ad_id'] ?? 0);

if ($adId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid listing']);
    exit();
}

try {
    $isSaved = toggleFavorite($pdo, $userId, $adId);
    echo json_encode([
        'success' => true,
        'saved' => $isSaved,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not update favorite']);
}
