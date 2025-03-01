<?php
// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

 include_once CONTROLLERS . "/head.php";
 
 include_once CONTROLLERS . "/leagues.php";
 include_once CONTROLLERS . "/rating_players.php";
 include_once CONTROLLERS . "/table.php";

function getLastActiveDate($turnir){
    global $dbF;

    $sql = "SELECT `date` FROM `v9ky_match` WHERE `turnir`= :turnir AND `canseled` = 1 ORDER by `date` DESC LIMIT 1";

    $fields = $dbF->query($sql, [":turnir" => $turnir])->find();

    return($fields['date']);

}

 if(!isset($tournament) || empty($tournament)){
    $tournament = getTournament();
 }

 if(!isset($turnir)){
    $turnir = getTurnir($tournament);
 }

 if(isset($_GET['first_day'])) {
    $selectedDate = $_GET['first_day'];
} else {
    $selectedDate = date("Y-m-d", strtotime(getLastActiveDate($turnir)));
}

$dateNow = new DateTime();
 
// Данные матча выбранного турнира
$sql = "SELECT 
    DATE(`date`) as `match_date`,
    tur,
    turnir,
    canseled
    FROM `v9ky_match` 
    WHERE turnir = :turnir 
    ORDER BY `date` ASC";
$matches = $dbF->query($sql, [":turnir" => $turnir])->findAll();
 
// Извлекаем даты в отдельный массив
$dates = array_column($matches, 'match_date');
 
// Функция для форматирования дат
function formatMatchDates($dates) 
{
    // Масив назв місяців українською
    $months = [
        "01" => "Січень", "02" => "Лютий", "03" => "Березень", "04" => "Квітень",
        "05" => "Травень", "06" => "Червень", "07" => "Липень", "08" => "Серпень",
        "09" => "Вересень", "10" => "Жовтень", "11" => "Листопад", "12" => "Грудень"
    ];

    // Видаляємо дублікати дат
    $dates = array_unique($dates);

    // Перетворюємо строки дат у об'єкти DateTime
    $dates = array_map(function($date) {
        return new DateTime($date);
    }, $dates);

    // Сортуємо дати у зростаючому порядку
    usort($dates, function($a, $b) {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    });

    $result = []; // Масив для збереження згрупованих дат

    // Ініціалізація першої групи
    $current_group = [$dates[0]->format("d")]; // Додаємо перший день
    $first_day = $dates[0]->format("Y-m-d"); // Зберігаємо перший день у форматі YYYY-MM-DD
    $current_month = $dates[0]->format("m"); // Отримуємо місяць першої дати
    $current_month_name = $months[$current_month]; // Назва місяця

    // Проходимо по всіх датах, починаючи з другої
    for ($i = 1; $i < count($dates); $i++) {
        $prev_date = $dates[$i - 1]; // Попередня дата
        $current_date = $dates[$i];  // Поточна дата

        // Визначаємо різницю у днях між поточною та попередньою датою
        $interval = $prev_date->diff($current_date)->days;
        $current_month_new = $current_date->format("m"); // Отримуємо місяць поточної дати
        $current_month_name_new = $months[$current_month_new]; // Назва нового місяця

        if ($interval == 1) {
            // Якщо дата йде підряд, додаємо її в поточну групу
            $current_group[] = $current_date->format("d");
            $last_day = $current_date->format("Y-m-d"); // Оновлюємо останній день у форматі YYYY-MM-DD
            
            // Якщо змінився місяць, оновлюємо назву місяця
            if ($current_month != $current_month_new) {
                $current_month_name = $months[$current_month] . "-" . $current_month_name_new;
            }
        } else {
            // Якщо є розрив між датами, зберігаємо поточну групу та починаємо нову
            $last_day = count($current_group) > 1 ? $last_day : "0"; // Якщо лише одна дата в групі, last_day = 0
            $month_name = $current_month_name . " " . implode("-", $current_group); // Формуємо назву блоку
            $result[] = [
                'text' => $month_name,
                'first_day' => $first_day,
                'last_day' => $last_day,
                'date_first_day' => new DateTime($first_day),
                'text_month' => $current_month_name,
                'text_day' => implode("-", $current_group)
            ];

            // Починаємо нову групу з поточної дати
            $current_group = [$current_date->format("d")];
            $first_day = $current_date->format("Y-m-d"); // Оновлюємо перший день нової групи
            $current_month = $current_month_new; // Оновлюємо місяць
            $current_month_name = $months[$current_month]; // Оновлюємо назву місяця
            $last_day = "0"; // Скидаємо значення останнього дня
        }
    }

    // Додаємо останню групу дат у результат
    $last_day = count($current_group) > 1 ? $last_day : "0"; // Визначаємо останній день групи
    $month_name = $current_month_name . " " . implode("-", $current_group); // Формуємо назву останнього блоку
    $result[] = [
        'text' => $month_name,
        'first_day' => $first_day,
        'last_day' => $last_day,
        'date_first_day' => new DateTime($first_day),
        'text_month' => $current_month_name,
        'text_day' => implode("-", $current_group)
    ];

    return $result; // Повертаємо масив згрупованих дат
}

