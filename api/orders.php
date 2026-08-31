<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

try {
    $pdo = getDb();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $orders = $pdo->query('SELECT * FROM orders ORDER BY id DESC')->fetchAll();
        foreach ($orders as &$order) {
            $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
            $stmt->execute([$order['id']]);
            $order['items'] = $stmt->fetchAll();
        }
        echo json_encode($orders);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON body']);
            exit;
        }

        $required = ['name', 'email', 'phone', 'address', 'city', 'postal', 'country', 'subtotal', 'shipping', 'total', 'items'];
        foreach ($required as $field) {
            if (!isset($body[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Missing field: $field"]);
                exit;
            }
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO orders (name, email, phone, address, city, postal, country, subtotal, shipping, total)
            VALUES (:name, :email, :phone, :address, :city, :postal, :country, :subtotal, :shipping, :total)
        ");
        $stmt->execute([
            ':name'     => $body['name'],
            ':email'    => $body['email'],
            ':phone'    => $body['phone'],
            ':address'  => $body['address'],
            ':city'     => $body['city'],
            ':postal'   => $body['postal'],
            ':country'  => $body['country'],
            ':subtotal' => $body['subtotal'],
            ':shipping' => $body['shipping'],
            ':total'    => $body['total'],
        ]);

        $orderId = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, price, qty)
            VALUES (:order_id, :product_id, :product_name, :price, :qty)
        ");
        foreach ($body['items'] as $item) {
            $itemStmt->execute([
                ':order_id'     => $orderId,
                ':product_id'   => $item['id'],
                ':product_name' => $item['name'],
                ':price'        => $item['price'],
                ':qty'          => $item['qty'],
            ]);
        }

        $pdo->commit();

        echo json_encode(['orderId' => (int) $orderId]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
