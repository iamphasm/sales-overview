<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

foreach (['title', 'date_added', 'investment_cost'] as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        http_response_code(400);
        echo json_encode(['error' => "Field '{$field}' is required"]);
        exit;
    }
}

$auction = add_auction([
    'link'            => $data['link'] ?? '',
    'production_year' => $data['production_year'] ?? '',
    'title'           => $data['title'],
    'category'        => $data['category'] ?? '',
    'date_added'      => $data['date_added'],
    'finish_date'     => $data['finish_date'],
    'investment_cost' => (float)$data['investment_cost'],
    'status'          => !empty($data['sold']) ? 'finished' : 'live',
]);

echo json_encode(['success' => true, 'auction' => $auction]);
