<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authantification/login.php");
    exit();
}

require_once __DIR__ . '/../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['photo_id']) && isset($_POST['ad_id'])) {
    $photo_id = (int)$_POST['photo_id'];
    $ad_id = (int)$_POST['ad_id'];
    
    try {
        // Verify the photo belongs to an ad owned by the user
        $stmt = $pdo->prepare('
            SELECT ap.photo_path 
            FROM ad_photos ap
            JOIN ads a ON ap.ad_id = a.id
            WHERE ap.id = ? AND a.user_id = ?
        ');
        $stmt->execute([$photo_id, $_SESSION['user_id']]);
        $photo = $stmt->fetch();
        
        if ($photo) {
            // Delete the photo file if it exists
            if (file_exists($photo['photo_path'])) {
                unlink($photo['photo_path']);
            }
            
            // Delete the photo record
            $stmt = $pdo->prepare('DELETE FROM ad_photos WHERE id = ?');
            $stmt->execute([$photo_id]);
        }
        
        header("Location: edit_ad.php?id=$ad_id");
        exit();
    } catch (\PDOException $e) {
        // On error, just redirect back
        header("Location: edit_ad.php?id=$ad_id&error=" . urlencode($e->getMessage()));
        exit();
    }
}

// If not POST, redirect to my ads
header("Location: myad.php");
exit();
