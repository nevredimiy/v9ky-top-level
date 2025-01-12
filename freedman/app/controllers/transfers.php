<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


if(!isset($turnir) && !isset($tournament)){
    $turnir = getTurnir();
}
if(!isset($turnir)){
    $turnir = getTurnir($tournament);
}


$transfers = getTransfers($turnir);

require_once CONTROLLERS . "/head.php";
require_once CONTROLLERS . "/leagues.php";

require_once VIEWS . "/transfers.tpl.php";

require_once CONTROLLERS . "/footer.php";