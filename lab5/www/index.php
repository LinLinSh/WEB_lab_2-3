<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 5 - Студенты</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Лабораторная работа 5</h1>
            <p>Система управления студентами</p>
        </div>
        
        <div class="content">
            <div class="card">
                <h2>📊 Статус системы</h2>
                <p><strong>PHP работает корректно!</strong></p>
                <p>Версия PHP: <?php echo phpversion(); ?></p>
                
                <?php
                // Тест БД
                try {
                    $pdo = new PDO('mysql:host=db;dbname=lab5_db', 'lab5_user', 'lab5_pass');
                    $pdo->exec("SET NAMES 'utf8mb4'");
                    echo '<div class="status success">✅ Подключение к БД успешно</div>';
                    
                    // Создаем таблицу если её нет
                    $sql = "CREATE TABLE IF NOT EXISTS students (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL,
                        email VARCHAR(100) NOT NULL UNIQUE,
                        group_name VARCHAR(50) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                    
                    $pdo->exec($sql);
                    echo '<div class="status success">✅ Таблица students создана</div>';
                    
                    // Показываем количество студентов
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM students");
                    $count = $stmt->fetch()['count'];
                    echo "<p><strong>Количество студентов в базе:</strong> $count</p>";
                    
                } catch (PDOException $e) {
                    echo '<div class="status error">❌ Ошибка БД: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
            
            <div class="nav-links">
                <a href="form.html" class="btn">➕ Добавить студента</a>
                <a href="list.php" class="btn btn-secondary">👥 Список студентов</a>
            </div>
        </div>
    </div>
</body>
</html>
