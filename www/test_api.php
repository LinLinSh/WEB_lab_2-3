<?php
// Убедимся что нет лишних символов перед <?php
require_once 'ApiClient.php';

// Устанавливаем заголовки ДО любого вывода
header('Content-Type: text/plain; charset=utf-8');

echo "Testing API connection...\n\n";

// Проверяем доступные функции
echo "=== PHP Functions ===\n";
echo "file_get_contents: " . (function_exists('file_get_contents') ? '✅ Available' : '❌ Missing') . "\n";
echo "stream_context_create: " . (function_exists('stream_context_create') ? '✅ Available' : '❌ Missing') . "\n";
echo "json_decode: " . (function_exists('json_decode') ? '✅ Available' : '❌ Missing') . "\n";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? '✅ Enabled' : '❌ Disabled') . "\n\n";

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