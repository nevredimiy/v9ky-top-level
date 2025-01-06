<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

// $passw = 'GSrhjpj8ir';


 // Стартуем сессию если оне ещё не запущена.
 if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

if($_SERVER["REQUEST_METHOD"] == "POST") {


    $errors = [];
    // Получаем данные из формы
    $username = isset( $_POST['login'] ) ? $_POST['login'] : '';
    $password = isset( $_POST['password'] ) ? $_POST['password'] : '';

    // Проверяем, заполнены ли поля
    if (empty($username) || empty($password)) {
        $errors[] = "Логин и пароль обязательны для заполнения!";
    }

    $user = $dbF->query("SELECT * FROM `system_users` WHERE `name` = :name", [ ":name" => $_POST['login'] ] )->find();

    if(empty($user)){
        $errors[] = "Не верный логин или пароль";
    }
    
    if ($user && password_verify($password, $user['pass'])) {
        // Успешный вход
        echo "Добро пожаловать, " . htmlspecialchars($user['name']) . "!";
        $_SESSION['status'] = $user['permition'];
        $_SESSION['username'] = $user['name'];
        header("Location: dashboard");
        
        exit;
    }

    

}

require_once ADMINVIEWS . '/login.php';