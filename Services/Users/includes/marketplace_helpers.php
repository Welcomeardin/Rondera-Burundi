<?php

function userInitials(?string $name): string
{
    if (empty($name)) {
        return 'US';
    }

    $parts = preg_split('/\s+/', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }

    return strtoupper(substr($parts[0], 0, 2));
}

function formatProductPrice($price): string
{
    if ($price === null || $price === '') {
        return 'Contact for price';
    }

    return number_format((float) $price, 0, ',', ' ') . ' BIF';
}

function resolveProductImage(?string $photoPath, ?int $adId = null, ?string $fallback = null): string
{
    if (!empty($photoPath) && (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://'))) {
        return $photoPath;
    }

    if ($adId) {
        return 'ad_photo.php?ad_id=' . $adId;
    }

    return $fallback ?: 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400&auto=format&fit=crop&q=80';
}

function formatCategoryLabel(?string $category): string
{
    return ucwords(str_replace('-', ' ', $category ?? ''));
}

function adToProductCard(array $ad): array
{
    $photo = $ad['primary_photo'] ?? $ad['first_photo'] ?? null;
    $status = $ad['status'] ?? 'pending';
    $badge = match ($status) {
        'active' => '🏷️ Active',
        'sold' => '🏷️ Sold',
        'pending' => '🏷️ Pending',
        default => '',
    };

    return [
        'id' => (int) $ad['id'],
        'title' => $ad['title'],
        'price' => $ad['price'],
        'price_display' => formatProductPrice($ad['price']),
        'total_price' => formatProductPrice($ad['price']),
        'location' => $ad['location'],
        'category' => formatCategoryLabel($ad['category']),
        'badge' => $badge,
        'img' => resolveProductImage($photo, (int) $ad['id']),
        'desc' => $ad['description'],
        'specs' => [
            'Category' => formatCategoryLabel($ad['category']),
            'Location' => $ad['location'],
            'Status' => ucfirst($status),
        ],
        'owner' => [
            'id' => (int) ($ad['seller_id'] ?? $ad['user_id']),
            'name' => $ad['seller_name'] ?? 'Seller',
            'since' => !empty($ad['seller_since'])
                ? 'Member since ' . date('Y', strtotime($ad['seller_since']))
                : 'Rondera member',
            'ads' => 'Seller on Rondera',
            'rating' => '4.8',
        ],
        'seller_id' => (int) ($ad['seller_id'] ?? $ad['user_id']),
        'subtitle' => formatCategoryLabel($ad['category']),
        'extra' => '✔️ Ready to Ship',
        'source' => 'database',
    ];
}

function fetchAdsQuery(PDO $pdo, string $where, int $limit, array $params = []): array
{
    $stmt = $pdo->prepare("
        SELECT a.*, u.id AS seller_id, u.full_name AS seller_name, u.created_at AS seller_since,
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) AS primary_photo,
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id LIMIT 1) AS first_photo
        FROM ads a
        JOIN users u ON u.id = a.user_id
        WHERE a.status NOT IN ('deleted') {$where}
        ORDER BY a.created_at DESC
        LIMIT ?
    ");

    $i = 1;
    foreach ($params as $param) {
        $stmt->bindValue($i++, $param);
    }
    $stmt->bindValue($i, $limit, PDO::PARAM_INT);
    $stmt->execute();

    $results = [];
    foreach ($stmt->fetchAll() as $ad) {
        $results[] = adToProductCard($ad);
    }

    return $results;
}

function getAdsFromDatabase(PDO $pdo, int $limit = 20): array
{
    return fetchAdsQuery($pdo, '', $limit);
}

function getAdsByCategory(PDO $pdo, string $category, int $limit = 20): array
{
    return fetchAdsQuery($pdo, 'AND a.category = ?', $limit, [$category]);
}

function getProductFromDatabase(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('
        SELECT a.*, u.id AS seller_id, u.full_name AS seller_name, u.created_at AS seller_since,
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) AS primary_photo,
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id LIMIT 1) AS first_photo
        FROM ads a
        JOIN users u ON u.id = a.user_id
        WHERE a.id = ? AND a.status NOT IN (\'deleted\')
        LIMIT 1
    ');
    $stmt->execute([$id]);
    $ad = $stmt->fetch();

    if (!$ad) {
        return null;
    }

    return adToProductCard($ad);
}

function getProductById(PDO $pdo, int $id): ?array
{
    return getProductFromDatabase($pdo, $id);
}

