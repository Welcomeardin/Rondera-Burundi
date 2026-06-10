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
$orderId = (int) ($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? '';
$action = $_POST['action'] ?? 'update';

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid order ID']);
    exit();
}

try {
    if ($action === 'delete') {
        // Delete order only if user is seller or buyer
        $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ? AND (seller_id = ? OR buyer_id = ?)');
        $success = $stmt->execute([$orderId, $userId, $userId]);
        echo json_encode(['success' => $success]);
    } else {
        if (!in_array($status, ['confirmed', 'cancelled', 'completed'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status']);
            exit();
        }
        $success = updateOrderStatus($pdo, $orderId, $userId, $status);
        echo json_encode(['success' => $success]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
