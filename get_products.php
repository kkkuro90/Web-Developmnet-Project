<?php
require_once 'config.php';

$stmt = $pdo->query("SELECT id, name, price, image FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($products as &$product) {
    if (!str_starts_with($product['image'], 'uploads/')) {
        $product['image'] = 'uploads/products/' . $product['image'];
    }
}

echo json_encode($products);
?>