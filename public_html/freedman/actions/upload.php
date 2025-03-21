<?php  

$targetDir = __DIR__ . "/uploads/";  
$imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));  
$targetFile = $targetDir . uniqid() . "." . $imageFileType;  

// Проверка ошибок загрузки
if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "error" => "Ошибка загрузки: " . $_FILES["image"]["error"]]);
    exit;
}

// Проверка директории
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true); // Создать папку, если её нет
}

// Перемещение файла
if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {  
    $fileLink = 'https://v9ky.in.ua/freedman/actions/uploads/' . basename($targetFile);

    // Отправляем изображение в Telegram
    $telegramToken = '7043411328:AAF78JIwun1ytT_-fOnhbkLsdYtsBa1ROQs'; // Замените на ваш новый токен
    $chatId = '493655224'; // Ваш chat_id

    $telegramUrl = "https://api.telegram.org/bot$telegramToken/sendPhoto";
    $postData = [
        'chat_id' => $chatId,
        'photo' => $fileLink, // Ссылка на изображение
        'caption' => "Скриншот страницы: $fileLink"
    ];

    $ch = curl_init($telegramUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $telegramResponse = curl_exec($ch);
    curl_close($ch);

    echo json_encode([
        "success" => true,
        "link" => $fileLink,
        "telegram_response" => json_decode($telegramResponse, true)
    ]);
} else {  
    echo json_encode(["success" => false, "error" => "Ошибка при сохранении файла."]);  
}
?>
