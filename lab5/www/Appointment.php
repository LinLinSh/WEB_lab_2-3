<?php
class Appointment {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function createTable() {
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
        
        $this->pdo->exec($sql);
    }
    
    public function addAppointment($patient_name, $patient_phone, $doctor_name, $specialization, $appointment_date, $appointment_time, $symptoms) {
        $sql = "INSERT INTO appointments (patient_name, patient_phone, doctor_name, specialization, appointment_date, appointment_time, symptoms) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$patient_name, $patient_phone, $doctor_name, $specialization, $appointment_date, $appointment_time, $symptoms]);
    }
    
    public function getAllAppointments() {
        $sql = "SELECT * FROM appointments ORDER BY appointment_date DESC, appointment_time DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAppointmentsByDoctor($doctor_name) {
        $sql = "SELECT * FROM appointments WHERE doctor_name = ? ORDER BY appointment_date, appointment_time";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$doctor_name]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateAppointmentStatus($id, $status) {
        $sql = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
    
    public function deleteAppointment($id) {
        $sql = "DELETE FROM appointments WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
