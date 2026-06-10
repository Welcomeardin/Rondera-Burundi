<?php
require_once __DIR__ . '/../../db_connect.php';

$adId = isset($_GET['ad_id']) ? (int) $_GET['ad_id'] : 0;
$photoId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($adId <= 0 && $photoId <= 0) {
    http_response_code(404);
    exit();
}

try {
    if ($photoId > 0) {
        $stmt = $pdo->prepare('SELECT photo_path FROM ad_photos WHERE id = ? LIMIT 1');
        $stmt->execute([$photoId]);
    } else {
        $stmt = $pdo->prepare('
            SELECT photo_path FROM ad_photos
            WHERE ad_id = ?
            ORDER BY is_primary DESC, id ASC
            LIMIT 1
        ');
        $stmt->execute([$adId]);
    }

    $row = $stmt->fetch();
    if (!$row || empty($row['photo_path'])) {
        http_response_code(404);
        exit();
    }

    $path = $row['photo_path'];

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
        exit();
    }

    if (!file_exists($path) || !is_readable($path)) {
        http_response_code(404);
        exit();
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $types = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];

    header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
    header('Cache-Control: public, max-age=86400');
    readfile($path);
} catch (PDOException $e) {
    http_response_code(500);
}
