<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$errors = [];
$success = '';

if (
    isset($_FILES['tshirt_upload']) &&
    $_FILES['tshirt_upload']['error'] === 0 &&
    isset($_POST['team_id'])
) {
    $team_id = intval($_POST['team_id']);
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/img/t-shirt/';
    $filename = basename($_FILES['tshirt_upload']['name']);
    $tmpPath = $_FILES['tshirt_upload']['tmp_name'];
    $fileSize = $_FILES['tshirt_upload']['size'];
    $fileType = mime_content_type($tmpPath);

    

    $maxSize = 1024 * 1024; // 1 MB    
    $allowedTypes = ['image/png'];

    // Перевірка типу
    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = "Дозволено лише PNG-файли.";
    }

    // Перевірка розміру
    if ($fileSize > $maxSize) {
        $errors[] = "Файл перевищує 1 MB.";
    }

    // Перевірка унікальності імені
    $existingFiles = glob($uploadDir . '*.png');

    foreach ($existingFiles as $item) {
        if (basename($item) === $filename) {
            $errors[] = "Файл із такою назвою вже існує.";
            break;
        }
    }

    // Перевірка наявності папки
    if (!is_dir($uploadDir)) {
        $errors[] = "Папка для зберігання не існує.";
    }

    // Якщо немає помилок — зберігаємо файл
    if (empty($errors)) {
        // $uniqueName = uniqid('tshirt_', true) . '.png';
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($tmpPath, $targetPath)) {
            $success = "Файл успішно завантажено!";
        } else {
            $errors[] = "Не вдалося зберегти файл.";
        }
    }
} else {
    $errors[] = "Дані не отримано або сталася помилка при завантаженні.";
}

// Зберігаємо повідомлення в сесію
$_SESSION['upload_errors'] = $errors;
$_SESSION['upload_success'] = $success;

// Переадресація назад
$team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;
header("Location: team_update.php?id=" . $team_id);
exit;
?>
