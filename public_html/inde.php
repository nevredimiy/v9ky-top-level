<?php
$nenadacss = 1;

if(!$_GET['foo']) {
    
    include_once "head.php";
    include_once "slider_spons.php";
    include_once "menu.php";
    //include_once "run_line.php";
    include_once "goroda.php";
    include_once "glavnaya.php";
    include_once "footer.php";

} 


if($_GET['foo']) {    
    include_once CONTROLLERS . "/main.php";
}