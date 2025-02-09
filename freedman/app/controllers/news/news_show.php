<?php

if(isset($_GET['id'])) {
    $newsId = intval($_GET['id']);
    $news_show = getOneNews($newsId);
}

include_once CONTROLLERS . "/head.php"; 
include_once CONTROLLERS . "/leagues.php";
require_once VIEWS . '/news/news_show.tpl.php';
include_once CONTROLLERS . "/footer.php";