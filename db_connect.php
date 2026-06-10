<?php
// db_connect.php
// Centralized database connection file using PDO

// Detect if we are on local or production (InfinityFree)
$is_production = strpos($_SERVER['HTTP_HOST'], 'localhost') === false && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false;

if ($is_production) {
    // PRODUCTION SETTINGS (InfinityFree)
    // Update these with your actual details from InfinityFree Control Panel
    $host = 'sql208.infinityfree.com'; // Change XXX to your actual server number
    $db   = 'if0_42149677_ronderaburundi ';
    $user = 'if0_42149677_XXX';
    $pass = '6RwhH1sdDIg';
} else {
    // LOCAL SETTINGS
    $host = '127.0.0.1';
    $db   = 'marketplace_db';
    $user = 'root';
    $pass = '';
}
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log error instead of displaying raw message
    die("Database connection failed: " . $e->getMessage());
}
?>
