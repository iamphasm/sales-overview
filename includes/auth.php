<?php
define('CONFIG_FILE', __DIR__ . '/../data/config.json');

function get_config(): array {
    if (!file_exists(CONFIG_FILE)) {
        // Refuse to operate without a config — do not fall back to hardcoded credentials
        return ['username' => '', 'password_hash' => ''];
    }
    return json_decode(file_get_contents(CONFIG_FILE), true) ?? [];
}

function save_config(array $config): void {
    file_put_contents(CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT));
}

function check_login(string $username, string $password): bool {
    $config = get_config();
    if (empty($config['username']) || empty($config['password_hash'])) {
        return false;
    }
    return $username === $config['username']
        && password_verify($password, $config['password_hash']);
}

function require_auth(): void {
    if (empty($_SESSION['logged_in'])) {
        http_response_code(401);
        // API callers get JSON; browser callers get a redirect
        if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            echo json_encode(['error' => 'Not authenticated']);
        } else {
            header('Location: ../index.php');
        }
        exit;
    }
}

function secure_session_start(): void {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
