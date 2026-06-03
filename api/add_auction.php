<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_auth();

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

// Only allow http/https links
$link = $data['link'] ?? '';
$link_scheme = strtolower(parse_url($link, PHP_URL_SCHEME) ?? '');
if ($link !== '' && !in_array($link_scheme, ['http', 'https'], true)) {
    $link = '';
}

$auction = add_auction([
    'link'            => $link,
    'production_year' => $data['production_year'] ?? '',
    'title'           => $data['title'],
    'category'        => $data['category'] ?? '',
    'date_added'      => $data['date_added'],
    'finish_date'     => $data['finish_date'] ?? '',
    'investment_cost' => (float)$data['investment_cost'],
    'status'          => !empty($data['sold']) ? 'finished' : 'live',
]);

echo json_encode(['success' => true, 'auction' => $auction]);