// Форматируем даты матчей для отображения в шаблоне
$dateMatches = formatMatchDates($dates);

/**
 * 
 * 
 * 
 * 
 * 
 */


// Получаем первый и последний день игр выбранной или текущей даты (тура)
function getFirstAndLastDays($dateMatches, $selectedDate)
{
    $firstDay = 0;
    $lastDay = 0;
    foreach($dateMatches as $match){
        if($match['first_day'] == $selectedDate || $match['last_day'] == $selectedDate){
            $firstDay = $match['first_day'];
            $lastDay = $match['last_day'];
        }
    }
    return ["first_day" => $firstDay, "last_day" => $lastDay];
}

$daysOfTur = getFirstAndLastDays($dateMatches, $selectedDate);

/**
 * @param integer - идентификатора турнира
 * @param integer - текущий выбранная дата
 * @return array 
 */
function getDataMatchesOfDate($turnir, $firstDay, $lastDay)
{
    if(!$firstDay) return 0;

    global $dbF;

    // Проверяем, задан ли last_day (если 0, то ищем матчи только на first_day)
    $dateCondition = ($lastDay === "0") 
        ? "DATE(m.`date`) = :first_day"
        : "DATE(m.`date`) BETWEEN :first_day AND :last_day";

    $sql = 
        "SELECT 
        m.id,
        m.turnir AS turnir_id,
        m.anons,
        t.season,
        m.date,
        m.tur, 
        m.tcolor1 AS color_tshirt1,
        t1.id AS team1_id,
        t1.name AS team1_name,
        t1.pict AS team1_photo,
        m.tcolor2 AS color_tshirt2,
        t2.id AS team2_id,
        t2.name AS team2_name,
        t2.pict AS team2_photo,
        m.field,
        f.name AS field_name,
        m.canseled,
        m.gols1 AS goals1,
        m.gols2 AS goals2,
        t.ru AS turnir_name,
        m.videohiden AS video_hd,
        m.video AS video,
        m.videobest AS videobest,
        m.video_intervu AS video_intervu,
        m.video_intervu2 AS video_intervu2
    FROM 
        v9ky_match m
    LEFT JOIN 
        `v9ky_team` t1 ON t1.id = m.team1
    LEFT JOIN
        `v9ky_team` t2 ON t2.id = m.team2
    LEFT JOIN
        `v9ky_turnir` t ON t.id = m.turnir
    LEFT JOIN
        `v9ky_fields` f ON f.id = m.field
    WHERE 
        m.`turnir` = :turnir 
        AND $dateCondition
    ORDER BY 
        m.date";

    // Подготавливаем параметры запроса
    $params = [":turnir" => $turnir, ":first_day" => $firstDay];
    
    // Если last_day не 0, добавляем его в параметры
    if ($lastDay !== "0") {
        $params[":last_day"] = $lastDay;
    }

    // Выполняем запрос в БД
    $fields = $dbF->query($sql, $params)->findAll();

    foreach($fields as $key => $field){
        $date = new DateTime($field['date']);
        // Устанавливаем локаль для русского языка
        setlocale(LC_TIME, 'uk_UA.UTF-8');

        // Форматируем дату
        $fields[$key]['match_day'] = strftime('%e %B (%a)', $date->getTimestamp());
        $fields[$key]['match_time'] = strftime('%H:%M', $date->getTimestamp());
    }
    
    return $fields;
}

$dataMatchesOfDate = 0;
if($daysOfTur['first_day']){
    $dataMatchesOfDate = getDataMatchesOfDate($turnir, $daysOfTur['first_day'], $daysOfTur['last_day']);
}

// Строка - дата последнего сыгранного матча в турнире (canseled = 1) (формат Y-m-d)
$dateLastTurString = $selectedDate;

// Преобразуем строку в объект даты
$dateLastTur = new DateTime($dateLastTurString);

// Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
$dateLastTur->modify('+5 days');


/**
 * Определяет "сборную тура" по номеру дате сыгранных матчей
 */
