<?php 

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

/**
* Все данные страницы основывауються от переменной turnir, которая береться из переменной tournament. 
* А tournament береться с адресной строки - это название турнира, написаное латиницей. 
* Код разбора адресной сроки находиться в index.php на 75 строке
*/


if(!isset($turnir) && !isset($tournament)) {
    $turnir = getTurnir();
}

if(!isset($turnir)) {
    $turnir = getTurnir($tournament);
}


// Формируем ссылку. Берем данные из адресной строки
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Разбиваем путь на части
$urlPathParts = explode('/', trim($urlPath, '/')); 
// Получаем текущие GET-параметры
$queryString = $_SERVER['QUERY_STRING'];

// Получаем массив лиг
$leagues = getLeagues($turnir);

// Обрабатываем каждую лигу. Формируем ссылку исходя из адресной строки и добавляем в массив leagues.
foreach ($leagues as $key => $league) {
  
    // Первая часть uri всегда будет слаг текущией лиги
    $urlPathParts[0] = $league['slug'];

    // Собираем новый путь
    $newPath = implode('/', $urlPathParts);

    // Добавляем GET-параметры, если они есть
    $myLink = $queryString ? $newPath . '?' . $queryString : $newPath;

    // Добавляем ссылку в массив лиг
    $leagues[$key]['link'] = $myLink;

    if (preg_match('/^(.+?)\s*(\(.+\))$/u', $league['full_name'], $matches)) {
        $leagues[$key]['name'] = trim($matches[1]); // "Суперліга"
        $leagues[$key]['locale_name'] = str_replace(["(", ")"], "", trim($matches[2])); // "(Футзал)"
    }

    
}



require_once VIEWS . '/leagues.tpl.php';

