<?php
$nenadacss = 1;

if(isset($_GET['foo'])) {
    
    include_once "head.php";
    include_once "slider_spons.php";
    include_once "menu.php";
    include_once "ligi.php";
    include_once "goroda.php";
    include_once "glavnaya.php";
    include_once "footer.php";

} 


if(!isset($_GET['foo'])) {    
    include_once CONTROLLERS . "/main.php";
}