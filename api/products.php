<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

try {
    $pdo = getDb();
    $rows = $pdo->query('SELECT id, name, price, description, emoji FROM products ORDER BY rowid')
                ->fetchAll();
    echo json_encode($rows);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