function getConversations(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("
        SELECT
            CASE WHEN m.sender_id = :uid THEN m.receiver_id ELSE m.sender_id END AS other_user_id,
            MAX(m.created_at) AS last_message_at,
            SUBSTRING_INDEX(
                GROUP_CONCAT(m.message ORDER BY m.created_at DESC SEPARATOR '||'),
                '||',
                1
            ) AS last_message,
            SUBSTRING_INDEX(
                GROUP_CONCAT(m.ad_id ORDER BY m.created_at DESC SEPARATOR ','),
                ',',
                1
            ) AS latest_ad_id,
            COUNT(DISTINCT m.ad_id) AS listing_count,
            SUM(CASE WHEN m.receiver_id = :uid2 AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count
        FROM marketplace_messages m
        WHERE m.sender_id = :uid3 OR m.receiver_id = :uid4
        GROUP BY other_user_id
        ORDER BY last_message_at DESC
    ");
    $stmt->execute([
        'uid' => $userId,
        'uid2' => $userId,
        'uid3' => $userId,
        'uid4' => $userId,
    ]);

    $results = [];
    foreach ($stmt->fetchAll() as $conversation) {
        $otherUserId = (int) $conversation['other_user_id'];
        $adId = (int) $conversation['latest_ad_id'];
        $listingCount = (int) $conversation['listing_count'];

        $userStmt = $pdo->prepare('SELECT id, full_name FROM users WHERE id = ? LIMIT 1');
        $userStmt->execute([$otherUserId]);
        $otherUser = $userStmt->fetch();

        $product = $adId > 0 ? getProductById($pdo, $adId) : null;
        $productTitle = $product['title'] ?? 'Listing';
        if ($listingCount > 1) {
            $productTitle .= ' · ' . $listingCount . ' listings';
        }

        $results[] = [
            'ad_id' => $adId,
            'other_user_id' => $otherUserId,
            'other_user_name' => $otherUser['full_name'] ?? 'User',
            'other_user_initials' => userInitials($otherUser['full_name'] ?? 'User'),
            'last_message' => $conversation['last_message'],
            'last_message_at' => $conversation['last_message_at'],
            'unread_count' => (int) $conversation['unread_count'],
            'listing_count' => $listingCount,
            'product_title' => $productTitle,
            'product_price' => $product['price_display'] ?? '',
            'product_image' => $product['img'] ?? '',
        ];
    }

    return $results;
}

function getUnreadConversationCount(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT
            CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END
        ) AS cnt
        FROM marketplace_messages
        WHERE receiver_id = ? AND is_read = 0
    ");
    $stmt->execute([$userId, $userId]);

    return (int) $stmt->fetchColumn();
}

function getThreadMessages(PDO $pdo, int $userId, int $otherUserId, ?int $adId = null): array
{
    $stmt = $pdo->prepare('
        SELECT m.*, u.full_name AS sender_name, a.title AS ad_title
        FROM marketplace_messages m
        JOIN users u ON u.id = m.sender_id
        LEFT JOIN ads a ON a.id = m.ad_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ');
    $stmt->execute([$userId, $otherUserId, $otherUserId, $userId]);
    $messages = $stmt->fetchAll();

    $markRead = $pdo->prepare('
        UPDATE marketplace_messages
        SET is_read = 1
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ');
    $markRead->execute([$otherUserId, $userId]);

    return $messages;
}

function getLatestAdIdInThread(PDO $pdo, int $userId, int $otherUserId): int
{
    $stmt = $pdo->prepare('
        SELECT ad_id FROM marketplace_messages
        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
          AND ad_id IS NOT NULL
        ORDER BY created_at DESC
        LIMIT 1
    ');
    $stmt->execute([$userId, $otherUserId, $otherUserId, $userId]);
    $row = $stmt->fetch();

    return $row ? (int) $row['ad_id'] : 0;
}

function getUserOrders(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('
        SELECT o.*, a.title AS ad_title, a.price AS ad_price,
               buyer.full_name AS buyer_name, seller.full_name AS seller_name,
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) AS primary_photo,
               (SELECT photo_path FROM ad_photos WHERE ad_id = a.id LIMIT 1) AS first_photo
        FROM orders o
        JOIN ads a ON a.id = o.ad_id
        JOIN users buyer ON buyer.id = o.buyer_id
        JOIN users seller ON seller.id = o.seller_id
        WHERE o.buyer_id = ? OR o.seller_id = ?
        ORDER BY o.created_at DESC
    ');
    $stmt->execute([$userId, $userId]);

    $orders = [];
    foreach ($stmt->fetchAll() as $row) {
        $photo = $row['primary_photo'] ?? $row['first_photo'] ?? null;
        $orders[] = [
            'id' => (int) $row['id'],
            'ad_id' => (int) $row['ad_id'],
            'buyer_id' => (int) $row['buyer_id'],
            'seller_id' => (int) $row['seller_id'],
            'amount' => $row['amount'],
            'amount_display' => formatProductPrice($row['amount']),
            'status' => $row['status'],
            'notes' => $row['notes'],
            'created_at' => $row['created_at'],
            'ad_title' => $row['ad_title'],
            'buyer_name' => $row['buyer_name'],
            'seller_name' => $row['seller_name'],
            'img' => resolveProductImage($photo, (int) $row['ad_id']),
            'is_buyer' => (int) $row['buyer_id'] === $userId,
            'is_seller' => (int) $row['seller_id'] === $userId,
            'other_user_id' => (int) $row['buyer_id'] === $userId ? (int) $row['seller_id'] : (int) $row['buyer_id'],
            'other_user_name' => (int) $row['buyer_id'] === $userId ? $row['seller_name'] : $row['buyer_name'],
        ];
    }

    return $orders;
}

function getPendingOrderNotificationCount(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare('
        SELECT COUNT(*) FROM orders
        WHERE seller_id = ? AND status = \'pending\'
    ');
    $stmt->execute([$userId]);

    return (int) $stmt->fetchColumn();
}

function updateOrderStatus(PDO $pdo, int $orderId, int $userId, string $newStatus): bool
{
    // Verify user is the seller for this order
    $stmt = $pdo->prepare('SELECT seller_id, buyer_id, ad_id FROM orders WHERE id = ?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order || (int) $order['seller_id'] !== $userId) {
        return false;
    }

    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    return $stmt->execute([$newStatus, $orderId]);
}

function favoriteTableColumns(PDO $pdo): array
{
    $rows = $pdo->query('SHOW COLUMNS FROM favorites')->fetchAll(PDO::FETCH_ASSOC);

    return array_column($rows, 'Field');
}

function createFavoritesTable(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS favorites (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            ad_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_favorite (user_id, ad_id),
            INDEX idx_user (user_id),
            INDEX idx_ad (ad_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

function ensureFavoritesTable(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }

    createFavoritesTable($pdo);

    $cols = favoriteTableColumns($pdo);

    // If table is empty, it's safer to just drop and recreate if schema is wrong
    if (!in_array('ad_id', $cols, true) || !in_array('user_id', $cols, true)) {
        try {
            $count = (int) $pdo->query('SELECT COUNT(*) FROM favorites')->fetchColumn();
            if ($count === 0) {
                $pdo->exec('DROP TABLE favorites');
                createFavoritesTable($pdo);
                $cols = favoriteTableColumns($pdo);
            }
        } catch (PDOException $e) {
            // Ignore and try migration
        }
    }

    if (!in_array('user_id', $cols, true)) {
        foreach (['utilisateur_id', 'userid', 'uid', 'customer_id'] as $candidate) {
            if (in_array($candidate, $cols, true)) {
                try {
                    $pdo->exec("ALTER TABLE favorites CHANGE `{$candidate}` user_id INT NOT NULL");
                } catch (PDOException $e) {
                    try {
                        $pdo->exec("ALTER TABLE favorites CHANGE `{$candidate}` user_id INT NOT NULL, ALGORITHM=INPLACE");
                    } catch (PDOException $e2) {
                        $pdo->exec("ALTER TABLE favorites CHANGE `{$candidate}` user_id INT NOT NULL, ALGORITHM=COPY");
                    }
                }
                break;
            }
        }
        $cols = favoriteTableColumns($pdo);
    }

    if (!in_array('ad_id', $cols, true)) {
        $renamed = false;
        foreach (['listing_id', 'product_id', 'annonce_id', 'maison_id', 'item_id'] as $candidate) {
            if (in_array($candidate, $cols, true)) {
                try {
                    $pdo->exec("ALTER TABLE favorites CHANGE `{$candidate}` ad_id INT NOT NULL");
                } catch (PDOException $e) {
                    try {
                        $pdo->exec("ALTER TABLE favorites CHANGE `{$candidate}` ad_id INT NOT NULL, ALGORITHM=INPLACE");
                    } catch (PDOException $e2) {
                        $pdo->exec("ALTER TABLE favorites CHANGE `{$candidate}` ad_id INT NOT NULL, ALGORITHM=COPY");
                    }
                }
                $renamed = true;
                break;
            }
        }

        if (!$renamed) {
            try {
                $pdo->exec('ALTER TABLE favorites ADD COLUMN ad_id INT NULL');
            } catch (PDOException) {
                try {
                    $pdo->exec('ALTER TABLE favorites ADD COLUMN ad_id INT NULL AFTER user_id');
                } catch (PDOException) {
                    // Handled by recreate fallback below.
                }
            }
        }

        $cols = favoriteTableColumns($pdo);
    }

    if (!in_array('ad_id', $cols, true)) {
        $count = (int) $pdo->query('SELECT COUNT(*) FROM favorites')->fetchColumn();
        if ($count === 0) {
            $pdo->exec('DROP TABLE favorites');
            createFavoritesTable($pdo);
            $cols = favoriteTableColumns($pdo);
        }
    }

    if (!in_array('created_at', $cols, true)) {
        if (in_array('date_ajout', $cols, true)) {
            try {
                $pdo->exec('ALTER TABLE favorites CHANGE date_ajout created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
            } catch (PDOException $e) {
                try {
                    $pdo->exec('ALTER TABLE favorites CHANGE date_ajout created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ALGORITHM=INPLACE');
                } catch (PDOException $e2) {
                    $pdo->exec('ALTER TABLE favorites CHANGE date_ajout created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ALGORITHM=COPY');
                }
            }
        } else {
            try {
                $pdo->exec('ALTER TABLE favorites ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
            } catch (PDOException) {
                // Optional column.
            }
        }
    }

    try {
        $pdo->exec('ALTER TABLE favorites ADD UNIQUE KEY unique_favorite (user_id, ad_id)');
    } catch (PDOException) {
        // Index may already exist.
    }

    $done = in_array('ad_id', favoriteTableColumns($pdo), true);
}

function favoritesTableReady(PDO $pdo): bool
{
    ensureFavoritesTable($pdo);

    return in_array('ad_id', favoriteTableColumns($pdo), true);
}

function getUserFavorites(PDO $pdo, int $userId): array
{
    if (!favoritesTableReady($pdo)) {
        return [];
    }

    $stmt = $pdo->prepare('
        SELECT f.created_at AS saved_at, a.*
        FROM favorites f
        JOIN ads a ON a.id = f.ad_id
        WHERE f.user_id = ? AND f.ad_id IS NOT NULL AND a.status NOT IN (\'deleted\')
        ORDER BY f.created_at DESC
    ');
    $stmt->execute([$userId]);

    $results = [];
    foreach ($stmt->fetchAll() as $ad) {
        $card = adToProductCard($ad);
        $card['saved_at'] = $ad['saved_at'];
        $results[] = $card;
    }

    return $results;
}

function isFavorite(PDO $pdo, int $userId, int $adId): bool
{
    if (!favoritesTableReady($pdo)) {
        return false;
    }

    $stmt = $pdo->prepare('SELECT 1 FROM favorites WHERE user_id = ? AND ad_id = ? LIMIT 1');
    $stmt->execute([$userId, $adId]);

    return (bool) $stmt->fetchColumn();
}

function toggleFavorite(PDO $pdo, int $userId, int $adId): bool
{
    if (!favoritesTableReady($pdo)) {
        throw new PDOException('Favorites table is not ready. Run migrate_favorites.php once.');
    }

    if (isFavorite($pdo, $userId, $adId)) {
        $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND ad_id = ?');
        $stmt->execute([$userId, $adId]);

        return false;
    }

    $stmt = $pdo->prepare('INSERT INTO favorites (user_id, ad_id) VALUES (?, ?)');
    $stmt->execute([$userId, $adId]);

    return true;
}

function sendMessage(PDO $pdo, int $senderId, int $receiverId, int $adId, string $message): int
{
    $stmt = $pdo->prepare('
        INSERT INTO marketplace_messages (sender_id, receiver_id, ad_id, message)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$senderId, $receiverId, $adId, $message]);

    return (int) $pdo->lastInsertId();
}

function formatMessageTime(string $datetime): string
{
    $time = strtotime($datetime);
    $today = strtotime('today');
    $yesterday = strtotime('yesterday');

    if ($time >= $today) {
        return date('g:i A', $time);
    }

    if ($time >= $yesterday) {
        return 'Yesterday';
    }

    if ($time >= strtotime('-6 days')) {
        return date('D', $time);
    }

    return date('M j', $time);
}

function loginRedirectUrl(string $returnPath): string
{
    return '../../Authantification/login.php?redirect=' . urlencode('Services/Users/' . ltrim($returnPath, '/'));
}
