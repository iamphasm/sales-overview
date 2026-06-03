<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/auth.php';

if (empty($_SESSION['logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$current  = $data['current_password']  ?? '';
$new      = $data['new_password']      ?? '';
$confirm  = $data['confirm_password']  ?? '';

$config = get_config();

if (!password_verify($current, $config['password_hash'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Current password is incorrect.']);
    exit;
}

if (strlen($new) < 4) {
    http_response_code(400);
    echo json_encode(['error' => 'New password must be at least 4 characters.']);
    exit;
}

if ($new !== $confirm) {
    http_response_code(400);
    echo json_encode(['error' => 'New passwords do not match.']);
    exit;
}

$config['password_hash'] = password_hash($new, PASSWORD_BCRYPT);
save_config($config);

echo json_encode(['success' => true]);
