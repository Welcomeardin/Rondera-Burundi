<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=marketplace_db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Clear existing
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE subcategories");
    $pdo->exec("TRUNCATE TABLE categories");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Categories to insert
    $categories = [
        ['name' => 'Property rentals', 'icon' => 'home'],
        ['name' => 'Property sales', 'icon' => 'home'],
        ['name' => 'Lands & Plots', 'icon' => 'map'],
        ['name' => 'Vehicles for Sale', 'icon' => 'truck'],
        ['name' => 'Vehicles for rent', 'icon' => 'key'],
    ];

    $subcategories = [
        'Property rentals' => ['Houses', 'Apartments', 'Rooms', 'Short stays', 'Commercial spaces'],
        'Property sales' => ['Houses', 'Villas', 'Apartments', 'Commercial buildings', 'Other'],
        'Lands & Plots' => ['Residential plots', 'Commercial plots', 'Agricultural land', 'Industrial', 'Mixed Use'],
        'Vehicles for Sale' => ['Cars', 'Motorcycles', 'Trucks', 'Bicycles', 'Other'],
        'Vehicles for rent' => ['Cars', 'Motorcycles', 'Trucks', 'Buses', 'Other'],
    ];

    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, icon, active) VALUES (?, ?, 1)");
        $stmt->execute([$cat['name'], $cat['icon']]);
        $catId = $pdo->lastInsertId();

        $subs = $subcategories[$cat['name']];
        foreach ($subs as $sub) {
            $stmtSub = $pdo->prepare("INSERT INTO subcategories (category_id, name) VALUES (?, ?)");
            $stmtSub->execute([$catId, $sub]);
        }
    }

    echo "Successfully seeded categories and subcategories in marketplace_db!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
