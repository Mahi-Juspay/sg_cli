<?php
require_once __DIR__ . '/db.php';

$pdo = getDb();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS products (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        description TEXT,
        emoji TEXT
    );

    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        address TEXT NOT NULL,
        city TEXT NOT NULL,
        postal TEXT NOT NULL,
        country TEXT NOT NULL,
        subtotal REAL NOT NULL,
        shipping REAL NOT NULL,
        total REAL NOT NULL,
        status TEXT NOT NULL DEFAULT 'pending',
        created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL REFERENCES orders(id),
        product_id TEXT NOT NULL,
        product_name TEXT NOT NULL,
        price REAL NOT NULL,
        qty INTEGER NOT NULL
    );
");

$products = [
    ['mug-01',      'Ceramic Coffee Mug',  14.00, 'Handmade stoneware mug, 350ml. Microwave and dishwasher safe.', '☕'],
    ['tote-01',     'Canvas Tote Bag',     22.50, 'Heavyweight organic cotton tote with reinforced straps.',        '👜'],
    ['notebook-01', 'Dotted Notebook',      9.75, 'A5 dotted notebook, 160 pages of 100gsm paper.',                '📓'],
    ['plant-01',    'Mini Potted Plant',   18.00, 'A small snake plant in a ceramic pot. Low maintenance.',         '🪴'],
    ['candle-01',   'Soy Wax Candle',      16.50, 'Lavender-scented hand-poured candle, 40h burn time.',           '🕯️'],
    ['bottle-01',   'Insulated Bottle',    28.00, 'Stainless steel, 500ml. Keeps drinks cold for 24h.',            '🍼'],
];

$stmt = $pdo->prepare("
    INSERT OR IGNORE INTO products (id, name, price, description, emoji)
    VALUES (?, ?, ?, ?, ?)
");

foreach ($products as $p) {
    $stmt->execute($p);
}

echo "Done — tables created and products seeded.\n";
