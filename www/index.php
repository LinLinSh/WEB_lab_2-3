<?php
session_start();

// Обработка AJAX-запроса на обновление API
if (($_GET['action'] ?? null) === 'refresh_api') {
    header('Content-Type: application/json; charset=utf-8');
    require_once 'ApiClient.php';
    $api = new ApiClient();
    
    // Только проверенные рабочие API endpoints
    $endpoints = [
        'https://jsonplaceholder.typicode.com/posts?_limit=3',
        'https://api.spaceflightnewsapi.net/v4/articles/?limit=3'
    ];
    
    $cacheFile = __DIR__ . '/api_cache.json';
    $cacheTtl = 300; // 5 минут
    
    // Используем кеш если он свежий
    if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTtl) {
        $data = json_decode(file_get_contents($cacheFile), true);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Пробуем endpoints по очереди
    foreach ($endpoints as $url) {
        $data = $api->request($url);
        if (!isset($data['error'])) {
            // Сохраняем успешный результат
            file_put_contents($cacheFile, json_encode([
                'source' => $url,
                'data' => $data,
                'cached_at' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            
            echo json_encode(['source' => $url, 'data' => $data], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    
    // Если все API упали, создаем демо-данные
    $demoData = [
        'source' => 'demo',
        'data' => [
            'results' => [
                [
                    'title' => 'Демо: Медицинская конференция 2024',
                    'url' => '#',
                    'news_site' => 'Локальная система'
                ],
                [
                    'title' => 'Демо: Новые методы лечения',
                    'url' => '#', 
                    'news_site' => 'Локальная система'
                ]
            ]
        ],
        'cached_at' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($cacheFile, json_encode($demoData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode($demoData, JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Запись к врачу — ЛР4</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 0 15px; }
    .section { margin: 25px 0; padding: 15px; background: #f9f9f9; border-radius: 8px; }
    h2 { color: #2c3e50; }
    pre { background: #eee; padding: 10px; border-radius: 4px; overflow-x: auto; }
    button { padding: 8px 16px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #2980b9; }
    .error { color: red; }
    .success { color: green; }
    .loading { color: #666; }
    .api-info { background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
    .demo-notice { background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; border-left: 4px solid #ffc107; }
  </style>
</head>
<body>

<h1>Запись к врачу</h1>
<a href="form.html">← Вернуться к форме</a>

<?php if (isset($_SESSION['form_data'])): ?>
<div class="section">
  <h2>✅ Ваша заявка принята!</h2>
  <?php
  $labels = ['name' => 'Имя', 'age' => 'Возраст', 'doctor' => 'Врач', 'visit_type' => 'Форма визита'];
  $doctorMap = ['therapist' => 'Терапевт', 'dentist' => 'Стоматолог', 'cardiologist' => 'Кардиолог', 'dermatologist' => 'Дерматолог'];
  $visitMap = ['on-site' => 'Очно', 'online' => 'Онлайн'];
  foreach ($_SESSION['form_data'] as $key => $value):
      if ($key === 'first_visit') continue;
      $label = $labels[$key] ?? $key;
      if ($key === 'doctor') $value = $doctorMap[$value] ?? $value;
      if ($key === 'visit_type') $value = $visitMap[$value] ?? $value;
      echo "<p><b>{$label}:</b> " . htmlspecialchars($value) . "</p>";
  endforeach;
  echo isset($_SESSION['form_data']['first_visit']) ? "<p><b>Первая консультация:</b> Да</p>" : "<p><b>Первая консультация:</b> Нет</p>";
  ?>
</div>
<?php endif; ?>

<div class="section">
  <h2>📡 Новости и данные (API)</h2>
  
  <div class="api-info">
    <strong>Статус:</strong> Используются проверенные API endpoints (JSONPlaceholder + Spaceflight News)
  </div>
  
  <button id="refreshBtn">🔄 Обновить данные</button>
  <a href="test_api.php" style="margin-left: 10px;">🧪 Тест API</a>
  
  <div id="apiResult">
    <?php
    $cacheFile = __DIR__ . '/api_cache.json';
    if (file_exists($cacheFile)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if (isset($cached['error'])) {
            echo "<p class='error'>Ошибка API: " . htmlspecialchars($cached['error']) . "</p>";
        } elseif (!empty($cached['data'])) {
            $data = $cached['data'];
            $isDemo = ($cached['source'] ?? '') === 'demo';
            
            if ($isDemo) {
                echo "<div class='demo-notice'>⚠️ Используются демо-данные (внешние API временно недоступны)</div>";
            } else {
                echo "<p class='success'>✅ Данные загружены из " . parse_url($cached['source'], PHP_URL_HOST) . " (кеш: " . ($cached['cached_at'] ?? 'unknown') . ")</p>";
            }
            
            // Обрабатываем разные форматы API ответов
            if (isset($data['results'])) {
                echo "<h3>🚀 Новости:</h3><ul>";
                foreach ($data['results'] as $item) {
                    echo "<li><a href='" . htmlspecialchars($item['url']) . "' target='_blank'>" . htmlspecialchars($item['title']) . "</a>" . (isset($item['news_site']) ? " — " . htmlspecialchars($item['news_site']) : "") . "</li>";
                }
                echo "</ul>";
            } elseif (is_array($data) && isset($data[0]['title'])) {
                echo "<h3>📝 Последние записи:</h3><ul>";
                foreach ($data as $item) {
                    $title = $item['title'] ?? 'No title';
                    $desc = $item['body'] ?? '';
                    echo "<li><strong>" . htmlspecialchars($title) . "</strong>: " . htmlspecialchars(substr($desc, 0, 100)) . "...</li>";
                }
                echo "</ul>";
            }
        }
    } else {
        echo "<p>Данные ещё не загружены. Нажмите «Обновить».</p>";
    }
    ?>
  </div>
</div>

<script>
document.getElementById('refreshBtn').addEventListener('click', async () => {
  const btn = document.getElementById('refreshBtn');
  const resultDiv = document.getElementById('apiResult');
  
  btn.disabled = true;
  btn.textContent = 'Загрузка...';
  resultDiv.innerHTML = '<p class="loading">⏳ Загружаем данные...</p>';
  
  try {
    const res = await fetch('/?action=refresh_api');
    const data = await res.json();
    
    let html = '';
    if (data.error) {
      html = `<p class="error">❌ Ошибка: ${data.error}</p>`;
    } else if (data.data) {
      const apiData = data.data;
      const isDemo = data.source === 'demo';
      
      if (isDemo) {
        html = `<div class="demo-notice">⚠️ Используются демо-данные (внешние API временно недоступны)</div>`;
      } else {
        html = `<p class="success">✅ Данные обновлены! (Источник: ${new URL(data.source).hostname})</p>`;
      }
      
      if (apiData.results) {
        html += '<h3>🚀 Новости:</h3><ul>';
        apiData.results.forEach(item => {
          html += `<li><a href="${item.url}" target="_blank">${item.title}</a> — ${item.news_site || ''}</li>`;
        });
        html += '</ul>';
      } else if (Array.isArray(apiData) && apiData[0]) {
        html += '<h3>📝 Последние записи:</h3><ul>';
        apiData.forEach(item => {
          const title = item.title || 'No title';
          const desc = item.body || '';
          html += `<li><strong>${title}</strong>: ${desc.substring(0, 100)}...</li>`;
        });
        html += '</ul>';
      }
    } else {
      html = '<p>Неизвестный формат ответа</p>';
    }
    
    resultDiv.innerHTML = html;
  } catch (e) {
    resultDiv.innerHTML = '<p class="error">❌ Ошибка сети: ' + e.message + '</p>';
  } finally {
    btn.disabled = false;
    btn.textContent = '🔄 Обновить данные';
  }
});
</script>

</body>
</html>