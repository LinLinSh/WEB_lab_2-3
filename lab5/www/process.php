<?php
header('Content-Type: text/html; charset=utf-8');
include 'Student.php';

try {
    $pdo = new PDO(
        'mysql:host=db;dbname=lab5_db',
        'lab5_user',
        'lab5_pass'
    );
    $pdo->exec("SET NAMES 'utf8mb4'");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $student = new Student($pdo);
    $student->createTable();
    
    $message = '';
    $messageType = '';
    
    if ($_POST) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $group_name = $_POST['group_name'];
        
        if ($student->addStudent($name, $email, $group_name)) {
            $message = '🎉 Студент успешно добавлен в базу данных!';
            $messageType = 'success';
        } else {
            $message = '❌ Ошибка при добавлении студента';
            $messageType = 'error';
        }
    }
    
    // Показываем всех студентов
    $students = $student->getAllStudents();
    
} catch(PDOException $e) {
    $message = '❌ Ошибка базы данных: ' . $e->getMessage();
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат - Лабораторная работа 5</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Результат операции</h1>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (count($students) > 0): ?>
                <div class="card">
                    <h2>👥 Текущий список студентов</h2>
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
                                <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td><?php echo $s['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($s['email']); ?></td>
                                        <td><?php echo htmlspecialchars($s['group_name']); ?></td>
                                        <td><?php echo $s['created_at']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p><strong>Всего студентов:</strong> <?php echo count($students); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="nav-links">
                <a href="form.html" class="btn">➕ Добавить еще студента</a>
                <a href="list.php" class="btn btn-secondary">👥 Весь список</a>
                <a href="index.php" class="btn">🏠 На главную</a>
            </div>
        </div>
    </div>
</body>
</html>
