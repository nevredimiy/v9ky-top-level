<?php
 // Стартуем сессию если оне ещё не запущена.
 if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}  

// Проверка авторизации  
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin') {  
    header("Location: /admin/login"); // Перенаправление на страницу входа  
    exit();  
}  

require_once ADMINVIEWS . "/dashboard.php";