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

// Экшин календарей матчей. Это кнопочки желтые или белые кнопочки с  месяцем и числом.
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
        // Получаем массив с датами каждого тура
        $dateTurs = getDateTurs($turnir);
        // Добавляем элемент link в массив
        $dateTurs = addLinkItem($dateTurs);

        
        // Находим дату выбранного турнира
        $dateLastTurString = '';
        foreach($dateTurs as $dateT){
            if($dateT['tur'] == $currentTur) {
                $dateLastTurString = $dateT['min_date'];
            }
        }

        // Преобразуем строку в объект даты
        $dateLastTur = new DateTime($dateLastTurString);

        // Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
        $dateLastTur->modify('+5 days');

        // Текущая дата и время
        $currentDate = new DateTime();



        if($currentTur <= $lastTur && !empty($dataAllPlayers)) {
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


        //-----------------------------------//

        // Данные тура
        $dataCurrentTur = getDataCurrentTur( $turnir, $currentTur);
            
        // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);

        $dataMatch = [];
        
        foreach ($dataCurrentTurWithDate as $match) {
            if($match['id'] == $dataCurrentTurWithDate[0]['id']){
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

        
        // количество побед, ничих, количество голов
        $team1Wins = 0;
        $team2Wins = 0;
        $draws = 0;
        $countGoals1 = 0;
        $countGoals2 = 0;

        

        // foreach($historyMeets as $match){
        //     if($match['goals1'] > $match['goals2']) {
        //         $dataMatch['team1_name'] == $match['team1_name'] ? $team1Wins ++ : $team2Wins ++; 
        //     } elseif ($match['goals1'] < $match['goals2']) {
        //         ($dataMatch['team1_name'] == $match['team1_name']) ? $team2Wins ++ : $team1Wins ++; 
        //     } else {
        //         $draws ++;
        //     }
        //     $countGoals1 += $match['goals1'];
        //     $countGoals2 += $match['goals2'];
        // }

        foreach($historyMeets as $match){
            if($match['goals1'] > $match['goals2']) {
              (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $team1Wins ++ : $team2Wins ++; 
            } elseif ($match['goals1'] < $match['goals2']) {
              (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $team2Wins ++ : $team1Wins ++; 
            } else {
                $draws ++;
            }
            (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $countGoals1 += $match['goals1'] : $countGoals2 += $match['goals1'];
            (strcasecmp(cleanString($dataMatch['team2_name']), cleanString($match['team2_name'])) === 0) ? $countGoals2 += $match['goals2'] : $countGoals1 += $match['goals2'];
        }

        // Находим прогноз на будущие матчи
        // Рассчитываем общее количество матчей
        $totalMatches = count($historyMeets);

        // Вычисляем проценты
        $team1WinPercent = $totalMatches == 0 ? 0 : ($team1Wins / $totalMatches) * 100;
        $drawPercent = $totalMatches == 0 ? 0 : ($draws / $totalMatches) * 100;
        $team2WinPercent = $totalMatches == 0 ? 0 : ($team2Wins / $totalMatches) * 100;

        // Проверяем и корректируем проценты
        $minimumPercent = 10;

        // Список для перераспределения
        $percentages = [
            'team1Win' => $team1WinPercent,
            'draw' => $drawPercent,
            'team2Win' => $team2WinPercent,
        ];

        // Найти, какие значения меньше минимального
        $totalReduction = 0;
        foreach ($percentages as $key => &$percent) {
            if ($percent < $minimumPercent) {
                $totalReduction += $minimumPercent - $percent;
                $percent = $minimumPercent;
            }
        }
        unset($percent);

        // Перераспределить излишек между остальными
        $remainingKeys = array_keys(array_filter(
            $percentages, 
            function ($p) use ($minimumPercent) { 
                
                return $p > $minimumPercent;
            } 
        ));
        if (count($remainingKeys) > 0) {
            foreach ($remainingKeys as $key) {
                $percentages[$key] -= $totalReduction / count($remainingKeys);
            }
        }

        //-----------------------------------//



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
        
        try {
            ob_start();
            require_once VIEWS . '/calendar_of_matches.tpl.php';
            $section1Content = ob_get_clean();
        } catch (Exception $e) {
            $section1Content = '<p>Ошибка загрузки данных</p>';
        }
        if($currentTur <= $lastTur && $dateLastTur <= $currentDate){
            try {
                ob_start();
                require VIEWS . '/controls_content.tpl.php';
                $section2Content = ob_get_clean();
            } catch (Exception $e) {
                $section2Content = '<p>Ошибка загрузки данных</p>';
            }
        } else {
            $section2Content = "";
        }
        // Отправьте JSON-ответ
        echo json_encode([
            'success' => true,
            'section1' => $section1Content,
            'section2' => $section2Content
        ]);
        
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

        
        // количество побед, ничих, количество голов
        $team1Wins = 0;
        $team2Wins = 0;
        $draws = 0;
        $countGoals1 = 0;
        $countGoals2 = 0;

        

        foreach($historyMeets as $match){
            if($match['goals1'] > $match['goals2']) {
              (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) 
              ? $team1Wins ++ 
              : $team2Wins ++; 
            } elseif ($match['goals1'] < $match['goals2']) {
              (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $team2Wins ++ : $team1Wins ++; 
            } else {
                $draws ++;
            }
            (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $countGoals1 += $match['goals1'] : $countGoals2 += $match['goals1'];
            (strcasecmp(cleanString($dataMatch['team2_name']), cleanString($match['team2_name'])) === 0) ? $countGoals2 += $match['goals2'] : $countGoals1 += $match['goals2'];
            
        }
        
        // Находим прогноз на будущие матчи
        // Рассчитываем общее количество матчей
        $totalMatches = count($historyMeets);

        // Вычисляем проценты
        $team1WinPercent = $totalMatches == 0 ? 0 : ($team1Wins / $totalMatches) * 100;
        $drawPercent = $totalMatches == 0 ? 0 : ($draws / $totalMatches) * 100;
        $team2WinPercent = $totalMatches == 0 ? 0 : ($team2Wins / $totalMatches) * 100;

        // Проверяем и корректируем проценты
        $minimumPercent = 10;

        // Список для перераспределения
        $percentages = [
            'team1Win' => $team1WinPercent,
            'draw' => $drawPercent,
            'team2Win' => $team2WinPercent,
        ];

        // Найти, какие значения меньше минимального
        $totalReduction = 0;
        foreach ($percentages as $key => &$percent) {
            if ($percent < $minimumPercent) {
                $totalReduction += $minimumPercent - $percent;
                $percent = $minimumPercent;
            }
        }
        unset($percent);

        // Перераспределить излишек между остальными
        $remainingKeys = array_keys(array_filter(
            $percentages, 
            function ($p) use ($minimumPercent) { 
                
                return $p > $minimumPercent;
            } 
        ));
        if (count($remainingKeys) > 0) {
            foreach ($remainingKeys as $key) {
                $percentages[$key] -= $totalReduction / count($remainingKeys);
            }
        }

    
        require_once VIEWS . "/anons.tpl.php";
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
            "assist" => ["icon" => "boots-icon.png", "icon_desc" => "Асистент"], 
            "goal" => ["icon" => "football-icon.png", "icon_desc" => "Гол"],
            "yellow_card" =>["icon" => "yellow-card-icon.png", "icon_desc" => "Жовта картка"],
            "red_card" => ["icon" => "red-card-icon.png", "icon_desc" => "Червона картка"],
            "yellow_red_card" => ["icon" => "yellow-red-icon.png", "icon_desc" => "Жовто-червона картка"],
            "autogoal" => ["icon" => "red-football-icon.png", "icon_desc" => "Автогол"],
            "penalty_success" => ["icon" => "football-penalty-6m.png", "icon_desc" => "Вдале пенальті"],
            "penalty_fail" => ["icon" => "football-cross-penalty-6m.png", "icon_desc" => "Невдале пенальті"],
        ];

        $bestPlayerOfMatch = getBestPlayerOfMatch($data['match_id']);

        foreach($matchReport as &$report){
            if($report['event_type'] == 'penalty' && $report['status_penalty'] === '1' ){
                $report['event_type'] = 'penalty_success';
            }
            if($report['event_type'] == 'penalty' && $report['status_penalty'] === '0' ){
                $report['event_type'] = 'penalty_fail';
            }
        }        

        // Состав команды
        $team1Composition = getTeamComposition($data['match_id'], $dataMatch['team1_id']);
        $team2Composition = getTeamComposition($data['match_id'], $dataMatch['team2_id']);       

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

        // Стастистика матча
        $staticMatch = getStaticMatch($data['match_id'], $dataMatch['team1_id'], $dataMatch['team2_id']);
        
        
        if(isset($staticMatch['team1']['data']['match_date'])) {
            
            // Преобразуем строку в объект даты
            $matchDate = new DateTime($staticMatch['team1']['data']['match_date']);

            // Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
            $matchDate->modify('+5 days');
            
        } else {
            $matchDateSql = $dbF->query("SELECT `date` FROM `v9ky_match` WHERE `id` = :id", [":id" => $data['match_id']])->find();
            
            $matchDate = new DateTime($matchDateSql['date']);
            $matchDate->modify('+5 days');
        }

        // Текущая дата и время
        $currentDate = new DateTime();

        require_once VIEWS . "/match_stats.tpl.php";
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
    
        require_once VIEWS . "/kkd.tpl.php";
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
    
        require_once VIEWS . "/preview.tpl.php";
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
    
        require_once VIEWS . "/video.tpl.php";
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
    
        require_once VIEWS . "/photo.tpl.php";
        die;
    }
}
// Этот аякс приходит со страницы /match_calendar
if( isset($data['action']) && $data['action'] == 'match_calendar' ) {





    $lastTur = $data['lasttur'];
    $currentTur = $data['tur'];
    $turnir = $data['turnir'];
    $url = $data['url'];

    if(!isset($turnir) && !isset($tournament)){
        $turnir = getTurnir();
    }
    if(!isset($turnir)){
        $turnir = getTurnir($tournament);
    }
    
    
    $dateTurs = getDateTurs($turnir);
      
    // Добавляем элемент link в массив
    $dateTurs = addLinkItem($dateTurs, $url);
        
    // Данные тура
    $dataCurrentTur = getDataCurrentTur($turnir, $currentTur);

    // Создаем массив для группировки
    $groupedData = [];

    // Проходим по исходному массиву
    foreach ($dataCurrentTur as $item) {
        // Получаем дату без времени
        $dateWithoutTime = (new DateTime($item['date']))->format('Y-m-d');

        // Группируем по дате
        if (!isset($groupedData[$dateWithoutTime])) {
            $groupedData[$dateWithoutTime] = [];
        }

        $groupedData[$dateWithoutTime][] = $item;
    }

    $tshirtImages = [
        0 => IMAGES . "/t-shirt/azure-shirt.png",
        1 => IMAGES . "/t-shirt/yellow-shirt.png",
        2 => IMAGES . "/t-shirt/white-shirt.png",
        3 => IMAGES . "/t-shirt/azure-shirt.png",
        4 => IMAGES . "/t-shirt/blue-shirt.png",
        5 => IMAGES . "/t-shirt/red-shirt.png",
        6 => IMAGES . "/t-shirt/azure-shirt.png",
        7 => IMAGES . "/t-shirt/orange-shirt.png",
        8 => IMAGES . "/t-shirt/rose-shirt.png",
        9 => IMAGES . "/t-shirt/azure-shirt.png",
        10 => IMAGES . "/t-shirt/azure-shirt.png",
        11 => IMAGES . "/t-shirt/azure-shirt.png",
        12 => IMAGES . "/t-shirt/azure-shirt.png",
    ];

    require_once VIEWS . "/match_calendar_content.tpl.php";
    die;
    
}

if(isset($data['action']) && $data['action'] == 'green_zone' ) {
    
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
        
        // Получаем массив с датами каждого тура
        $dateTurs = getDateTurs($turnir);

        // Добавляем элемент link в массив
        $dateTurs = addLinkItem($dateTurs);
        
        // Находим дату выбранного турнира
        $dateLastTurString = '';
        foreach($dateTurs as $dateT){
            if($dateT['tur'] == $currentTur) {
                $dateLastTurString = $dateT['min_date'];
            }
        }

        // Преобразуем строку в объект даты
        $dateLastTur = new DateTime($dateLastTurString);

        // Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
        $dateLastTur->modify('+5 days');

        // Текущая дата и время
        $currentDate = new DateTime();



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

        // // Добавляем два элемента в массивы - форматированная дата и время матча.
        $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);   
        
        require_once VIEWS . '/calendar_of_matches_green_zone.tpl.php';       
        
        die;
    }
}

if(isset($data['action']) && $data['action'] == 'after-play' ) {
    
    $video = array();

    if(!empty($data['link'])){

        $video['link'] = $data['link'];
        $video['title'] = 'Після гри';
        $video['width'] = '560';
        $video['height'] = '315';
        require_once VIEWS . '/youtube_frame.tpl.php';        
        die;

    }
}

if(isset($data['action']) && $data['action'] == 'top-goals' ) {
    
    $video = array();

    if(!empty($data['link'])){

        $video['link'] = $data['link'];
        $video['title'] = 'Топ гол';
        $video['width'] = '560';
        $video['height'] = '315';
        require_once VIEWS . '/youtube_frame.tpl.php';        
        die;
        
    }
}

if(isset($data['action']) && $data['action'] == 'top-save' ) {
    
    $video = array();

    if(!empty($data['link'])){

        $video['link'] = $data['link'];
        $video['title'] = 'Топ сейв';
        $video['width'] = '560';
        $video['height'] = '315';
        require_once VIEWS . '/youtube_frame.tpl.php';        
        die;
        
    }
}