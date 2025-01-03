<?php


// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


require_once dirname(__DIR__) . '/../../freedman/config.php';
require_once CORE . '/classes/DbFreedman.php';
$db_config = require_once FREEDMAN . '/db.php';
$dbFreedman = DbFreedman::getInstance();
$dbF = $dbFreedman->getConnection($db_config);
require_once CORE . '/helpers.php';
require_once CORE . '/functions.php';

$data = json_decode(file_get_contents('php://input'), true);

// Акшин календарей матчей. Это кнопочки желтые или белые кнопочки с  месяцем и числом.
if(isset($data['action']) && $data['action'] == 'calendar_of_matches' ) {
    
    if($data['tur'] && $data['turnir']) {

        // Когда tournament пустая это ознчает, что в адресной строке нет названия тура. Обычно это надпись после слеша в адресной строке
        // Если переменная tournament пустая, то заполняем ее из последнего сезона первым туром
        if (!isset($tournament)) {
       
            $tournament = mb_substr( strstr( $_SERVER["REQUEST_URI"], "?", true ), 1 );

            if(!$tournament) {
                $tournament = getTournament();
            }
        }        

        $lastTur = $data['lasttur'];
        $currentTur = $data['tur'];
        $turnir = $data['turnir'];
        
        $allStaticPlayers = getAllStaticPlayers($turnir);
        $dataAllPlayers = getDataPlayers($allStaticPlayers);
        $dateTurs = getDateTurs($turnir);

        if($currentTur <= $lastTur) {
            // Все игроки из выбранного тура
            $bestPlayers = getPlayersOfTur($allStaticPlayers, $currentTur);

            // Лучшие игроки - отфильтрованные
            $bestPlayersForTable = mergeStaticAndData($bestPlayers, $dataAllPlayers);


            $labels = [
                'topgravetc' => ['icon' => 'star-icon.png', 'role' => 'Топ-Гравець'], 
                'golkiper' => ['icon' => 'gloves-icon.png', 'role' => 'Топ-Голкіпер'], 
                'bombardir' => ['icon' => 'football-icon.png', 'role' => 'Топ-Бомбардир'], 
                'asistent' => ['icon' => 'boots-icon.svg', 'role' => 'Топ-Асистент'],
                'zahusnuk' => ['icon' => 'pitt-icon.svg', 'role' => 'Топ-Захисник'],
                'dribling' => ['icon' => 'player-icon.svg', 'role' => 'Топ-Дриблінг'],
                'udar' => ['icon' => 'rocket-ball-icon.png', 'role' => 'Топ-Удар'],
                'pas' => ['icon' => 'ball-icon.png', 'role' => 'Топ-Пас'],
            ];
        }

        $dataCurrentTur = getDataCurrentTur($turnir, $currentTur);

        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);   
        
        require_once '../views/calendar_of_matches.tpl.php';
        
        die;
    }
}



if(isset($data['action']) && $data['action'] == 'anons' ) {

    if($data['match_id'] && $data['tur'] && $data['turnir'] ) { 
        
        // Данные тура
        $dataCurrentTur = getDataCurrentTur($data['turnir'], $data['tur']);
    
        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);
    
        $dataMatch = [];
        
        foreach ($dataCurrentTurWithDate as $match) {
            if($match['id'] == $data['match_id']){
                $dataMatch['team1_name'] = $match['team1_name'];
                $dataMatch['team1_photo'] = $match['team1_photo'];
                $dataMatch['team2_name'] = $match['team2_name'];
                $dataMatch['team2_photo'] = $match['team2_photo'];
                $dataMatch['anons'] = $match['anons'];
                $dataMatch['goals1'] = $match['goals1'];
                $dataMatch['goals2'] = $match['goals2'];
                break;
            }
        }


        $historyMeets = getHistoryMeets($dataMatch['team1_name'], $dataMatch['team2_name']);

    
        require_once "../views/anons.tpl.php";
        die;
    }
}


if(isset($data['action']) && $data['action'] == 'match_stats' ) {

    if($data['match_id'] && $data['tur'] && $data['turnir'] ) { 
        
        // Данные тура
        $dataCurrentTur = getDataCurrentTur($data['turnir'], $data['tur']);
    
        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);
    
        $dataMatch = [];
        
        foreach ($dataCurrentTurWithDate as $match) {
            if($match['id'] == $data['match_id']){
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
                break;
            }
        }

        // Протокол матча
        $matchReport = getMatchReport($data['match_id']);
        $eventType = [
            "assist" => "boots-icon.svg", 
            "goal" => "football-icon.png", 
            "yellow_card" => "yellow-card-icon.svg", 
            "red_card" => "red-card-icon.svg", 
            "yellow_red_card" => "yellow-card-icon.svg"
        ];

        // Состав команды
        $team1Composition = getTeamComposition($data['match_id'], $dataMatch['team1_id']);
        $team2Composition = getTeamComposition($data['match_id'], $dataMatch['team2_id']);

        // Получение тренера и менеджера
        $trainerAndManager1 = getTrainerAndManager($dataMatch['team1_id']);
        $trainerAndManager2 = getTrainerAndManager($dataMatch['team2_id']);
    
        require_once "../views/match_stats.tpl.php";
        die;
    }
}

