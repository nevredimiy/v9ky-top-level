<?php

// Включаем ошибки для отладки (перед отправкой на продакшен лучше отключить)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once dirname(__DIR__) . '/../../freedman/config.php';
require_once CORE . '/classes/DbFreedman.php';
$db_config = require_once FREEDMAN . '/db.php';
$dbFreedman = DbFreedman::getInstance();
$dbF = $dbFreedman->getConnection($db_config);
require_once CORE . '/helpers.php';
require_once CORE . '/functions.php';


// Проверяем, был ли передан match_id
if (!isset($_POST['match_id'])) {
    die("<p>Помилка: відсутній ідентифікатор матчу</p>");
}

$match_id = intval($_POST['match_id']); // Приводим к числу


// Получаем данные матча
$fields = $dbF->query("SELECT `turnir`, `tur`, `team1`, `team2` FROM `v9ky_match` WHERE id = :match_id", [":match_id" => $match_id])->find();

// Проверка: если матч не найден
if (!$fields) {
    die("<p>Помилка: матч не знайдено</p>");
}

// Данные тура
$dataCurrentTur = getDataCurrentTur($fields['turnir'], $fields['tur']);

// Добавляем два элемента в массивы - форматированная дата и время матча.
$dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);

$dataMatch = [];

foreach ($dataCurrentTurWithDate as $match) {
    if($match['id'] == $match_id){
        $dataMatch['team1_name'] = $match['team1_name'];
        $dataMatch['team1_photo'] = $match['team1_photo'];
        $dataMatch['team2_name'] = $match['team2_name'];
        $dataMatch['team2_photo'] = $match['team2_photo'];
        $dataMatch['goals1'] = $match['goals1'];
        $dataMatch['goals2'] = $match['goals2'];
        $dataMatch['season'] = $match['season'];
        $dataMatch['tur'] = $match['tur'];
        $dataMatch['field_name'] = $match['field_name'];
        $dataMatch['match_day'] = $match['match_day'];
        $dataMatch['match_time'] = $match['match_time'];
        $dataMatch['team1_id'] = $match['team1_id'];
        $dataMatch['team2_id'] = $match['team2_id'];
        $dataMatch['canseled'] = $match['canseled'];

        break;
    }
}



// Протокол матча
$matchReport = getMatchReport($match_id);


$eventType = [
    "assist" => ["icon" => "boots-icon.png", "icon_desc" => "Асистент"], 
    "goal" => ["icon" => "football-icon.png", "icon_desc" => "Гол"],
    "yellow_card" =>["icon" => "yellow-card-icon.png", "icon_desc" => "Жовта картка"],
    "red_card" => ["icon" => "red-card-icon.png", "icon_desc" => "Червона картка"],
    "yellow_red_card" => ["icon" => "yellow-red-icon.png", "icon_desc" => "Жовто-червона картка"],
    "autogoal" => ["icon" => "red-football-icon.png", "icon_desc" => "Автогол"],
    "penalty_success" => ["icon" => "football-penalty-6m.png", "icon_desc" => "Вдале пенальті"],
    "penalty_fail" => ["icon" => "football-cross-penalty-6m.png", "icon_desc" => "Невдале пенальті"],
];

$bestPlayerOfMatch = getBestPlayerOfMatch($match_id);

foreach($matchReport as &$report){
    if($report['event_type'] == 'penalty' && $report['status_penalty'] === '1' ){
        $report['event_type'] = 'penalty_success';
    }
    if($report['event_type'] == 'penalty' && $report['status_penalty'] === '0' ){
        $report['event_type'] = 'penalty_fail';
    }
} 

// Состав команды
$team1Composition = getTeamComposition($match_id, $dataMatch['team1_id']);
$team2Composition = getTeamComposition($match_id, $dataMatch['team2_id']);  

// Получение тренера и менеджера
$trainerAndManager1 = [];
$trainerAndManager2 = [];

$managerName1 = getManagerName($dataMatch['team1_id']);
$trainerName1 = getTrainerName($dataMatch['team1_id']);
$managerName2 = getManagerName($dataMatch['team2_id']);
$trainerName2 = getTrainerName($dataMatch['team2_id']);

if(!empty($managerName1) || !empty($trainerName1)) {
    if($managerName1[0]['player_id'] == $trainerName1[0]['player_id']){
        $trainerAndManager1[] = $managerName1[0];
    }else {
        $trainerAndManager1['manager'] = $managerName1[0];
        $trainerAndManager1['trainer'] = $trainerName1[0];
    }
}

if(!empty($managerName2) || !empty($trainerName2)) {
    if($managerName2[0]['player_id'] == $trainerName2[0]['player_id']){
        $trainerAndManager2[] = $managerName2[0];
    }else {
        $trainerAndManager2['manager'] = $managerName2[0];
        $trainerAndManager2['trainer'] = $trainerName2[0];
    }
}


// Определяем массив параметров статистики
$statsList = [
    'total_udar' => ['title' => 'Кількість ударів', 'percentage_key' => 'udar_percentage_team'],
    'total_vstvor' => ['title' => 'Удари в площину воріт', 'percentage_key' => 'vstvor_percentage_team'],
    'total_mimo' => ['title' => 'Удари повз ворота', 'percentage_key' => 'mimo_percentage_team'],
    'total_pasplus' => ['title' => 'Вдалі паси', 'percentage_key' => 'pasplus_percentage_team'],
    'total_pasminus' => ['title' => 'Невдалі паси', 'percentage_key' => 'pasminus_percentage_team'],
    'total_otbor' => ['title' => 'Відбір м\'яча', 'percentage_key' => 'otbor_percentage_team'],
    'total_obvodkaplus' => ['title' => 'Вдалий дриблінг', 'percentage_key' => 'obvodkaplus_percentage_team'],
    'total_obvodkaminus' => ['title' => 'Невдалий дриблінг', 'percentage_key' => 'obvodkaminus_percentage_team'],
    'total_seyv' => ['title' => 'Кількість сейвів', 'percentage_key' => 'seyv_percentage_team']
];

// Стастистика матча
$staticMatch = getStaticMatch($match_id, $dataMatch['team1_id'], $dataMatch['team2_id']);

// dump($staticMatch);


if(isset($staticMatch['team1']['data']['match_date'])) {
    
    // Преобразуем строку в объект даты
    $matchDate = new DateTime($staticMatch['team1']['data']['match_date']);

    // Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
    $matchDate->modify('+4 days 21 hours');
    
} else {
    $matchDateSql = $dbF->query("SELECT `date` FROM `v9ky_match` WHERE `id` = :id", [":id" => $match_id])->find();
    
    $matchDate = new DateTime($matchDateSql['date']);
    $matchDate->modify('+4 days 21 hours');
}

// Текущая дата и время
$dateNow = $currentDate = new DateTime();

require_once VIEWS . "/actions/action_team_page.tpl.php";
die;
