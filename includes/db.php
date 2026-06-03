<?php
define('DATA_FILE', __DIR__ . '/../data/auctions.json');

function get_auctions(): array {
    if (!file_exists(DATA_FILE)) return [];
    $json = file_get_contents(DATA_FILE);
    return json_decode($json, true) ?? [];
}

function save_auctions(array $auctions): void {
    $dir = dirname(DATA_FILE);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents(DATA_FILE, json_encode($auctions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function add_auction(array $data): array {
    $auctions = get_auctions();
    $data['id'] = uniqid('', true);
    $data['status'] = $data['status'] ?? 'live';
    $data['final_price'] = null;
    $data['created_at'] = date('Y-m-d H:i:s');
    $auctions[] = $data;
    save_auctions($auctions);
    return $data;
}

function update_auction(string $id, array $updates): bool {
    $auctions = get_auctions();
    foreach ($auctions as &$auction) {
        if ($auction['id'] === $id) {
            foreach ($updates as $key => $value) {
                $auction[$key] = $value;
            }
            save_auctions($auctions);
            return true;
        }
    }
    return false;
}

function delete_auction(string $id): bool {
    $auctions = get_auctions();
    $filtered = array_values(array_filter($auctions, fn($a) => $a['id'] !== $id));
    if (count($filtered) === count($auctions)) return false;
    save_auctions($filtered);
    return true;
}

function auto_update_statuses(): void {
    $auctions = get_auctions();
    $today = date('Y-m-d');
    $changed = false;
    foreach ($auctions as &$auction) {
        if ($auction['status'] === 'live' && !empty($auction['finish_date']) && $auction['finish_date'] < $today) {
            $auction['status'] = 'finished';
            $changed = true;
        }
    }
    if ($changed) save_auctions($auctions);
}

function get_live_auctions(): array {
    return array_values(array_filter(get_auctions(), fn($a) => $a['status'] === 'live'));
}

function get_finished_auctions(): array {
    return array_values(array_filter(get_auctions(), fn($a) => $a['status'] === 'finished'));
}
