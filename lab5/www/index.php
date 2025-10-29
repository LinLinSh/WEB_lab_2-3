<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинский центр - Запись на прием</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Медицинский центр "Здоровье"</h1>
            <p>Запись на прием к врачу - быстро и удобно</p>
        </div>
        
        <div class="content">
            <div class="card">
                <h2>📊 Статус системы</h2>
                <p><strong>Система записи на прием работает корректно!</strong></p>
                <p>Версия PHP: <?php echo phpversion(); ?></p>
                
                <?php
                try {
                    $pdo = new PDO('mysql:host=db;dbname=clinic_db', 'clinic_user', 'clinic_pass');
                    $pdo->exec("SET NAMES 'utf8mb4'");
                    echo '<div class="status success">✅ Подключение к БД успешно</div>';
                    
                    $sql = "CREATE TABLE IF NOT EXISTS appointments (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        patient_name VARCHAR(100) NOT NULL,
                        patient_phone VARCHAR(20) NOT NULL,
                        doctor_name VARCHAR(100) NOT NULL,
                        specialization VARCHAR(100) NOT NULL,
                        appointment_date DATE NOT NULL,
                        appointment_time TIME NOT NULL,
                        symptoms TEXT,
                        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                    
                    $pdo->exec($sql);
                    echo '<div class="status success">✅ Таблица записей создана</div>';
                    
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
                    $count = $stmt->fetch()['count'];
                    echo "<p><strong>Всего записей в системе:</strong> $count</p>";
                    
                    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM appointments WHERE status = 'pending'");
                    $pending = $stmt->fetch()['pending'];
                    echo "<p><strong>Ожидают подтверждения:</strong> <span class='status pending'>$pending</span></p>";
                    
                } catch (PDOException $e) {
                    echo '<div class="status error">❌ Ошибка БД: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
            
            <div class="card">
                <h2>👨‍⚕️ Наши врачи</h2>
                <div class="doctor-card">
                    <h3>Доктор Иванова А.П.</h3>
                    <p>💊 Терапевт</p>
                    <p>📅 График: Пн-Пт, 9:00-18:00</p>
                </div>
                <div class="doctor-card">
                    <h3>Доктор Петров С.М.</h3>
                    <p>🦷 Стоматолог</p>
                    <p>📅 График: Вт-Сб, 10:00-19:00</p>
                </div>
                <div class="doctor-card">
                    <h3>Доктор Сидорова Е.В.</h3>
                    <p>👁️ Офтальмолог</p>
                    <p>📅 График: Пн-Ср, 8:00-17:00</p>
                </div>
            </div>
            
            <div class="nav-links">
                <a href="appointment.html" class="btn">📅 Записаться на прием</a>
                <a href="appointments.php" class="btn btn-secondary">📋 Все записи</a>
                <a href="doctors.php" class="btn btn-info">👨‍⚕️ Расписание врачей</a>
            </div>
        </div>
    </div>
</body>
</html>
