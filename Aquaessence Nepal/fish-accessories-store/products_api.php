<?php
header('Content-Type: application/json');

include 'config.php';

$response = [
    'success' => false,
    'products' => [],
    'message' => 'Unable to fetch products.'
];

if (!isset($conn) || $conn->connect_error) {
    echo json_encode($response);
    exit();
}

$sql = "SELECT id, name, category, price, stock, image_url FROM products ORDER BY id DESC LIMIT 24";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $response['products'][] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'price' => (float)$row['price'],
            'stock' => (int)$row['stock'],
            'image_url' => !empty($row['image_url']) ? $row['image_url'] : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=320&fit=crop'
        ];
    }

    $response['success'] = true;
    $response['message'] = 'Products loaded.';
} else {
    $response['message'] = 'Database query failed: ' . $conn->error;
}

echo json_encode($response);
