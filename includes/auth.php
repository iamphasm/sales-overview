<?php
define('CONFIG_FILE', __DIR__ . '/../data/config.json');

function get_config(): array {
    if (!file_exists(CONFIG_FILE)) {
        return [
            'username'      => 'admin',
            'password_hash' => password_hash('admin', PASSWORD_BCRYPT),
        ];
    }
    return json_decode(file_get_contents(CONFIG_FILE), true) ?? [];
}

function save_config(array $config): void {
    file_put_contents(CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT));
}

function check_login(string $username, string $password): bool {
    $config = get_config();
    return $username === ($config['username'] ?? '')
        && password_verify($password, $config['password_hash'] ?? '');
}

function require_auth(): void {
    if (empty($_SESSION['logged_in'])) {
        header('Location: index.php');
        exit;
    }
}
