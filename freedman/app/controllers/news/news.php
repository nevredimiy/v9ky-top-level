<?php

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

require_once CORE . '/classes/Pagination.php';

$per_page = 12;
$total_count = (int)getCountNews();
$total = ceil($total_count / $per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) {
    $page = 1;
}

if($page > $total) {
    $page = $total;
}

$start = ($page - 1) * $per_page;

// var_dump($per_page, $total_count, $total, $page, $start);

$news = getNews($start, $per_page);


$pagination = new Pagination((int)$page, $per_page, $total);

$start1 = $pagination->getStart();




// foreach($news as $item) {
//     echo '<br>';
//     echo $item['id'];
// }
// echo '<br>';
// echo '<br>';
// echo '<br>';
// echo "<a href='?page=1'> |< </a> ";
// $pre_page = $page - 1;
// if($pre_page < 1) {
//     $pre_page = 1;
// }
// echo "<a href='?page={$pre_page}'> < </a> ";
// for ($i = 1; $i <= $pages_cnt; $i++) {

//     echo "<a href='?page={$i}'>{$i}</a> ";
// }

include_once CONTROLLERS . "/head.php"; 
include_once CONTROLLERS . "/leagues.php";
require_once VIEWS . '/news/news.tpl.php';
include_once CONTROLLERS . "/footer.php";