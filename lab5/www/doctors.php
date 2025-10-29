<?php
header('Content-Type: text/html; charset=utf-8');
include 'Appointment.php';

try {
    $pdo = new PDO('mysql:host=db;dbname=clinic_db', 'clinic_user', 'clinic_pass');
    $pdo->exec("SET NAMES 'utf8mb4'");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $appointment = new Appointment($pdo);
    
    $doctors = [
        'Иванова А.П.' => 'Терапевт',
        'Петров С.М.' => 'Стоматолог', 
        'Сидорова Е.В.' => 'Офтальмолог'
    ];
    
    $doctorSchedules = [];
    foreach ($doctors as $doctor => $specialization) {
        $doctorSchedules[$doctor] = $appointment->getAppointmentsByDoctor($doctor);
    }
    
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание врачей - Медицинский центр</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👨‍⚕️ Расписание врачей</h1>
            <p>График работы и записи пациентов</p>
        </div>
        
        <div class="content">
            <?php if (isset($error)): ?>
                <div class="message error">
                    ❌ Ошибка загрузки данных: <?php echo $error; ?>
                </div>
            <?php else: ?>
                <?php foreach ($doctorSchedules as $doctor => $appointments): ?>
                    <div class="card">
                        <h2>👨‍⚕️ <?php echo $doctor; ?> - <?php echo $doctors[$doctor]; ?></h2>
                        
                        <?php if (count($appointments) > 0): ?>
                            <div class="table-container">
                                <table class="appointments-table">
                                    <thead>
                                        <tr>
                                            <th>Пациент</th>
                                            <th>Дата</th>
                                            <th>Время</th>
                                            <th>Жалобы</th>
                                            <th>Статус</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $apt): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                                                <td><?php echo $apt['appointment_date']; ?></td>
                                                <td><?php echo $apt['appointment_time']; ?></td>
                                                <td><?php echo htmlspecialchars(substr($apt['symptoms'], 0, 30)) . '...'; ?></td>
                                                <td>
                                                    <?php 
                                                    $statusText = [
                                                        'pending' => '⏳',
                                                        'confirmed' => '✅',
                                                        'completed' => '🏁',
                                                        'cancelled' => '❌'
                                                    ];
                                                    echo $statusText[$apt['status']];
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <p><strong>Всего записей:</strong> <?php echo count($appointments); ?></p>
                        <?php else: ?>
                            <p>На данный момент у врача нет записей.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="nav-links">
                <a href="index.php" class="btn btn-secondary">🏠 На главную</a>
                <a href="appointment.html" class="btn">📅 Записаться на прием</a>
                <a href="appointments.php" class="btn">📋 Все записи</a>
            </div>
        </div>
    </div>
</body>
</html>
