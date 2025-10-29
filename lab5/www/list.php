<?php
header('Content-Type: text/html; charset=utf-8');
include 'Student.php';

try {
    $pdo = new PDO('mysql:host=db;dbname=lab5_db', 'lab5_user', 'lab5_pass');
    $pdo->exec("SET NAMES 'utf8mb4'");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $student = new Student($pdo);
    $students = $student->getAllStudents();
    
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов - Лабораторная работа 5</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Список студентов</h1>
            <p>Все зарегистрированные студенты в системе</p>
        </div>
        
        <div class="content">
            <?php if (isset($error)): ?>
                <div class="message error">
                    ❌ Ошибка загрузки данных: <?php echo $error; ?>
                </div>
            <?php elseif (count($students) > 0): ?>
                <div class="table-container">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>👤 Имя</th>
                                <th>📧 Email</th>
                                <th>🎓 Группа</th>
                                <th>📅 Дата добавления</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($student['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['group_name']); ?></td>
                                    <td><?php echo $student['created_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p><strong>Всего студентов:</strong> <?php echo count($students); ?></p>
            <?php else: ?>
                <div class="message info">
                    ℹ️ В базе данных пока нет студентов.
                </div>
            <?php endif; ?>
            
            <div class="nav-links">
                <a href="index.php" class="btn btn-secondary">🏠 На главную</a>
                <a href="form.html" class="btn">➕ Добавить студента</a>
            </div>
        </div>
    </div>
</body>
</html>
