<?php
if(!isset($turnir)){
    $turnir = getTurnir();
}

if(isset($currentTur)){
    // Выбранный тур
    $currentTur = $lastTur != '' ? $lastTur : 1;
    if(isset($_GET['tur'])){
        // Берем тур из адресной строки
        $currentTur = $_GET['tur'];
    }
}

$resultOfTur = getResultOfTur($turnir, $currentTur);
$resultTur = [];

if(isset($resultOfTur['url1'])){
    if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $resultOfTur['url1'], $matches)) {
        $videoId = $matches[1];
        $resultTur['after_play'] = $videoId;
    } 
}

if(isset($resultOfTur['url2'])){
    if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $resultOfTur['url2'], $matches)) {
        $videoId = $matches[1];
        $resultTur['top_goals'] = $videoId;
    } 
}

if(isset($resultOfTur['url3'])){
    if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $resultOfTur['url3'], $matches)) {
        $videoId = $matches[1];
        $resultTur['top_save'] = $videoId;
    } 
}
   
$randomNews = getRandomNews();

include_once VIEWS . '/controls1.tpl.php';
