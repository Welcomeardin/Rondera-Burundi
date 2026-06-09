<?php
// API/index.php
// REST API Endpoint Router for Rondera Inzu

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../db_connect.php';

$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'user':
        // Return 401 if user session is not active
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized. Please log in first.']);
            exit();
        }
        
        try {
            $stmt = $pdo->prepare('SELECT id, full_name, email, phone, role, status, created_at FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found.']);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;
        
    case 'houses':
        try {
            // Fetch property rentals
            $stmt = $pdo->query('SELECT l.*, pd.property_type, pd.bedrooms, pd.bathrooms, pd.building_size FROM listings l JOIN property_details pd ON l.id = pd.listing_id WHERE l.status = \'approved\' ORDER BY l.created_at DESC LIMIT 20');
            $houses = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'count' => count($houses), 'houses' => $houses]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint not found',
            'available_endpoints' => [
                'GET /API/index.php?endpoint=user' => 'Get current authenticated user info (requires active session)',
                'GET /API/index.php?endpoint=houses' => 'List all available rental properties'
            ]
        ]);
        break;
}
?>
