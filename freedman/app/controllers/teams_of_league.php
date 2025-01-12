<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

if(!isset($turnir)){
   isset($tournament) ? $turnir = getTurnir($tournament) : $turnir = getTurnir();
}

$teams = getTeamsOfLeague($turnir);

require_once CONTROLLERS . '/head.php';
require_once CONTROLLERS . '/leagues.php';

require  VIEWS . '/teams_of_league.tpl.php';

require_once CONTROLLERS . '/footer.php';
