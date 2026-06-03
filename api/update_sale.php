<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Auction ID is required']);
    exit;
}

$price = isset($data['final_price']) ? (float)$data['final_price'] : null;
$success = update_auction($data['id'], ['final_price' => $price]);

echo json_encode(['success' => $success]);