function getPlayersOfDateTur( $allStaticPlayers, $firstDay, $lastDay ){
    
    //  Массив игроков текущего тура
    $playerOfTur = [];
    // Массив для лучших игроков тура
    $bestPlayer = [];

    // Находим игроков текущего тура и записываем в массив с который будем фильтровать для 8 рубрик
    $playerOfTur = filterMatchesByDateRange($allStaticPlayers, $firstDay, $lastDay);   

    // добавление элемента с ключом 'player_total' в массив $playerOfTur
    foreach ($playerOfTur as $key => $item) {
        $playerOfTur[$key]['player_total'] = $item['count_goals'] * 15 
        + $item['count_asists'] * 10 
        + $item['zagostrennia'] * 10
        + $item['pasplus'] * 3 
        - $item['pasminus'] * 3 
        - $item['vtrata'] * 3 
        + $item['vstvor'] * 7 
        - $item['mimo'] * 4 
        + $item['obvodkaplus'] * 5 
        - $item['obvodkaminus'] * 3 
        + $item['otbor'] * 8 
        - $item['otbormin'] * 5 
        + $item['blok'] * 4 
        + $item['seyv'] * 15 
        - $item['seyvmin'] * 7;
        }  

    // --- Топ Игрок ---
    // Получаем массив игроков из рубрики Топ-Игрок
    $topgravetcs = getBestPlayers($playerOfTur, 'topgravetc');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($topgravetcs)) {
        $maxPlayerTotal = max(array_column($topgravetcs, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $topgravetcs = array_filter($topgravetcs, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });

    // берем только первого бомбардира
    $topgravetcs = array_slice($topgravetcs, 0, 1);

    // Результат записываем в основной массив
    foreach ($topgravetcs as $topgravetc) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($topgravetc['count_points'] > 0) {
        $bestPlayer[] = $topgravetc;
        }
    }    

    // --- Голкипер ---
    // Получаем массив лучших 
    $golkipers = getBestPlayers($playerOfTur, 'golkiper');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($golkipers)) {
        $maxPlayerTotal = max(array_column($golkipers, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $golkipers = array_filter($golkipers, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });

    // берем только первого бомбардира
    $golkipers = array_slice($golkipers, 0, 1);

    // Результат записываем в основной массив
    foreach ($golkipers as $golkiper) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($golkiper['count_points'] > 0) {
        $bestPlayer[] = $golkiper;
        }
    }

    // --- Бомбардир ---
    // Находим максимальное значение count_goals. 
    if(empty($playerOfTur)){
        $maxGoals = 0;
    } else {
        $maxGoals = max(array_column($playerOfTur, 'count_goals'));
    }

    // Отбираем все элементы с максимальным значением count_goals
    $bombardirs = array_filter($playerOfTur, function ($item) use ($maxGoals) {
        return $item['count_goals'] == $maxGoals;
    });

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($bombardirs)) {
        $maxPlayerTotal = max(array_column($bombardirs, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $bombardirs = array_filter($bombardirs, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });

    // Добавляем игроку ключ что он лучший в туре бомбардир
    foreach($bombardirs as $key => $res){
        $bombardirs[$key]['best_player'] = 'bombardir';
        $bombardirs[$key]['count_points'] = $res['count_goals'];
    }

    // берем только первого бомбардира
    $bombardirs = array_slice($bombardirs, 0, 1);

    // Результат записываем в основной массив
    foreach ($bombardirs as $bombardir) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($bombardir['count_points'] > 0) {
        $bestPlayer[] = $bombardir;
        }
    }    

    // --- Асистент ---
    // Находим максимальное значение count_asists. 
    if(empty($playerOfTur)){
        $maxAsist = 0;
    } else {
        $maxAsist = max(array_column($playerOfTur, 'count_asists'));
    }

    // Отбираем все элементы с максимальным значением count_goals
    $asists = array_filter($playerOfTur, function ($item) use ($maxAsist) {
        return $item['count_asists'] == $maxAsist;
    });


    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($asists)) {
        $maxPlayerTotal = max(array_column($asists, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $asists = array_filter($asists, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });

    // Добавляем игроку ключ что он лучший в туре бомбардир
    foreach($asists as $key => $res){
        $asists[$key]['best_player'] = 'asistent';
        $asists[$key]['count_points'] = $res['count_asists'];
    }

    // берем только первого ассиста
    $asists = array_slice($asists, 0, 1);

    // Результат записываем в основной массив
    foreach ($asists as $asist) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($asist['count_points'] > 0) {
        $bestPlayer[] = $asist;
        }
    }

    // --- Захисник ---
    // Получаем массив лучших защитников
    $zahusnuks = getBestPlayers($playerOfTur, 'zahusnuk');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($zahusnuks)) {
        $maxPlayerTotal = max(array_column($zahusnuks, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $zahusnuks = array_filter($zahusnuks, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });


    // берем только первого ассиста
    $zahusnuks = array_slice($zahusnuks, 0, 1);

    // Результат записываем в основной массив
    foreach ($zahusnuks as $zahusnuk) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($zahusnuk['count_points'] > 0) {
        $bestPlayer[] = $zahusnuk;
        }
    }

    // --- Дриблинг ---
    // Получаем массив лучших 
    $driblings = getBestPlayers($playerOfTur, 'dribling');

        // Находим максимальное значение player_total среди отобранных бомбардиров
        if (!empty($driblings)) {
        $maxPlayerTotal = max(array_column($driblings, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $driblings = array_filter($driblings, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });


    // Результат записываем в основной массив
    foreach ($driblings as $dribling) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($dribling['count_points'] > 0) {
        $bestPlayer[] = $dribling;
        }
    }

    // --- Удар ---
    // Получаем массив лучших 
    $udars = getBestPlayers($playerOfTur, 'udar');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($udars)) {
        $maxPlayerTotal = max(array_column($udars, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $udars = array_filter($udars, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });

    // берем только первого ассиста
    $udars = array_slice($udars, 0, 1);

    // Результат записываем в основной массив
    foreach ($udars as $udar) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($udar['count_points'] > 0) {
        $bestPlayer[] = $udar;
        }
    }

    // --- Пас ---
    // Получаем массив лучших 
    $pases = getBestPlayers($playerOfTur, 'pas');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($pases)) {
        $maxPlayerTotal = max(array_column($pases, 'player_total'));
    } else {
        $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $pases = array_filter($pases, function ($item) use ($maxPlayerTotal) {
        return $item['player_total'] == $maxPlayerTotal;
    });

    // берем только первого ассиста
    $pases = array_slice($pases, 0, 1);

    // Результат записываем в основной массив
    foreach ($pases as $pas) {
        // Если у игрока количество очков ноль, то это не топ игрок
        if($pas['count_points'] > 0) {
        $bestPlayer[] = $pas;
        }
    }
    return $bestPlayer;  
}

/**
 * Фильтрует общий массив статистики ($allStaticPlayer). Берет статистику игрока только из матчей заданого времени
 * Filters the general array of statistics. Takes player statistics only from matches of the specified time
 * @param array - массив статистики всех играков. [ "player_id" => [ "match_id" => [ ...all static  ] ] ]
 * @param string - Первый день тура
 * @param string - Последний день тура. Обысно следующий. Может равняться 0. В этом случае тур длиться один день
 * @return array - Статистика играков а матчи, которые были сыграны за тур.
 */
function filterMatchesByDateRange($matches, $startDate, $endDate) {
    $filteredMatches = []; // Новый массив для отфильтрованных матчей

    foreach ($matches as $playerId => $playerMatches) {
        foreach ($playerMatches as $matchId => $matchData) {
            // Преобразуем `match_date` к формату `YYYY-MM-DD`
            $matchDate = date("Y-m-d", strtotime($matchData['match_date']));
            $start = date("Y-m-d", strtotime($startDate)); // Преобразуем `startDate`
            
            // Если `endDate` равно "0", ищем только по `startDate`
            if ($endDate === "0") {
                if ($matchDate == $start) {
                    $filteredMatches[] = $matchData;
                }
            } else {
                $end = date("Y-m-d", strtotime($endDate)); // Преобразуем `endDate`

                // Фильтруем матчи по диапазону дат
                if ($matchDate >= $start && $matchDate <= $end) {
                    $filteredMatches[] = $matchData;
                }
            }
        }
    }

    return $filteredMatches;
}


$playerOfDateTur = getPlayersOfDateTur( $allStaticPlayers, $daysOfTur['first_day'], $daysOfTur['last_day'] );

// Лучшие игроки - отфильтрованные
$bestPlayersForTable = mergeStaticAndData($playerOfDateTur, $dataAllPlayers);


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

/**
 * Этот код находит значение ключа tur, которое встречается чаще всего. Если несколько значений встречаются одинаковое количество раз, выбирается наибольшее из них.
 * @return string
 */
function getMostFrequentTur($array) {
    $counts = []; // Ассоциативный массив для хранения количества повторений

    // Подсчитываем количество каждого значения tur
    foreach ($array as $item) {
        $tur = $item['tur'];
        if (!isset($counts[$tur])) {
            $counts[$tur] = 0;
        }
        $counts[$tur]++;
    }

    // Определяем максимальное количество повторений
    $maxCount = max($counts);

    // Отбираем все значения tur, которые встречаются максимально часто
    $mostFrequent = [];
    foreach ($counts as $tur => $count) {
        if ($count == $maxCount) {
            $mostFrequent[] = $tur;
        }
    }

    // Возвращаем наибольшее значение из наиболее частых
    return max($mostFrequent);
}

$currentTur = getMostFrequentTur($dataMatchesOfDate);


include_once VIEWS . "/calendar_of_matches1.tpl.php";

 
 include_once CONTROLLERS . "/controls1.php";
 include_once CONTROLLERS . "/disqualification.php";



 
 include_once CONTROLLERS . "/footer.php";