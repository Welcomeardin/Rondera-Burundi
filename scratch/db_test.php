<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "--- DATABASES ---\n";
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($databases);
    
    if (in_array('marketplace_db', $databases)) {
        echo "\n--- marketplace_db TABLES ---\n";
        $pdo->query("USE marketplace_db");
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        print_r($tables);
        
        foreach ($tables as $table) {
            echo "\n--- DESCRIBE $table ---\n";
            $stmt = $pdo->query("DESCRIBE `$table`");
            print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    } else {
        echo "\nmarketplace_db not found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
