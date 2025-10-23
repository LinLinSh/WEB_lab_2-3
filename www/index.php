<?php
session_start();

// Обработка AJAX-запроса на обновление API
if (($_GET['action'] ?? null) === 'refresh_api') {
    header('Content-Type: application/json; charset=utf-8');
    require_once 'ApiClient.php';
    $api = new ApiClient();
    $url = 'https://api.spaceflightnewsapi.net/v4/articles/?limit=3';
    $cacheFile = __DIR__ . '/api_cache.json';
    $cacheTtl = 300; // 5 минут

    if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTtl) {
        $data = json_decode(file_get_contents($cacheFile), true);
    } else {
        $data = $api->request($url);
        if (!isset($data['error'])) {
            file_put_contents($cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
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
  </style>
</head>
<body>

<h1>Запись к врачу</h1>
<a href="form.html">← Вернуться к форме</a>

<?php if (isset($_SESSION['form_data'])): ?>
<div class="section">
  <h2>Ваша заявка</h2>
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

<?php if (isset($_SESSION['user_info'])): ?>
<div class="section">
  <h2>Информация о пользователе</h2>
  <?php foreach ($_SESSION['user_info'] as $key => $val): ?>
    <p><b><?= htmlspecialchars($key) ?>:</b> <?= htmlspecialchars($val) ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (isset($_COOKIE['last_submission'])): ?>
<div class="section">
  <h2>Последняя отправка формы</h2>
  <p><?= htmlspecialchars($_COOKIE['last_submission']) ?></p>
</div>
<?php endif; ?>

<div class="section">
  <h2>Медицинские и космические новости (API)</h2>
  <button id="refreshBtn">🔄 Обновить данные</button>
  <div id="apiResult">
    <?php
    $cacheFile = __DIR__ . '/api_cache.json';
    if (file_exists($cacheFile)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if (isset($cached['error'])) {
            echo "<p class='error'>Ошибка API: " . htmlspecialchars($cached['error']) . "</p>";
        } elseif (!empty($cached['results'])) {
            echo "<ul>";
            foreach ($cached['results'] as $item) {
                echo "<li><a href='" . htmlspecialchars($item['url']) . "' target='_blank'>" . htmlspecialchars($item['title']) . "</a> — " . htmlspecialchars($item['news_site'] ?? '') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Нет данных.</p>";
        }
    } else {
        echo "<p>Данные ещё не загружены. Отправьте форму или нажмите «Обновить».</p>";
    }
    ?>
  </div>
</div>

<script>
document.getElementById('refreshBtn').addEventListener('click', async () => {
  const btn = document.getElementById('refreshBtn');
  btn.disabled = true;
  btn.textContent = 'Загрузка...';
  try {
    const res = await fetch('/?action=refresh_api');
    const data = await res.json();
    let html = '';
    if (data.error) {
      html = `<p class="error">Ошибка: ${data.error}</p>`;
    } else if (data.results && data.results.length) {
      html = '<ul>';
      data.results.forEach(item => {
        html += `<li><a href="${item.url}" target="_blank">${item.title}</a> — ${item.news_site || ''}</li>`;
      });
      html += '</ul>';
    } else {
      html = '<p>Нет данных.</p>';
    }
    document.getElementById('apiResult').innerHTML = html;
  } catch (e) {
    document.getElementById('apiResult').innerHTML = '<p class="error">Ошибка сети</p>';
  } finally {
    btn.disabled = false;
    btn.textContent = '🔄 Обновить данные';
  }
});
</script>

</body>
</html>
