<?php
session_start();

$name = trim($_POST['name'] ?? '');
$age = $_POST['age'] ?? '';
$doctor = $_POST['doctor'] ?? '';
$first_visit = isset($_POST['first_visit']);
$visit_type = $_POST['visit_type'] ?? '';

$errors = [];
if (empty($name)) $errors[] = "Имя не может быть пустым";
if (!is_numeric($age) || $age < 1 || $age > 120) $errors[] = "Возраст должен быть от 1 до 120";
if (empty($doctor)) $errors[] = "Выберите врача";
if (empty($visit_type)) $errors[] = "Выберите форму визита";

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: index.php");
    exit();
}

$_SESSION['name'] = $name;
$_SESSION['age'] = (int)$age;
$_SESSION['doctor'] = $doctor;
$_SESSION['first_visit'] = $first_visit;
$_SESSION['visit_type'] = $visit_type;

$line = implode(";", [$name, $age, $doctor, $first_visit ? 'Да' : 'Нет', $visit_type]) . "\n";
file_put_contents(__DIR__ . '/data.txt', $line, FILE_APPEND | LOCK_EX);

header("Location: index.php");
exit();
?>
