<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=marketplace_db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "--- CATEGORIES ---\n";
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($categories);
    
    echo "\n--- SUBCATEGORIES ---\n";
    $stmt = $pdo->query("SELECT * FROM subcategories");
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($subcategories);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
