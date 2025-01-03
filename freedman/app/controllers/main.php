<?php
// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);



 include_once CONTROLLERS . "/head.php";
 include_once HOME . "/slider_spons.php";
 include_once CONTROLLERS . "/menu.php";   
 include_once CONTROLLERS . "/leagues.php";
 include_once CONTROLLERS . "/rating_players.php";
 include_once CONTROLLERS . "/table.php";
 include_once CONTROLLERS . "/calendar_of_matches.php";
 include_once CONTROLLERS . "/controls.php";
 include_once CONTROLLERS . "/disqualification.php";
 include_once CONTROLLERS . "/footer.php";