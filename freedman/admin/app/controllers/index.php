<?php
// Проверяем, является ли запрашиваемый URI админским  
if (strpos($url_path, '/admin') === 0) {  
    // Удаляем префикс /admin  
    $admin_path = str_replace('/admin', '', $url_path);  
    $admin_parts = explode('/', trim($admin_path, ' /'));  

    // Получаем модуль и действие для админских страниц  
    $admin_module = $admin_parts[0];  
    $admin_action = isset($admin_parts[1]) ? $admin_parts[1] : 'main'; // Действие по умолчанию  

    switch ($admin_module) {  
        case "dashboard":  
            $title = "Админская панель";   
            require_once("admin/dashboard.php");  
            break;  
        case "users":  
            $title = "Управление пользователями";   
            require_once("admin/users.php");  
            break;  
        case "settings":  
            $title = "Настройки";   
            require_once("admin/settings.php");  
            break;  
        // Добавьте другие админские модули по мере необходимости  
        default:  
            $title = "404 - Страница не найдена";   
            require_once("admin/404.php");  
            break;  
    }  
    exit; // Завершаем выполнение, если обработали админский запрос  
}