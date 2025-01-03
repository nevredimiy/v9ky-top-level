<?php
session_start();  

// Проверка авторизации  
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {  
    header("Location: /admin/login"); // Перенаправление на страницу входа  
    exit();  
}  

require_once ADMINVIEWS . "/dashboard.php";