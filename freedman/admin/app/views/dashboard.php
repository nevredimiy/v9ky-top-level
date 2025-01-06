<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
</head>
<body>
        Это админка созданная Артемом 31.12.2024 - dashboard.php
<?php
        // Если авторизован, выводим приветствие
echo "Добро пожаловать, " . htmlspecialchars($_SESSION['username']) . "!";
?>
</body>
</html>