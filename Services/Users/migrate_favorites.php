<?php
/**
 * One-time migration for the favorites table.
 * Visit: /Services/Users/migrate_favorites.php
 */
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/includes/marketplace_helpers.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    ensureFavoritesTable($pdo);
    $cols = favoriteTableColumns($pdo);

    echo "Favorites table columns:\n";
    foreach ($cols as $col) {
        echo " - {$col}\n";
    }

    if (!in_array('ad_id', $cols, true)) {
        echo "\nERROR: ad_id column is still missing.\n";
        echo "Drop the old favorites table manually if it is empty, then reload this page.\n";
        exit(1);
    }

    echo "\nMigration OK. Favorites are ready to use.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Migration failed: ' . $e->getMessage() . "\n";
    exit(1);
}
