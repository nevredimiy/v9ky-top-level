<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$errors = [];
$success = '';


if (isset($_POST['filename'])) {
    $filename = basename($_POST['filename']); // безопасно
    $filePath = '../../img/t-shirt/' . $filename;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            $success = "Файл $filename видалено.";
        } else {
            $errors[] =  "Помилка при видаленні файлу.";
        }
    } else {
        $errors[] =  "Файл не знайдено.";
    }
} else {
    $errors[] =  "Файл не вказано.";
}

// Зберігаємо повідомлення в сесію
$_SESSION['upload_errors'] = $errors;
$_SESSION['upload_success'] = $success;

// Переадресація назад
$team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;
header("Location: team_update.php?id=" . $team_id);
exit;
?>
