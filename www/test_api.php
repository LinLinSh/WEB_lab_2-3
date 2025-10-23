<?php
// Убедимся что нет лишних символов перед <?php
require_once 'ApiClient.php';

// Устанавливаем заголовки ДО любого вывода
header('Content-Type: text/plain; charset=utf-8');

echo "=== SSL и системная информация ===\n\n";

// Проверяем SSL
echo "SSL проверки:\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) ? '✅ ' . $_SERVER['HTTPS'] : '❌ off') . "\n";
echo "SSL_CIPHER: " . ($_SERVER['SSL_CIPHER'] ?? '❌ none') . "\n";
echo "SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'unknown') . "\n";
echo "REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'unknown') . "\n\n";

// Системная информация
echo "=== Системная информация ===\n";
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
echo "PHP_VERSION: " . PHP_VERSION . "\n";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? '✅ On' : '❌ Off') . "\n\n";

// Проверяем DNS
echo "=== DNS Resolution Test ===\n";
$hosts = ['jsonplaceholder.typicode.com', 'api.spaceflightnewsapi.net'];
foreach ($hosts as $host) {
    $ip = gethostbyname($host);
    echo "$host: " . ($ip !== $host ? "✅ $ip" : "❌ Failed") . "\n";
}
echo "\n";

$api = new ApiClient();

// Тестируем только рабочие API endpoints
$endpoints = [
    'JSONPlaceholder' => 'https://jsonplaceholder.typicode.com/posts/1',
    'Spaceflight News' => 'https://api.spaceflightnewsapi.net/v4/articles/?limit=1'
];

foreach ($endpoints as $name => $url) {
    echo "=== Testing: $name ===\n";
    echo "URL: $url\n";
    
    $result = $api->request($url);
    
    if (isset($result['error'])) {
        echo "❌ ERROR: {$result['error']}\n";
    } else {
        echo "✅ SUCCESS: Got response\n";
        if (isset($result['title'])) {
            echo "Title: " . substr($result['title'], 0, 50) . "...\n";
        } else if (isset($result['results'][0]['title'])) {
            echo "First title: " . substr($result['results'][0]['title'], 0, 50) . "...\n";
        }
        echo "Data keys: " . implode(', ', array_keys($result)) . "\n";
    }
    echo "\n";
}
?>