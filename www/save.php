<?php
session_start();
require_once 'UserInfo.php';

$_SESSION['form_data'] = $_POST;
$_SESSION['user_info'] = UserInfo::getInfo();
setcookie("last_submission", date('Y-m-d H:i:s'), time() + 3600, "/");

header("Location: /");
exit;