if( isset($data['action']) && $data['action'] == 'kkd' ) {

    if( $data['match_id'] && $data['tur'] && $data['turnir'] ) { 
        
    
        // Данные тура
        $dataCurrentTur = getDataCurrentTur($data['turnir'], $data['tur']);
    
        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);
    
        $dataMatch = [];
        
        foreach ($dataCurrentTurWithDate as $match) {
            if($match['id'] == $data['match_id']){
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
                break;
            }
        }

        $teamCompositionAndStats1 = getTeamCompositionAndStats($data['match_id'], $dataMatch['team1_id']);
        $teamCompositionAndStats2 = getTeamCompositionAndStats($data['match_id'], $dataMatch['team2_id']);
    
        require_once "../views/kkd.tpl.php";
        die;
    }
}

if( isset($data['action']) && $data['action'] == 'preview' ) {

    if( $data['match_id'] && $data['tur'] && $data['turnir'] ) { 
        
    
        $dataCurrentTur = getDataCurrentTur($data['turnir'], $data['tur']);
    
        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);
    
        $dataMatch = [];
        
        foreach ($dataCurrentTurWithDate as $match) {
            if($match['id'] == $data['match_id']){
                $dataMatch['team1_name'] = $match['team1_name'];
                $dataMatch['team1_photo'] = $match['team1_photo'];
                $dataMatch['team2_name'] = $match['team2_name'];
                $dataMatch['team2_photo'] = $match['team2_photo'];
                $dataMatch['anons'] = $match['anons'];
                $dataMatch['goals1'] = $match['goals1'];
                $dataMatch['goals2'] = $match['goals2'];
                $dataMatch['season'] = $match['season'];
                $dataMatch['tur'] = $match['tur'];
                $dataMatch['field_name'] = $match['field_name'];
                $dataMatch['match_day'] = $match['match_day'];
                $dataMatch['match_time'] = $match['match_time'];
                $dataMatch['team1_id'] = $match['team1_id'];
                $dataMatch['team2_id'] = $match['team2_id'];
                break;
            }
        }

        $video = getVideo($data['match_id']);
    
        require_once "../views/preview.tpl.php";
        die;
    }
}

if( isset($data['action']) && $data['action'] == 'video' ) {

    if( $data['match_id'] && $data['tur'] && $data['turnir'] ) { 
        
    
        $dataCurrentTur = getDataCurrentTur($data['turnir'], $data['tur']);
    
        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);
    
        $dataMatch = [];
        
        foreach ($dataCurrentTurWithDate as $match) {
            if($match['id'] == $data['match_id']){
                $dataMatch['team1_name'] = $match['team1_name'];
                $dataMatch['team1_photo'] = $match['team1_photo'];
                $dataMatch['team2_name'] = $match['team2_name'];
                $dataMatch['team2_photo'] = $match['team2_photo'];
                $dataMatch['anons'] = $match['anons'];
                $dataMatch['goals1'] = $match['goals1'];
                $dataMatch['goals2'] = $match['goals2'];
                $dataMatch['season'] = $match['season'];
                $dataMatch['tur'] = $match['tur'];
                $dataMatch['field_name'] = $match['field_name'];
                $dataMatch['match_day'] = $match['match_day'];
                $dataMatch['match_time'] = $match['match_time'];
                $dataMatch['team1_id'] = $match['team1_id'];
                $dataMatch['team2_id'] = $match['team2_id'];
                break;
            }
        }

        $video = getVideo($data['match_id']);
    
        require_once "../views/video.tpl.php";
        die;
    }
}

if( isset($data['action']) && $data['action'] == 'photo' ) {

    if( $data['match_id'] && $data['tur'] && $data['turnir'] ) { 
        
    
        $dataCurrentTur = getDataCurrentTur($data['turnir'], $data['tur']);
    
        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);
    
        $dataMatch = [];
        
        foreach ($dataCurrentTurWithDate as $match) {
            if($match['id'] == $data['match_id']){
                $dataMatch['team1_name'] = $match['team1_name'];
                $dataMatch['team1_photo'] = $match['team1_photo'];
                $dataMatch['team2_name'] = $match['team2_name'];
                $dataMatch['team2_photo'] = $match['team2_photo'];
                $dataMatch['anons'] = $match['anons'];
                $dataMatch['goals1'] = $match['goals1'];
                $dataMatch['goals2'] = $match['goals2'];
                $dataMatch['season'] = $match['season'];
                $dataMatch['tur'] = $match['tur'];
                $dataMatch['field_name'] = $match['field_name'];
                $dataMatch['match_day'] = $match['match_day'];
                $dataMatch['match_time'] = $match['match_time'];
                $dataMatch['team1_id'] = $match['team1_id'];
                $dataMatch['team2_id'] = $match['team2_id'];
                break;
            }
        }

        $photoPath = PHOTO . "/{$data['match_id']}/";
        $photo = scandir($photoPath); 
    
        require_once "../views/photo.tpl.php";
        die;
    }
}