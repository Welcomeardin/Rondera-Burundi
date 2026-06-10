<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

$query = trim($_GET['q'] ?? '');
$category = trim($_GET['cat'] ?? '');

$where = "AND (a.title LIKE ? OR a.description LIKE ? OR a.location LIKE ?)";
$params = ["%$query%", "%$query%", "%$query%"];

if ($category !== '') {
    $where .= " AND a.category = ?";
    $params[] = $category;
}

try {
    $results = fetchAdsQuery($pdo, $where, 12, $params);
    echo json_encode(['success' => true, 'results' => $results]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Search failed']);
}
