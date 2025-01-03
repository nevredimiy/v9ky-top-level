<?php

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

require_once CONTROLLERS . '/head.php';
require_once CONTROLLERS . '/menu.php';

$fields = getFields();


require_once VIEWS . '/stadiums.tpl.php';

require_once CONTROLLERS . '/footer.php';