<?php
header('Content-Type: application/json');

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

$scheme = strtolower(parse_url($url, PHP_URL_SCHEME));
if (!in_array($scheme, ['http', 'https'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Only http/https URLs are allowed']);
    exit;
}

// Block private/loopback addresses (SSRF protection)
$host = parse_url($url, PHP_URL_HOST);
$ip = gethostbyname($host);
if (
    $ip === false ||
    preg_match('/^(127\.|10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)/', $ip) ||
    in_array($ip, ['::1', 'localhost'])
) {
    http_response_code(400);
    echo json_encode(['error' => 'URL not allowed']);
    exit;
}

$ctx = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'header'  => "User-Agent: Mozilla/5.0 (compatible; AuksjonBot/1.0)\r\n",
        'timeout' => 10,
        'follow_location' => 1,
        'max_redirects' => 3,
    ],
    'ssl' => [
        'verify_peer' => true,
        'verify_peer_name' => true,
    ],
]);

$html = @file_get_contents($url, false, $ctx);
if ($html === false) {
    echo json_encode(['error' => 'Could not fetch the page']);
    exit;
}

$result = [];

// og:title takes priority over <title>
if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/si', $html, $m)) {
    $result['title'] = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
} elseif (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m)) {
    $t = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
    $t = preg_replace('/\s*[|\-–—]\s*[Aa]uksjon.*$/u', '', $t);
    $result['title'] = trim($t);
}

// Extract year from title or page content
$search_text = ($result['title'] ?? '') . ' ' . substr(strip_tags($html), 0, 2000);
if (preg_match('/\b(19[5-9]\d|20[0-2]\d)\b/', $search_text, $m)) {
    $result['production_year'] = $m[1];
}

echo json_encode(['success' => true, 'data' => $result]);
