<?php 

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

/**
* Все данные страницы основывауються от переменной turnir, которая береться из tournament. 
* А tournament береться с адресной строки - это название турнира, написаное латиницей. 
* Код разбора адресной сроки находиться в index.php на 75 строке
*/


require_once VIEWS . '/leagues.tpl.php';

