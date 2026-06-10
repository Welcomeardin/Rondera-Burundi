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
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'conversations':
            echo json_encode([
                'success' => true,
                'conversations' => getConversations($pdo, $userId),
                'unread_conversations' => getUnreadConversationCount($pdo, $userId),
            ]);
            break;

        case 'messages':
            $otherUserId = (int) ($_GET['user_id'] ?? 0);
            $adId = (int) ($_GET['ad_id'] ?? 0);

            if ($otherUserId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing conversation parameters']);
                break;
            }

            if ($adId <= 0) {
                $adId = getLatestAdIdInThread($pdo, $userId, $otherUserId);
            }

            $product = $adId > 0 ? getProductById($pdo, $adId) : null;
            $userStmt = $pdo->prepare('SELECT id, full_name FROM users WHERE id = ? LIMIT 1');
            $userStmt->execute([$otherUserId]);
            $otherUser = $userStmt->fetch();

            echo json_encode([
                'success' => true,
                'messages' => getThreadMessages($pdo, $userId, $otherUserId),
                'ad_id' => $adId,
                'product' => $product,
                'other_user' => [
                    'id' => $otherUserId,
                    'name' => $otherUser['full_name'] ?? 'User',
                    'initials' => userInitials($otherUser['full_name'] ?? 'User'),
                ],
            ]);
            break;

        case 'send':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
            }

            $receiverId = (int) ($_POST['receiver_id'] ?? 0);
            $adId = (int) ($_POST['ad_id'] ?? 0);
            $message = trim($_POST['message'] ?? '');

            if ($receiverId <= 0 || $message === '') {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid message data']);
                break;
            }

            if ($adId <= 0) {
                $adId = getLatestAdIdInThread($pdo, $userId, $receiverId);
            }

            if ($adId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'No listing context for this conversation']);
                break;
            }

            if ($receiverId === $userId) {
                http_response_code(400);
                echo json_encode(['error' => 'You cannot message yourself']);
                break;
            }

            $messageId = sendMessage($pdo, $userId, $receiverId, $adId, $message);

            echo json_encode([
                'success' => true,
                'message' => [
                    'id' => $messageId,
                    'sender_id' => $userId,
                    'receiver_id' => $receiverId,
                    'ad_id' => $adId,
                    'message' => $message,
                    'created_at' => date('Y-m-d H:i:s'),
                    'sender_name' => trim(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? '')) ?: 'Me',
                ],
            ]);
            break;

        case 'unread_count':
            echo json_encode([
                'success' => true,
                'count' => getUnreadConversationCount($pdo, $userId),
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
