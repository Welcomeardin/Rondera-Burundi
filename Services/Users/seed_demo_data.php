<?php
/**
 * Run once to seed demo sellers and listings for chat/orders.
 * Visit: /Services/Users/seed_demo_data.php
 */
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/demo_products.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

ensureMessagesTable($pdo);
ensureOrdersTable($pdo);

$categoryMap = [
    'Property Rentals' => 'property-rentals',
    'Property Sales' => 'property-sales',
    'Vehicles for Sale' => 'vehicles-sale',
    'Marketplace' => 'marketplace',
    'Electronics' => 'electronics',
];

$createdUsers = 0;
$createdAds = 0;

foreach (getDemoProducts() as $id => $product) {
    $email = $product['owner']['email'];

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $seller = $stmt->fetch();

    if (!$seller) {
        $password = password_hash('demo1234', PASSWORD_BCRYPT);
        $insert = $pdo->prepare('INSERT INTO users (full_name, email, phone, password_hash, role, status, verified) VALUES (?, ?, ?, ?, \'user\', \'active\', 1)');
        $insert->execute([$product['owner']['name'], $email, null, $password]);
        $sellerId = (int) $pdo->lastInsertId();
        $createdUsers++;
    } else {
        $sellerId = (int) $seller['id'];
    }

    $stmt = $pdo->prepare('SELECT id FROM ads WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $existingAd = $stmt->fetch();

    $category = $categoryMap[$product['category']] ?? 'marketplace';

    if (!$existingAd) {
        $insertAd = $pdo->prepare('INSERT INTO ads (id, user_id, category, title, description, price, location, status) VALUES (?, ?, ?, ?, ?, ?, ?, \'active\')');
        $insertAd->execute([
            $id,
            $sellerId,
            $category,
            $product['title'],
            $product['desc'],
            $product['price'],
            $product['location'],
        ]);
        $createdAds++;

        $photoStmt = $pdo->prepare('INSERT INTO ad_photos (ad_id, photo_path, is_primary) VALUES (?, ?, 1)');
        $photoStmt->execute([$id, $product['img']]);
    }
}

header('Content-Type: text/plain; charset=utf-8');
echo "Demo data ready.\n";
echo "Created users: {$createdUsers}\n";
echo "Created ads: {$createdAds}\n";
echo "Demo seller password (if newly created): demo1234\n";
