<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/auth.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$url = trim($data['url'] ?? '');

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid URL']);
    exit;
}

// Reject userinfo (http://user:pass@host)
$parsed = parse_url($url);
if (!empty($parsed['user']) || !empty($parsed['pass'])) {
    http_response_code(400);
    echo json_encode(['error' => 'URL not allowed']);
    exit;
}

$scheme = strtolower($parsed['scheme'] ?? '');
if (!in_array($scheme, ['http', 'https'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Only http/https URLs are allowed']);
    exit;
}

// SSRF protection — resolve host and block all private/reserved ranges
$host = $parsed['host'];
$ip = gethostbyname($host);
if ($ip === $host || !filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['error' => 'URL not allowed']);
    exit;
}

if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    http_response_code(400);
    echo json_encode(['error' => 'URL not allowed']);
    exit;
}

// Additional blocks: link-local, CGNAT, multicast, loopback
if (
    preg_match('/^169\.254\./', $ip) ||
    preg_match('/^100\.(6[4-9]|[7-9]\d|1[01]\d|12[0-7])\./', $ip) ||
    preg_match('/^2[2-3]\d\./', $ip) ||
    $ip === '0.0.0.0'
) {
    http_response_code(400);
    echo json_encode(['error' => 'URL not allowed']);
    exit;
}

// No redirects — prevents DNS rebinding via redirect chains
$ctx = stream_context_create([
    'http' => [
        'method'          => 'GET',
        'header'          => "User-Agent: Mozilla/5.0 (compatible; SalesBot/1.0)\r\n",
        'timeout'         => 10,
        'follow_location' => 0,
    ],
    'ssl' => [
        'verify_peer'      => true,
        'verify_peer_name' => true,
    ],
]);

$html = @file_get_contents($url, false, $ctx);
if ($html === false) {
    echo json_encode(['error' => 'Could not fetch the page']);
    exit;
}

$result = [];

if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/si', $html, $m)) {
    $result['title'] = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
} elseif (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m)) {
    $t = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
    $t = preg_replace('/\s*[|\-–—]\s*[Aa]uksjon.*$/u', '', $t);
    $result['title'] = trim($t);
}

$search_text = ($result['title'] ?? '') . ' ' . substr(strip_tags($html), 0, 2000);
if (preg_match('/\b(19[5-9]\d|20[0-2]\d)\b/', $search_text, $m)) {
    $result['production_year'] = $m[1];
}

echo json_encode(['success' => true, 'data' => $result]);
