<?php
header('Content-Type: text/html; charset=utf-8');
include 'Appointment.php';

try {
    $pdo = new PDO('mysql:host=db;dbname=clinic_db', 'clinic_user', 'clinic_pass');
    $pdo->exec("SET NAMES 'utf8mb4'");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $appointment = new Appointment($pdo);
    
    // Обработка изменения статуса
    if (isset($_POST['update_status'])) {
        $appointment->updateAppointmentStatus($_POST['appointment_id'], $_POST['new_status']);
    }
    
    // Обработка удаления
    if (isset($_POST['delete_appointment'])) {
        $appointment->deleteAppointment($_POST['appointment_id']);
    }
    
    $appointments = $appointment->getAllAppointments();
    
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все записи - Медицинский центр</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Все записи на прием</h1>
            <p>Управление записями пациентов</p>
        </div>
        
        <div class="content">
            <?php if (isset($error)): ?>
                <div class="message error">
                    ❌ Ошибка загрузки данных: <?php echo $error; ?>
                </div>
            <?php elseif (count($appointments) > 0): ?>
                <div class="table-container">
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>👤 Пациент</th>
                                <th>📞 Телефон</th>
                                <th>👨‍⚕️ Врач</th>
                                <th>📅 Дата</th>
                                <th>⏰ Время</th>
                                <th>📝 Симптомы</th>
                                <th>📊 Статус</th>
                                <th>⚙️ Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $apt): ?>
                                <tr>
                                    <td><?php echo $apt['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($apt['patient_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($apt['patient_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                                    <td><?php echo $apt['appointment_date']; ?></td>
                                    <td><?php echo $apt['appointment_time']; ?></td>
                                    <td><?php echo htmlspecialchars(substr($apt['symptoms'], 0, 50)) . '...'; ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        switch($apt['status']) {
                                            case 'pending': $statusClass = 'pending'; break;
                                            case 'confirmed': $statusClass = 'success'; break;
                                            case 'completed': $statusClass = 'completed'; break;
                                            case 'cancelled': $statusClass = 'error'; break;
                                        }
                                        ?>
                                        <span class="status <?php echo $statusClass; ?>">
                                            <?php 
                                            $statusText = [
                                                'pending' => 'Ожидание',
                                                'confirmed' => 'Подтверждено',
                                                'completed' => 'Завершено',
                                                'cancelled' => 'Отменено'
                                            ];
                                            echo $statusText[$apt['status']];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $apt['id']; ?>">
                                            <select name="new_status" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $apt['status'] == 'pending' ? 'selected' : ''; ?>>Ожидание</option>
                                                <option value="confirmed" <?php echo $apt['status'] == 'confirmed' ? 'selected' : ''; ?>>Подтвердить</option>
                                                <option value="completed" <?php echo $apt['status'] == 'completed' ? 'selected' : ''; ?>>Завершить</option>
                                                <option value="cancelled" <?php echo $apt['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменить</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        <form method="POST" style="display: inline; margin-left: 5px;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $apt['id']; ?>">
                                            <button type="submit" name="delete_appointment" value="1" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p><strong>Всего записей:</strong> <?php echo count($appointments); ?></p>
            <?php else: ?>
                <div class="message info">
                    ℹ️ В системе пока нет записей на прием.
                </div>
            <?php endif; ?>
            
            <div class="nav-links">
                <a href="index.php" class="btn btn-secondary">🏠 На главную</a>
                <a href="appointment.html" class="btn">📅 Новая запись</a>
            </div>
        </div>
    </div>
</body>
</html>
