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

    echo json_encode([
        "success" => true,
        "link" => $fileLink,
    ]);
} else {  
    echo json_encode(["success" => false, "error" => "Ошибка при сохранении файла."]);  
}
?>
