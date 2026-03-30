<?php
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'none'");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = [];

if (!empty($_POST)) {
    $data = $_POST;
} elseif (!empty($raw)) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}

if (!empty($data['website'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Bad request']);
    exit;
}

$type = isset($data['type']) ? trim($data['type']) : '';

if ($type !== 'support') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid type']);
    exit;
}

function clean_text($value, $maxLen) {
    $value = is_string($value) ? $value : '';
    $value = trim($value);
    $value = str_replace(["\r", "\n"], ' ', $value);
    if (strlen($value) > $maxLen) {
        $value = substr($value, 0, $maxLen);
    }
    return $value;
}

$entry = [
    'type' => 'support',
    'createdAt' => gmdate('c'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
];

$entry['name'] = clean_text($data['name'] ?? '', 60);
$entry['email'] = clean_text($data['email'] ?? '', 120);
$entry['request'] = clean_text($data['request'] ?? '', 500);
if ($entry['name'] === '' || $entry['email'] === '' || $entry['request'] === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing fields']);
    exit;
}

$line = json_encode($entry) . PHP_EOL;
$path = __DIR__ . '/support_submissions.txt';

if (file_put_contents($path, $line, FILE_APPEND | LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Write failed']);
    exit;
}

echo json_encode(['ok' => true]);
