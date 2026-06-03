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
if (!is_array($data) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Auction ID is required']);
    exit;
}

$allowed = ['link', 'brand', 'model', 'production_year', 'title', 'category', 'date_added', 'finish_date', 'investment_cost'];
$updates = [];
foreach ($allowed as $field) {
    if (array_key_exists($field, $data)) {
        $updates[$field] = $field === 'investment_cost' ? (float)$data[$field] : $data[$field];
    }
}

// Only allow http/https links
if (isset($updates['link']) && $updates['link'] !== '') {
    $link_scheme = strtolower(parse_url($updates['link'], PHP_URL_SCHEME) ?? '');
    if (!in_array($link_scheme, ['http', 'https'], true)) {
        $updates['link'] = '';
    }
}

if (!empty($data['sold'])) {
    $updates['status'] = 'finished';
}

$success = update_auction($data['id'], $updates);
echo json_encode(['success' => $success]);
