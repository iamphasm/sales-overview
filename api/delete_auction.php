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

$success = delete_auction($data['id']);
echo json_encode(['success' => $success]);
