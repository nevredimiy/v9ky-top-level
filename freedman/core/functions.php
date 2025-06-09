<?php


/**
 * Красивый распечатка кода как у Laravel
 */
function dump($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}


/**
 * Красивая распечатка кода + die
 */
function dd($data)
{
    dump($data);
    die;
}

/**
 * Красивая респечатка массива
 */
function dump_arr($data) {
    echo '<pre>' . print_r($data, 1) . '</pre>';
}
  
/**
 * Красивая распечатка массива первого элемента
 */
function dump_arr_first($data) {
    echo '<pre>' . print_r(array_slice($data,0,1,true), 1) . '</pre>';
}


/**
 * подключает вид страниц и прекращает работу скрипта
 * @param string|integer
 * @return void
 */
function abort($code = 404)
{
    http_response_code($code);
    require VIEWS . "/errors/{$code}.tpl.php";
    die;
}


/**
 * Получает лучших игроков тура.
 * @param string
 * @param string
 * @return array
 */

function getBestPlayerOfTurForAjax($turnir, $tur)
{
    global $dbF;
    
    $sql = "SELECT 
    t.season,
    m.date,
    m.tur, 
    m.team1,
    t1.name AS team1_name,
    t1.pict AS team1_photo,
    m.team2,    
    t2.name AS team2_name,
    t2.pict AS team2_photo,
    m.field,
    f.name AS field_name,
    m.canseled,
    m.gols1 AS goals1,
    m.gols2 AS goals2,
    t.ru AS turnir_name
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
    WHERE m.`turnir` = :turnir AND m.`tur` = :tur 
    ORDER BY 
        m.id";
        
    $fields = $dbF->query($sql, [":turnir" => $turnir, ":tur" => $tur])->findAll();
    
    return $fields;
}

/**
 * Получает всю статистику игроков в турнире.
 * @param integer - идентификатора турнира
 * @return array - массив со статистикой. [ $player_id => [ $match_id => [ ...statics ] ] ]
 */
function getAllStaticPlayers($turnir) 
{

    global $dbF;
    // Получаем данные из БД. Статискика всех игроков учавствуюих в текущей лиге. Статистика вся, кроме забитых голов
    $sql = 
        "SELECT 
            m.tur, 
            m.date AS match_date,
            p.team,
            s.player, 
            s.matc, 
            s.seyv, 
            s.seyvmin, 
            s.vstvor, 
            s.mimo, 
            s.pasplus, 
            s.pasminus, 
            s.otbor, 
            s.otbormin, 
            s.obvodkaplus, 
            s.obvodkaminus, 
            s.golevoypas, 
            s.zagostrennia, 
            s.vkid, 
            s.vkidmin, 
            s.blok, 
            s.vtrata,
            m.best_player AS count_best_player_of_match,
            (SELECT COUNT(*) AS count_goals FROM v9ky_gol g WHERE g.player= s.player and g.matc = s.matc  AND g.team = p.team) AS count_goals,
            (SELECT COUNT(*) AS count_asists FROM v9ky_asist a WHERE a.player= s.player and a.matc = s.matc) AS count_asists,
            (SELECT COUNT(*) AS yellow_cards FROM v9ky_yellow y WHERE y.player= s.player and y.matc = s.matc) AS yellow_cards,
            (SELECT COUNT(*) AS yellow_red_cards FROM v9ky_yellow_red yr WHERE yr.player= s.player and yr.matc = s.matc) AS yellow_red_cards,
            (SELECT COUNT(*) AS red_cards FROM v9ky_red r WHERE r.player= s.player and r.matc = s.matc) AS red_cards
        FROM `v9ky_sostav` s
        LEFT JOIN `v9ky_match` m ON m.id = s.matc
        LEFT JOIN `v9ky_player`p ON p.id = s.player
        WHERE s.`player` IN (
            SELECT `id` 
            FROM `v9ky_player` 
            WHERE `team` IN (
                SELECT `id` 
                FROM `v9ky_team` 
                WHERE `turnir` = :turnir
            )
        )";

    // Делаем запрос в БД на игроков которые "вибули"
    $fields = $dbF->query($sql, [":turnir" => $turnir])->findAll();

    $allStaticPlayers = [];

    foreach ( $fields as $key => $value ) {
        $allStaticPlayers[$value['player']][$value['matc']] = $value;
    }
  
    return $allStaticPlayers;

}

/**
 * Получает массив данных игроков - ФИО, фото и т.д.
 * @param array - строка идентификаторов игроков для получения ФИО, фото и т.д
 * @return array
 */
function getDataPlayers($allStaticPlayers) 
{
    if(empty($allStaticPlayers)){
        return false;
    }
    global $dbF;

    // Массив только c идентификаторами игроков
    $arrPlayersId = array_keys($allStaticPlayers);
  
    // Подготовляем список идентификаторов для SQL-запроса  
    $placeholders = implode(',', array_fill(0, count($arrPlayersId), '?'));  

    $sql = 
        "SELECT 
            p.id AS player_id,
            p.team AS team_id,
            p.man AS man_id,
            p.amplua AS amplua,
            p.v9ky AS v9ky,
            p.dubler AS dubler,
            p.vibuv AS vibuv,
            m.name1 AS last_name,
            m.name2 AS first_name,
            (SELECT mf.pict 
                FROM v9ky_man_face mf 
                WHERE mf.man = p.man 
                ORDER BY mf.id DESC LIMIT 1) AS player_photo,
            t.pict AS team_photo,
            t.name AS team_name
        FROM 
            v9ky_player p
        LEFT JOIN 
            v9ky_man m ON p.man = m.id
        LEFT JOIN 
            v9ky_man_face mf ON m.id = mf.man
        LEFT JOIN 
            v9ky_team t ON p.team = t.id
        WHERE 
            p.id IN ($placeholders)  
      ";  


    $fields = $dbF->query($sql, $arrPlayersId)->findAll();

    $dataAllPlayers = [];

    // Меняем структуру массива - для удобства работы с ним
    foreach ( $fields as $key => $value ) {
        
        $playerId = $value['player_id'];

        if(!isset($dataAllPlayers[$playerId])) {      
            $dataAllPlayers[$playerId] = $value;             
        }
    }  
    return $dataAllPlayers;
}

/**
 * @param integer - идентификатора турнира
 * @param integer - текущий тур
 * @return array 
 */
function getDataCurrentTur($turnir, $currentTur)
{
    global $dbF;

    $sql = 
        "SELECT 
        m.id,
        m.anons,
        t.season,
        m.date,
        m.tur, 
        m.team1,
        m.tcolor1 as color_tshirt1,
        t1.id AS team1_id,
        t1.name AS team1_name,
        t1.pict AS team1_photo,
        m.team2,    
        m.tcolor2 as color_tshirt2,
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
    WHERE m.`turnir` = :turnir AND m.`tur` = :currentTur 
    ORDER BY 
        m.id";

    // Делаем запрос в БД на игроков которые "вибули"
    $fields = $dbF->query($sql, [":turnir" => $turnir, ":currentTur" => $currentTur])->findAll();
    
    return $fields;
}

/**
 * @param integer - идентификатора турнира
 * @param integer - текущий выбранная дата
 * @return array 
 */
function getDataMatchesOfDay($turnir, $selectedDate)
{
    global $dbF;

    $sql = 
        "SELECT 
        m.id,
        m.anons,
        t.season,
        m.date,
        m.tur, 
        m.team1,
        m.tcolor1 as color_tshirt1,
        t1.id AS team1_id,
        t1.name AS team1_name,
        t1.pict AS team1_photo,
        m.team2,    
        m.tcolor2 as color_tshirt2,
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
    WHERE m.`turnir` = :turnir AND DATE(m.`date`) = :selectedDate 
    ORDER BY 
        m.id";

    // Делаем запрос в БД на игроков которые "вибули"
    $fields = $dbF->query($sql, [":turnir" => $turnir, ":selectedDate" => $selectedDate])->findAll();
    
    return $fields;
}

/**
 * @param integer - идентификатора турнира
 * @return array 
 */
function getDateTurs($turnir)
{

    global $dbF;

    $sql = "SELECT 
    tur, 
    MIN(date) AS min_date, 
    MAX(date) AS max_date,
    MONTHNAME(MIN(date)) AS month_min_name,
    MONTHNAME(MAX(date)) AS month_max_name,
    DATE_FORMAT(MIN(date), '%d') AS day_min, 
    DATE_FORMAT(MAX(date), '%d') AS day_max,
    DATE_FORMAT(MIN(date), '%m') AS month_min, 
    DATE_FORMAT(MAX(date), '%m') AS month_max
FROM 
    v9ky_match
WHERE `turnir` = :turnir
GROUP BY 
    tur
ORDER BY 
    tur ASC;";

    // Делаем запрос в БД на игроков которые "вибули"
    $fields = $dbF->query($sql, [":turnir" => $turnir])->findAll();
    
    return $fields;
}



/**
 * Получения tournament - название последнего турнира латиницей. Используеться для написания параметра в адресной строке.
 * @return string
 */
function getTournament()
{
    global $dbF;
    $sql = "SELECT * FROM `v9ky_turnir` WHERE `seasons` = (SELECT id FROM `v9ky_seasons` ORDER BY id DESC LIMIT 1) AND `city` = 2 LIMIT 1";
    $dataTurnir = $dbF->query($sql)->findAll();    
    $tournament = isset($dataTurnir[0]['name']) ? $dataTurnir[0]['name'] : '';
    return $tournament;
}

/**
 * Получения идентификатора турнира
 * @param string
 * @return string
 */
function getTurnir($tournament = '')
{
    global $dbF;

    // Если переменная tournament пустая, тогда берем последний турнир.
    if($tournament == ''){
        $tournament = getTournament();
    }

    $sql = "SELECT `id` FROM `v9ky_turnir` WHERE `name` = :tournament";
    $turnir = $dbF->query($sql, [":tournament" => $tournament])->find();
    return $turnir['id'];
}

/**
 * 
 */
function getSeasonName($turnirId)
{
    global $dbF;
    $sql = "SELECT `season` FROM `v9ky_turnir` WHERE `id` = :turnir_id";
    $fields = $dbF->query($sql, [":turnir_id" => $turnirId])->find();
    $seasonName = $fields['season'];
    return $seasonName;
}


/**
 * Получает стадионы из базы данных
 * @return array
 */
function getDataFields(){
    global $dbF;
    $fields = $dbF->query("SELECT 
        `name`, 
        `adres` AS address,
        `photo`,
        `fields_40x20`,
        `fields_60x40`,
        `parking`,
        `shower`,
        `loudspeaker`,
        `cloakroom`,
        `toilet`,
        `latitude`,
        `longitude`,
        (SELECT `name_ua` FROM `v9ky_city` c WHERE f.city = c.id) AS city
            FROM `v9ky_fields` f
            WHERE `visible` > 0")->findAll();

    return $fields;
}
/**
 * Функция убирает нежелательные символы. Напримен, "  «Manchester United» " - вернет "%Manchester United%" или "  Chelsea\t" - вернет "%Chelsea%"
 * Такой результат нужен для апроса в базу данный для поиска команд по названию (WHERE name LIKE :team1)
 */
function sanitizeInput($input) {
    // Убираем нежелательные символы и пробелы
    return '%' . preg_replace('/[^\w\s]/u', '', trim($input)) . '%';
}

/**
 *  Функция получает данные матчей из всех сезонов, где встречались две команды
 * @param string - Название команды 1
 * @param string - Название команды 2
 * @return array - массив матчей
 */
function getHistoryMeets($team1, $team2) {
    global $dbF;

    // Обрабатываем входные данные для использования с LIKE и REGEXP
    $team1 = trim($team1);
    $team2 = trim($team2);

    // SQL-запрос
    $sql = "SELECT 
            tr.season AS season_name,
            tr.ru AS liga_name,
            t1.name AS team1_name,
            t2.name AS team2_name,
            m.gols1 AS goals1, 
            m.gols2 AS goals2
        FROM `v9ky_match` m
        LEFT JOIN `v9ky_team` t1 ON t1.id = m.team1
        LEFT JOIN `v9ky_team` t2 ON t2.id = m.team2
        LEFT JOIN `v9ky_turnir` tr ON tr.id = m.turnir
        WHERE (
            -- Условие для team1 и team2
            (t1.name LIKE :team1 OR t1.name REGEXP :regex1) AND 
            (t2.name LIKE :team2 OR t2.name REGEXP :regex2)
        ) 
        AND m.canseled > 0
        OR (
            -- Условие для team2 и team1 (обратный порядок)
            (t2.name LIKE :team1 OR t2.name REGEXP :regex1) AND 
            (t1.name LIKE :team2 OR t1.name REGEXP :regex2)
        )
        AND m.canseled > 0
        ORDER BY m.date DESC
    ";

    // Параметры для подстановки
    $params = [
        ":team1" => '%' . $team1 . '%',
        ":team2" => '%' . $team2 . '%',
        ":regex1" => "[[:<:]]" . preg_quote($team1, '/') . "[[:>:]]",
        ":regex2" => "[[:<:]]" . preg_quote($team2, '/') . "[[:>:]]",
    ];


    // Выполняем запрос
    $fields = $dbF->query($sql, $params)->findAll();

    foreach($fields as $field){
            $field['team1_name'] = trim($field['team1_name']);
            $field['team2_name'] = trim($field['team2_name']);
    }

    return $fields;
}

/**
 * Получает состав команды 
 * @param string - идентификатор матча
 * @param string - идентификатор команды
 * @return array
 */
function getTeamComposition($matchId, $teamId) {
    global $dbF;

    // приводим значение к типу integer - от sql-инъекций
    $matchId = (int) $matchId;
    $teamId = (int) $teamId;
    
    $sql = "SELECT 
	p.id AS player_id,
	p.`team` AS team_id,
	m.`name1` AS lastname,
	CONCAT(LEFT(m.name2, 1), '.') AS firstname,
	s.nomer AS nomer,
    t.capitan AS capitan_id,
    t.manager AS manager_id,    
    t.trainer AS trainer_id
    FROM `v9ky_player` p
    LEFT JOIN (
        SELECT player, nomer FROM v9ky_sostav WHERE `matc` = :match_id
    ) s ON s.player = p.id
    LEFT JOIN 
        v9ky_man m ON m.id = p.man
    LEFT JOIN
        v9ky_team t ON t.id = p.team
    WHERE p.`id` IN (
        SELECT `player` FROM `v9ky_sostav` WHERE `matc`= :match_id
        )
    AND p.team = :team_id
    ORDER BY s.nomer
    ";

    $fields = $dbF->query($sql, [":match_id" => $matchId, ":team_id" => $teamId])->findAll();

    return $fields;
}

/**
 * Получает состав команды и игдивидуальную статистику 
 * @param string - идентификатор матча
 * @param string - идентификатор команды
 * @return array
 */
function getTeamCompositionAndStats($matchId, $teamId) {
    global $dbF;

    $matchId = (int) $matchId;
    $teamId = (int) $teamId;
    
    $sql = "SELECT 
	p.`id` AS player_id,
	p.`team` AS team_id,
	m.`name1` AS lastname,
	CONCAT(LEFT(m.name2, 1), '.') AS firstname,
    (SELECT COUNT(*) FROM `v9ky_gol` WHERE matc = :match_id AND player = p.`id` AND `team` = p.team) AS goals_scored,
    (SELECT COUNT(*) FROM `v9ky_asist` WHERE matc = :match_id AND player = p.`id`) AS asist,
	s.`zagostrennia` AS build_up,
	s.`pasplus` AS success_pass,
	s.`pasminus` AS bad_pass,
	s.`vtrata` AS loss_ball,
	s.vstvor AS shot_on,
	s.mimo AS shot_off,
	s.obvodkaplus AS successfull_dribble,
	s.obvodkaminus AS failed_dribble,
	s.otbor AS successful_tackle,
	s.otbormin AS failed_tackle,
	s.blok AS success_block,
	s.seyv AS success_save ,
	s.seyvmin AS failed_save

    FROM `v9ky_player` p
    LEFT JOIN (
        SELECT * FROM `v9ky_sostav`  WHERE `matc` = :match_id
    ) s ON s.player = p.id

    LEFT JOIN 
        `v9ky_man` m ON m.`id` = p.`man`

    WHERE p.`id` IN (
        SELECT `player` FROM `v9ky_sostav` WHERE `matc`= :match_id
        )
    AND p.team = :team_id
    ORDER BY s.nomer

    ";

    $fields = $dbF->query($sql, [":match_id" => $matchId, ":team_id" => $teamId])->findAll();

    $arr = [];

    // Обработка данных и добавление ключа `total`
    foreach ($fields as $key => $field) {
        $arr[$key] = $field; // Копируем все данные игрока
        $arr[$key]['total'] = $field['goals_scored'] * 15 
                            + $field['asist'] * 10 
                            + $field['build_up'] * 10 
                            + $field['success_pass'] * 3 
                            - $field['bad_pass'] * 3 
                            - $field['loss_ball'] * 3 
                            + $field['shot_on'] * 7 
                            - $field['shot_off'] * 4 
                            + $field['successfull_dribble'] * 5 
                            - $field['failed_dribble'] * 3 
                            + $field['successful_tackle'] * 8 
                            - $field['failed_tackle'] * 5 
                            + $field['success_block'] * 4 
                            + $field['success_save'] * 15 
                            - $field['failed_save'] * 7;
    }

    return $arr;
}

/**
 * Получаем ФИО тренера и менеджера
 * @param string - идентификатор команды
 * @return array - массив из двух элементов
 */
function getTrainerAndManager($teamId){
    global $dbF;

    $sql = "SELECT 
    name1 AS lastname,
    name2 AS firstname
    FROM `v9ky_man`
    WHERE id IN (
        SELECT man FROM `v9ky_player` WHERE id = (SELECT trainer FROM `v9ky_team` WHERE id = :team_id)
        UNION
        SELECT man FROM `v9ky_player` WHERE id = (SELECT manager FROM `v9ky_team` WHERE id = :team_id)
    )";

    $fields = $dbF->query($sql, [":team_id" => $teamId])->findAll();

    return $fields;
}

/**
 * Получаем ФИО тренера
 * @param string - идентификатор команды
 * @return array - массив
 */
function getTrainerName($teamId){
    global $dbF;

    $sql = "SELECT 
    m.name1 AS lastname,
    m.name2 AS firstname,
    p.id AS player_id
    FROM `v9ky_man` m
    LEFT JOIN `v9ky_player` p
        ON p.man = m.id
    WHERE m.id = (SELECT man FROM `v9ky_player` WHERE id = (SELECT trainer FROM `v9ky_team` WHERE id = :team_id))";

    $fields = $dbF->query($sql, [":team_id" => $teamId])->findAll();

    return $fields;
}

/**
 * Получаем ФИО менеджера
 * @param string - идентификатор команды
 * @return array - массив
 */
function getManagerName($teamId){
    global $dbF;

    $sql = "SELECT 
    m.name1 AS lastname,
    m.name2 AS firstname,
    p.id AS player_id
    FROM `v9ky_man` m
    LEFT JOIN `v9ky_player` p
        ON p.man = m.id
    WHERE m.id = (SELECT man FROM `v9ky_player` WHERE id = (SELECT manager FROM `v9ky_team` WHERE id = :team_id))";

    $fields = $dbF->query($sql, [":team_id" => $teamId])->findAll();

    return $fields;
}

/**
 * Получает все видео матча
 * @param string
 * @return array
 */
function getVideo($matchId){
    global $dbF;

    $sql = "SELECT 
    video, videohiden, videobest, video_intervu, video_intervu2
    FROM `v9ky_match`
    WHERE id = :match_id";

    $fields = $dbF->query($sql, [":match_id" => $matchId])->findAll();

    return $fields;
}

/**
 * Получение протокола матча
 * @param string - идентификатор матча
 * @return array
 */
function getMatchReport($matchId) {
    global $dbF;

    $sql = "SELECT 
        'assist' AS event_type, 
        a.player AS player_id, 
        m.name1 AS lastname,
        m.name2 AS firstname,
        a.team AS team_id,     
        NULL AS goal_id,       
        NULL AS card_color,    
        a.time AS event_time,
        (SELECT team FROM v9ky_player p WHERE p.id = a.player) AS team_id_player_belong,
        NULL AS status_penalty
    FROM v9ky_asist a
    LEFT JOIN v9ky_man m ON m.id = (SELECT man FROM v9ky_player WHERE id = a.player)
    WHERE a.matc = :match_id

    UNION ALL

    SELECT 
        'goal' AS event_type,
        g.player AS player_id,
        m.name1 AS lastname,
        m.name2 AS firstname,
        g.team AS team_id,
        g.id AS goal_id,
        NULL AS card_color,
        g.time AS event_time,
        (SELECT team FROM v9ky_player p WHERE p.id = g.player) AS team_id_player_belong,
        NULL AS status_penalty
    FROM v9ky_gol g
    LEFT JOIN v9ky_man m ON m.id = (SELECT man FROM v9ky_player WHERE id = g.player)
    WHERE g.matc = :match_id

    UNION ALL

    SELECT 
        'yellow_card' AS event_type,
        y.player AS player_id,
        m.name1 AS lastname,
        m.name2 AS firstname,
        y.team AS team_id,
        NULL AS goal_id,
        'yellow' AS card_color,
        y.time AS event_time,
        (SELECT team FROM v9ky_player p WHERE p.id = y.player) AS team_id_player_belong,
        NULL AS status_penalty
    FROM v9ky_yellow y
    LEFT JOIN v9ky_man m ON m.id = (SELECT man FROM v9ky_player WHERE id = y.player)
    WHERE y.matc = :match_id

    UNION ALL

    SELECT 
        'red_card' AS event_type,
        r.player AS player_id,
        m.name1 AS lastname,
        m.name2 AS firstname,
        r.team AS team_id,
        NULL AS goal_id,
        'red' AS card_color,
        r.time AS event_time,
        (SELECT team FROM v9ky_player p WHERE p.id = r.player) AS team_id_player_belong,
        NULL AS status_penalty
    FROM v9ky_red r
    LEFT JOIN v9ky_man m ON m.id = (SELECT man FROM v9ky_player WHERE id = r.player)
    WHERE r.matc = :match_id

    UNION ALL

    SELECT 
        'yellow_red_card' AS event_type,
        yr.player AS player_id,
        m.name1 AS lastname,
        m.name2 AS firstname,
        yr.team AS team_id,
        NULL AS goal_id,
        'yellow_red' AS card_color,
        yr.time AS event_time,
        (SELECT team FROM v9ky_player p WHERE p.id = yr.player) AS team_id_player_belong,
        NULL AS status_penalty
    FROM v9ky_yellow_red yr
    LEFT JOIN v9ky_man m ON m.id = (SELECT man FROM v9ky_player WHERE id = yr.player)
    WHERE yr.matc = :match_id

    UNION ALL

    SELECT 
        'penalty' AS event_type,
        pn.player AS player_id,
        m.name1 AS lastname,
        m.name2 AS firstname,
        pn.team AS team_id,
        NULL AS goal_id,
        NULL AS card_color,
        pn.time AS event_time,
        (SELECT team FROM v9ky_player p WHERE p.id = pn.player) team_id_player_belong,
        pn.status AS status_penalty
    FROM v9ky_penalty pn
    LEFT JOIN v9ky_man m ON m.id = (SELECT man FROM v9ky_player WHERE id = pn.player)
    WHERE pn.matc = :match_id

    ORDER BY event_time ASC, -- Сортировка по времени события
    CASE 
        WHEN event_type = 'assist' THEN 1 -- Приоритет assist
        WHEN event_type = 'goal' THEN 2   -- Затем goal
        WHEN event_type = 'yellow_card' THEN 3
        WHEN event_type = 'red_card' THEN 4
        WHEN event_type = 'yellow_red_card' THEN 5
        ELSE 6                            -- Другие события (если есть)
    END";

    $fields = $dbF->query($sql, [":match_id" => $matchId])->findAll();

    return $fields;

}


/**
 * Получает все команды выбранной Лиги
 */
function getTeamsOfLeague($turnir)
{
    global $dbF;

    $sql = "SELECT * FROM v9ky_team WHERE turnir= :turnir ORDER BY name ASC";

    $fields = $dbF->query($sql, [":turnir" => $turnir])->findAll();
    
    return $fields;

}

/**
 * Получаем данные команды по идентификатору команды
 */
function getTeamData($teamId)
{
    global $dbF;

    $sql = "SELECT * FROM `v9ky_team` WHERE `id` = :team_id ";

    $fields = $dbF->query($sql, [":team_id" => $teamId])->find();

    return $fields;
}

/**
 * Получение данных игроков по идентификатору команды
 */
function getPlayersOfTeam($teamId, $kep=0)
{

    global $dbF;

    if($kep){
        $active = "";
    } else {
        $active = "AND `active` = '1'";
    }

    $sql = "SELECT p.`id`,p.`team`,p.`nomer`,p.`name1`,p.`name2`,p.`name3`,p.`age`,p.`face`,p.`active`,p.`man`,m.`amplua`,p.`v9ky`,p.`dubler`,p.`vibuv`,m.`tel` 
        FROM `v9ky_player` p 
        LEFT JOIN
        	`v9ky_man` m ON m.id = p.man
        WHERE p.`team` = :team_id 
        ORDER BY 
            `active` desc, 
            `vibuv`, 
            p.`id` = (
                SELECT `capitan` FROM `v9ky_team` WHERE `id` = :team_id) DESC, 
            (SELECT count(*) AS kol FROM `v9ky_gol` WHERE `player` = p.id 
                AND `team` = :team_id) DESC, 
            (SELECT count(*) FROM `v9ky_sostav` WHERE `player` = p.id) DESC, 
            (SELECT `name1` FROM `v9ky_man` WHERE `id` = p.man)";

     $fields = $dbF->query($sql, [":team_id" => $teamId])->findAll();

     return $fields;
}

/**
 * Получаем капитана, менеджера, тренера.
 */
function getTeamHeads($teamId){
    global $dbF;

    $fields = $dbF->query("SELECT `capitan`, `trainer`, `manager` FROM `v9ky_team` WHERE  `id` = :team_id",[":team_id" => $teamId])->findAll();

    $teamHeads = [];
    foreach($fields as $field) {
        if($field['capitan'] != 0){
            $teamHeads['capitan'] = getPlayerData($field['capitan']);
            $teamHeads['capitan']['player_id'] = $field['capitan'];
        }
        if($field['manager'] != 0){
            $teamHeads['manager'] = getPlayerData($field['manager']);
            $teamHeads['manager']['player_id'] = $field['manager'];
        }
        if($field['trainer'] != 0){
            $teamHeads['trainer'] = getPlayerData($field['trainer']);
            $teamHeads['trainer']['player_id'] = $field['trainer'];
        }
    }

    return $teamHeads;
}

function getPlayerData($playerId){
    global $dbF;

    $sql = "SELECT 
        m.`id` AS man_id, 
        m.`tel`, 
        m.`name1` AS player_lastname, 
        m.`name2` AS player_firstname, 
        mf.`pict` AS player_photo,
        t.`pict` AS team_logo
        FROM `v9ky_man` m 
        LEFT JOIN 
            `v9ky_man_face` mf ON mf.man = m.id  
        LEFT JOIN 
            `v9ky_team` t ON t.id = (SELECT `team` FROM `v9ky_player` WHERE `id`=:player_id LIMIT 1)
        WHERE m.`id` = (SELECT `man` FROM `v9ky_player` WHERE `id`=:player_id LIMIT 1)
        ORDER BY mf.`id` DESC
        LIMIT 1";
    $fields = $dbF->query($sql,[":player_id" => $playerId])->findAll();
    if (empty($fields)) {
        return "";
    }
    return $fields[0];
}
/**
 * 
 */

 function getMatches($turnir, $teamId)
 {
    global $dbF;

    $sql = "SELECT 
        `id`, 
        `canseled`, 
        `date`, 
        `tur`, 
        `field`, 
        (SELECT `name` FROM `v9ky_fields` WHERE `id` = a.field) AS field_name, 
        gols1 AS goals1, 
        gols2 AS goals2, 
        (SELECT `ru` FROM `v9ky_turnir` WHERE `id` = a.turnir) AS turnir_name, 
        (SELECT `name` FROM `v9ky_team` WHERE `id` = a.team1) AS team1, 
        (SELECT pict FROM v9ky_team WHERE `id` = a.team1) AS team1_photo, 
        (SELECT name FROM v9ky_team WHERE `id` = a.team2) AS team2, 
        (SELECT pict FROM v9ky_team WHERE `id` = a.team2) AS team2_photo 
        FROM v9ky_match a 
        WHERE turnir=:turnir_id 
        AND (team1=:team_id OR team2=:team_id) 
        ORDER BY `date` DESC";

    $fields = $dbF->query($sql, [":team_id" => $teamId, ":turnir_id" => $turnir])->findAll();
    return $fields;
 }

 /**
  * Получает массив лиг текущего сезона и выбранного города. 
  */

 function getLeagues($turnir)
 {
    global $dbF;

    $sql = "SELECT 
    t.`id`,
    t.`name` AS slug,
    t.`ru` AS full_name,
    c.`name_ua` AS city_name -- Название города из таблицы v9ky_city
    FROM v9ky_turnir t
    LEFT JOIN v9ky_city c ON c.`id` = t.`city` -- Присоединяем таблицу v9ky_city
    WHERE t.`city` = (
            SELECT city FROM v9ky_turnir WHERE id = :turnir
        ) 
        AND t.`season` = (
            SELECT season FROM v9ky_turnir WHERE id = :turnir
        ) 
    ORDER BY t.`priority` ASC;
    ";

    $fields = $dbF->query($sql, [":turnir" => $turnir])->findAll();
    return $fields;

 }

 /**
  * 
  */
  function getCalendar()
  {
    global $dbF;

    $sql = "SELECT 
    m.`date`,
    m.`tur`,
    m.`field` AS field_id,
    f.`name` AS field_name,
    f.`adres` AS field_address,
    m.`tcolor1` AS tshirt_1,
    m.`tcolor2` AS tshirt_2,
    m.`turnir` AS turnir_id,
    t.`ru` AS turnir_name,
    m.`team1` AS team1_id,
    t1.`name` AS team1_name,
    t1.`pict` AS team1_photo,
    m.`team2` AS team2_id,
    t2.`name` AS team2_name,
    t2.`pict` AS team2_photo
FROM `v9ky_match` m 
LEFT JOIN `v9ky_team` t1 
    ON t1.`id` = m.`team1`
LEFT JOIN `v9ky_team` t2 
    ON t2.`id` = m.`team2`
LEFT JOIN `v9ky_fields` f 
    ON f.`id` = m.`field`
LEFT JOIN `v9ky_turnir` t 
    ON t.`id` = m.`turnir`
WHERE `canseled` = 0 
AND m.`turnir` IN (SELECT `id` FROM `v9ky_turnir` WHERE `active` = 1 ) 
ORDER BY 
    YEAR(m.`date`) ASC, 
    DAYOFYEAR(m.`date`) ASC, 
    (SELECT `priority` FROM `v9ky_fields` WHERE `id` = m.`field`), 
    m.`date` ASC";

    $fields = $dbF->query($sql)->findAll();

    return $fields;
  }

/**
 * Получает строку последнего тура.
 * @param string
 * @return string
 */
function getLastTur($turnirId)
{
    global $dbF;

    $sql = "SELECT tur as last_tur FROM `v9ky_match` WHERE `canseled` = 1 AND `turnir` = :turnir ORDER BY tur DESC LIMIT 1";

    $lastTur = $dbF->query($sql, [":turnir" => $turnirId])->find();

    return $lastTur['last_tur'];
}

/**
 * Получает строку последнего тура.
 * @param string
 * @return string
 */
function getLastTurDate($turnirId)
{
    global $dbF;

    $sql = "SELECT `date` as last_tur_date FROM `v9ky_match` WHERE `canseled` = 1 AND `turnir` = :turnir ORDER BY tur DESC LIMIT 1";

    $lastTur = $dbF->query($sql, [":turnir" => $turnirId])->find();

    return $lastTur['last_tur_date'];
}

/**
 * Формирует ссылку для элементов массива на основе денного слага.
 */
function addLinkItem($array, $url='')
{
    if($url == '')  {
        // Получаем текущий URL
        $currentUrl = $_SERVER['REQUEST_URI'];        
    } else  {
        $currentUrl = $url; 
    } 

    // Разбираем URL на части
    $urlParts = parse_url($currentUrl);

    // Извлекаем параметры из строки запроса
    $queryParams = [];
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
    }

   
    // Обрабатываем каждый элемент массива. 
    // Формируем ссылку исходя из адресной строки и добавляем в массив leagues.
    foreach ($array as $key => $value) {
        // Проверяем наличие параметра 'tur' и заменяем его
        // if (isset($queryParams['tur'])) {
        //     $queryParams['tur'] = $value['tur']; // Задаем новое значение
        // } else {
        //     // Если параметра 'tur' нет, можно добавить его
        //     $queryParams['tur'] = $value['tur'];
        // }

        $queryParams['tur'] = $value['tur'];

        // Собираем строку запроса обратно
        $newQuery = http_build_query($queryParams);

        // Собираем новый URL
        $newUrl = $urlParts['path'] . '?' . $newQuery;

        // Добавляем ссылку в массив с ключом link
        $array[$key]['link'] = $newUrl;
    }

    return $array;
}

/**
 * Меняем формат датты из такой 2024-12-22 11:35:00 в такую - 22 грудня ( неділя )
 * @param string
 * @return string
 */
 function getFormateDate($dateString, $short = false)
 {
    
    // Создаем объект DateTime
    $date = new DateTime($dateString);

    // Массивы для перевода месяцев и дней недели на украинский
    $months = [
        'January' => 'січня',
        'February' => 'лютого',
        'March' => 'березня',
        'April' => 'квітня',
        'May' => 'травня',
        'June' => 'червня',
        'July' => 'липня',
        'August' => 'серпня',
        'September' => 'вересня',
        'October' => 'жовтня',
        'November' => 'листопада',
        'December' => 'грудня'
    ];

    if($short) {        
        $daysOfWeek = [
            'Monday' => 'пн',
            'Tuesday' => 'вт',
            'Wednesday' => 'ср',
            'Thursday' => 'чт',
            'Friday' => 'пт',
            'Saturday' => 'сб',
            'Sunday' => 'нд'
        ];
    } else  {
        $daysOfWeek = [
            'Monday' => 'понеділок',
            'Tuesday' => 'вівторок',
            'Wednesday' => 'середа',
            'Thursday' => 'четвер',
            'Friday' => 'п’ятниця',
            'Saturday' => 'субота',
            'Sunday' => 'неділя'
        ];
    }

    // Форматируем дату
    $day = $date->format('j'); // День
    $month = $months[$date->format('F')]; // Месяц на украинском
    $dayOfWeek = $daysOfWeek[$date->format('l')]; // День недели на украинском

    // Собираем строку
    $formattedDate = "$day $month ($dayOfWeek)";

    return $formattedDate;
}
/**
 * Получает время из даты
 */
function getTime($date)
{
     // Преобразуем дату в объект DateTime
  $date = new DateTime($date);

  // Форматируем дату
  $formattedTime = strftime('%H:%M', $date->getTimestamp());

  return $formattedTime;
}

/**
 * 
 */
 function getTransfers($turnirId)
 {
    global $dbF;
    $sql = "SELECT * FROM `v9ky_transfer_log` WHERE `turnir` = :turnir_id ORDER BY `date` DESC";

    $fields = $dbF->query($sql, [":turnir_id" => $turnirId])->findAll();
    
    $transfers = [];

    foreach ($fields as $key => $field) {
        // Получаем фото игрока из таблицы `v9ky_man_face`
        if ( array_key_exists('man_id', $field) && $field['man_id'] === NULL ) {   
            $arr = explode(" ", $field['log']);
            // создаем запрос в БД
            $sql_man = "SELECT `pict` AS photo FROM `v9ky_man_face` WHERE `man` = (SELECT `id` FROM `v9ky_man` WHERE `name1` = :lastname AND `name2` = :firstname ORDER BY `id` DESC LIMIT 1)";
            // Получаем фото по Фамилии и Имени
            $playerData = $dbF->query( $sql_man, [ ":lastname" => $arr[1], ":firstname" => $arr[2] ] )->find();   
             // Создаем объект DateTime
            $date = new DateTime($field['date']);
            // Преобразуем дату в формат дд.мм.гггг
            $formattedDate = $date->format('d.m.Y');
            
            $transfers[$key]['date'] = $formattedDate;
            $transfers[$key]['lastname'] = $arr[1];
            $transfers[$key]['firstname'] = $arr[2];
            $transfers[$key]['photo'] = $playerData['photo'] ? $playerData['photo'] : 'avatar1.jpg';
            $transfers[$key]['action'] = $field['del_id'] ?  0 : 1; // если в $field['del_id'] = 0 - это добавление игрока в команду. Иначе удаление.
            $transfers[$key]['real_action'] = $arr[3] == 'додано' ? 0 : 1;
                                
        } else {
            $sql_man = "SELECT 
                mf.`pict` AS photo,
                m.`name1`AS lastname,
                m.`name2` AS firstname
                FROM `v9ky_man_face` mf
                LEFT JOIN `v9ky_man` m ON m.`id` = mf.`man`
                WHERE `man` = :man_id";
            $playerData = $dbF->query( $sql_man, [ ":man_id" => $field['man_id'] ] )->find();  
            
            $arr = explode(" ", $field['log']);

            // Создаем объект DateTime
            $date = new DateTime($field['date']);
            // Преобразуем дату в формат дд.мм.гггг
            $formattedDate = $date->format('d.m.Y');
            $transfers[$key]['date'] = $formattedDate;
            $transfers[$key]['lastname'] = $playerData['lastname'];
            $transfers[$key]['firstname'] = $playerData['firstname'];
            $transfers[$key]['photo'] = $playerData['photo'] ? $playerData['photo'] : 'avatar1.jpg';
            $transfers[$key]['action'] = $field['del_id'] ?  0 : 1; // если в $field['del_id'] = 0 - это добавление игрока в команду. Иначе удаление.         
             $transfers[$key]['real_action'] = $arr[3] == 'додано' ? 0 : 1;
        }

        if( array_key_exists('team_id', $field) && $field['team_id'] === NULL ){

            // Регулярное выражение для поиска текста после "команди" или "команды"
            $pattern = '/(?:команди|команды)\s+["«]?([^"»]+)["»]?/u';
        
            if (preg_match($pattern, $field['log'], $matches)) {
                // Подстрока после "команди" или "команды" находится в $matches[1]  
                $strTeamName = '%' . trim($matches[1]) . '%';              
                $sqlTeam = "SELECT `pict` AS photo, `name` FROM `v9ky_team` WHERE `name` LIKE :team_name ORDER BY id DESC LIMIT 1";
                $teamData = $dbF->query( $sqlTeam, [ ":team_name" => $strTeamName ] )->find();
                $transfers[$key]['team_photo'] = $teamData['photo'];
                $transfers[$key]['team_name'] = $teamData['name'];
            }            
            
        } else {
            $sqlTeam = "SELECT `pict` AS photo, `name` FROM `v9ky_team` WHERE `id` = :team_id ORDER BY id DESC LIMIT 1";
            $teamData = $dbF->query( $sqlTeam, [ ":team_id" => $field['team_id'] ] )->find();
            $transfers[$key]['team_photo'] = $teamData['photo'];
            $transfers[$key]['team_name'] = $teamData['name'];
        }        
    }

    return $transfers; 
 }


  function getStaticMatch($matchId, $team1_id, $team2_id)
  {
    global $dbF;

    $sql = "SELECT 
        SUM(s.`vstvor`) AS total_vstvor,
        SUM(s.`mimo`) AS total_mimo,
        SUM(s.`pasplus`) AS total_pasplus,
        SUM(s.`pasminus`) AS total_pasminus,
        SUM(s.`seyv`) AS total_seyv,
        SUM(s.`otbor`) AS total_otbor,
        SUM(s.`obvodkaplus`) AS total_obvodkaplus,
        SUM(s.`obvodkaminus`) AS total_obvodkaminus,
        p.`team`,
        m.date AS match_date
    FROM `v9ky_sostav` s
    LEFT JOIN `v9ky_player` p
        ON p.`id` = s.`player`
    LEFT JOIN `v9ky_match` m
        ON m.`id` = :match_id
    WHERE s.`matc` = :match_id
    AND p.`team` IN (:team1_id, :team2_id)
    GROUP BY p.`team`";

    $results = $dbF->query($sql, [
        ":match_id" => $matchId,
        ":team1_id" => $team1_id,
        ":team2_id" => $team2_id
    ])->findAll();

    $team1_data = [];
    $team2_data = [];

    foreach ($results as $row) {
        if ($row['team'] == $team1_id) {
            $team1_data = $row;
        } elseif ($row['team'] == $team2_id) {
            $team2_data = $row;
        }
    }

    // получаем количество ударов команды за матч
    
    $team1_data['total_udar'] = isset($team1_data['total_vstvor']) || isset($team1_data['total_mimo']) ? $team1_data['total_vstvor'] + $team1_data['total_mimo'] : 0;
    $team2_data['total_udar'] = isset($team2_data['total_vstvor']) || isset($team2_data['total_mimo']) ? $team2_data['total_vstvor'] + $team2_data['total_mimo'] : 0;

    $team1_data['total_vstvor'] = isset($team1_data['total_vstvor']) ? $team1_data['total_vstvor'] : 0;
    $team2_data['total_vstvor'] = isset($team2_data['total_vstvor']) ? $team2_data['total_vstvor'] : 0;

    $team1_data['total_mimo'] = isset($team1_data['total_mimo']) ? $team1_data['total_mimo'] : 0;
    $team2_data['total_mimo'] = isset($team2_data['total_mimo']) ? $team2_data['total_mimo'] : 0;
    
    $team1_data['total_pasplus'] = isset($team1_data['total_pasplus']) ? $team1_data['total_pasplus'] : 0;
    $team2_data['total_pasplus'] = isset($team2_data['total_pasplus']) ? $team2_data['total_pasplus'] : 0;

    $team1_data['total_pasminus'] = isset($team1_data['total_pasminus']) ? $team1_data['total_pasminus'] : 0;
    $team2_data['total_pasminus'] = isset($team2_data['total_pasminus']) ? $team2_data['total_pasminus'] : 0;

    $team1_data['total_otbor'] = isset($team1_data['total_otbor']) ? $team1_data['total_otbor'] : 0;
    $team2_data['total_otbor'] = isset($team2_data['total_otbor']) ? $team2_data['total_otbor'] : 0;

    $team1_data['total_obvodkaplus'] = isset($team1_data['total_obvodkaplus']) ? $team1_data['total_obvodkaplus'] : 0;
    $team2_data['total_obvodkaplus'] = isset($team2_data['total_obvodkaplus']) ? $team2_data['total_obvodkaplus'] : 0;

    $team1_data['total_obvodkaminus'] = isset($team1_data['total_obvodkaminus']) ? $team1_data['total_obvodkaminus'] : 0;
    $team2_data['total_obvodkaminus'] = isset($team2_data['total_obvodkaminus']) ? $team2_data['total_obvodkaminus'] : 0;

    $team1_data['total_seyv'] = isset($team1_data['total_seyv']) ? $team1_data['total_seyv'] : 0;
    $team2_data['total_seyv'] = isset($team2_data['total_seyv']) ? $team2_data['total_seyv'] : 0;


    $team1_data['udar_percentage_team'] = isset($team1_data['total_udar']) ? calculate_percentage($team1_data['total_udar'], $team2_data['total_udar']) : 50;
    $team2_data['udar_percentage_team'] = 100 - $team1_data['udar_percentage_team'];

    $team1_data['vstvor_percentage_team'] = isset($team1_data['total_vstvor']) ? calculate_percentage($team1_data['total_vstvor'], $team2_data['total_vstvor']) : 50;
    $team2_data['vstvor_percentage_team'] = 100 - $team1_data['vstvor_percentage_team'];

    $team1_data['mimo_percentage_team'] = isset($team1_data['total_mimo']) ? calculate_percentage($team1_data['total_mimo'], $team2_data['total_mimo']) : 50;
    $team2_data['mimo_percentage_team'] = 100 - $team1_data['mimo_percentage_team'];
    
    $team1_data['pasplus_percentage_team'] = isset($team1_data['total_pasplus']) ? calculate_percentage($team1_data['total_pasplus'], $team2_data['total_pasplus']) : 50;
    $team2_data['pasplus_percentage_team'] = 100 - $team1_data['pasplus_percentage_team'];

    $team1_data['pasminus_percentage_team'] = isset($team1_data['total_pasminus']) ? calculate_percentage($team1_data['total_pasminus'], $team2_data['total_pasminus']) : 50;
    $team2_data['pasminus_percentage_team'] = 100 - $team1_data['pasminus_percentage_team'];

    $team1_data['otbor_percentage_team'] = isset($team1_data['total_otbor']) ? calculate_percentage($team1_data['total_otbor'], $team2_data['total_otbor']) : 50;
    $team2_data['otbor_percentage_team'] = 100 - $team1_data['otbor_percentage_team'];

    $team1_data['obvodkaplus_percentage_team'] = isset($team1_data['total_obvodkaplus']) ? calculate_percentage($team1_data['total_obvodkaplus'], $team2_data['total_obvodkaplus']) : 50;
    $team2_data['obvodkaplus_percentage_team'] = 100 - $team1_data['obvodkaplus_percentage_team'];

    $team1_data['obvodkaminus_percentage_team'] = isset($team1_data['total_obvodkaminus']) ? calculate_percentage($team1_data['total_obvodkaminus'], $team2_data['total_obvodkaminus']) : 50;
    $team2_data['obvodkaminus_percentage_team'] = 100 - $team1_data['obvodkaminus_percentage_team'];

    $team1_data['seyv_percentage_team'] = isset($team1_data['total_seyv']) ? calculate_percentage($team1_data['total_seyv'], $team2_data['total_seyv']) : 50;
    $team2_data['seyv_percentage_team'] = 100 - $team1_data['seyv_percentage_team'];
    

    // Возвращаем структурированный массив
    return [
        'team1' => [
            'id' => $team1_id,
            'data' => $team1_data
        ],
        'team2' => [
            'id' => $team2_id,
            'data' => $team2_data
        ]
    ];
    
  }

  function getStaticMatch1($matchId, $team1_id, $team2_id)
  {
    global $dbF;

    $sql = "SELECT 
        SUM(s.`vstvor`) AS total_vstvor,
        SUM(s.`mimo`) AS total_mimo,
        SUM(s.`pasplus`) AS total_pasplus,
        SUM(s.`pasminus`) AS total_pasminus,
        SUM(s.`seyv`) AS total_seyv,
        SUM(s.`otbor`) AS total_otbor,
        SUM(s.`obvodkaplus`) AS total_obvodkaplus,
        SUM(s.`obvodkaminus`) AS total_obvodkaminus,
        p.`team`,
        m.date AS match_date
    FROM `v9ky_sostav` s
    LEFT JOIN `v9ky_player` p
        ON p.`id` = s.`player`
    LEFT JOIN `v9ky_match` m
        ON m.`id` = :match_id
    WHERE s.`matc` = :match_id
    AND p.`team` IN (:team1_id, :team2_id)
    GROUP BY p.`team`";

    $results = $dbF->query($sql, [
        ":match_id" => $matchId,
        ":team1_id" => $team1_id,
        ":team2_id" => $team2_id
    ])->findAll();

    $team1_data = [];
    $team2_data = [];

    foreach ($results as $row) {
        if ($row['team'] == $team1_id) {
            $team1_data = $row;
        } elseif ($row['team'] == $team2_id) {
            $team2_data = $row;
        }
    }

    // получаем количество ударов команды за матч
    
    $team1_data['total_udar'] = isset($team1_data['total_vstvor']) || isset($team1_data['total_mimo']) ? $team1_data['total_vstvor'] + $team1_data['total_mimo'] : 0;
    $team2_data['total_udar'] = isset($team2_data['total_vstvor']) || isset($team2_data['total_mimo']) ? $team2_data['total_vstvor'] + $team2_data['total_mimo'] : 0;

    $team1_data['total_vstvor'] = isset($team1_data['total_vstvor']) ? $team1_data['total_vstvor'] : 0;
    $team2_data['total_vstvor'] = isset($team2_data['total_vstvor']) ? $team2_data['total_vstvor'] : 0;

    $team1_data['total_mimo'] = isset($team1_data['total_mimo']) ? $team1_data['total_mimo'] : 0;
    $team2_data['total_mimo'] = isset($team2_data['total_mimo']) ? $team2_data['total_mimo'] : 0;
    
    $team1_data['total_pasplus'] = isset($team1_data['total_pasplus']) ? $team1_data['total_pasplus'] : 0;
    $team2_data['total_pasplus'] = isset($team2_data['total_pasplus']) ? $team2_data['total_pasplus'] : 0;

    $team1_data['total_pasminus'] = isset($team1_data['total_pasminus']) ? $team1_data['total_pasminus'] : 0;
    $team2_data['total_pasminus'] = isset($team2_data['total_pasminus']) ? $team2_data['total_pasminus'] : 0;

    $team1_data['total_otbor'] = isset($team1_data['total_otbor']) ? $team1_data['total_otbor'] : 0;
    $team2_data['total_otbor'] = isset($team2_data['total_otbor']) ? $team2_data['total_otbor'] : 0;

    $team1_data['total_obvodkaplus'] = isset($team1_data['total_obvodkaplus']) ? $team1_data['total_obvodkaplus'] : 0;
    $team2_data['total_obvodkaplus'] = isset($team2_data['total_obvodkaplus']) ? $team2_data['total_obvodkaplus'] : 0;

    $team1_data['total_obvodkaminus'] = isset($team1_data['total_obvodkaminus']) ? $team1_data['total_obvodkaminus'] : 0;
    $team2_data['total_obvodkaminus'] = isset($team2_data['total_obvodkaminus']) ? $team2_data['total_obvodkaminus'] : 0;

    $team1_data['total_seyv'] = isset($team1_data['total_seyv']) ? $team1_data['total_seyv'] : 0;
    $team2_data['total_seyv'] = isset($team2_data['total_seyv']) ? $team2_data['total_seyv'] : 0;


    $team1_data['udar_percentage_team'] = isset($team1_data['total_udar']) ? calculate_percentage($team1_data['total_udar'], $team2_data['total_udar']) : 50;
    $team2_data['udar_percentage_team'] = 100 - $team1_data['udar_percentage_team'];

    $team1_data['vstvor_percentage_team'] = isset($team1_data['total_vstvor']) ? calculate_percentage($team1_data['total_vstvor'], $team2_data['total_vstvor']) : 50;
    $team2_data['vstvor_percentage_team'] = 100 - $team1_data['vstvor_percentage_team'];

    $team1_data['mimo_percentage_team'] = isset($team1_data['total_mimo']) ? calculate_percentage($team1_data['total_mimo'], $team2_data['total_mimo']) : 50;
    $team2_data['mimo_percentage_team'] = 100 - $team1_data['mimo_percentage_team'];
    
    $team1_data['pasplus_percentage_team'] = isset($team1_data['total_pasplus']) ? calculate_percentage($team1_data['total_pasplus'], $team2_data['total_pasplus']) : 50;
    $team2_data['pasplus_percentage_team'] = 100 - $team1_data['pasplus_percentage_team'];

    $team1_data['pasminus_percentage_team'] = isset($team1_data['total_pasminus']) ? calculate_percentage($team1_data['total_pasminus'], $team2_data['total_pasminus']) : 50;
    $team2_data['pasminus_percentage_team'] = 100 - $team1_data['pasminus_percentage_team'];

    $team1_data['otbor_percentage_team'] = isset($team1_data['total_otbor']) ? calculate_percentage($team1_data['total_otbor'], $team2_data['total_otbor']) : 50;
    $team2_data['otbor_percentage_team'] = 100 - $team1_data['otbor_percentage_team'];

    $team1_data['obvodkaplus_percentage_team'] = isset($team1_data['total_obvodkaplus']) ? calculate_percentage($team1_data['total_obvodkaplus'], $team2_data['total_obvodkaplus']) : 50;
    $team2_data['obvodkaplus_percentage_team'] = 100 - $team1_data['obvodkaplus_percentage_team'];

    $team1_data['obvodkaminus_percentage_team'] = isset($team1_data['total_obvodkaminus']) ? calculate_percentage($team1_data['total_obvodkaminus'], $team2_data['total_obvodkaminus']) : 50;
    $team2_data['obvodkaminus_percentage_team'] = 100 - $team1_data['obvodkaminus_percentage_team'];

    $team1_data['seyv_percentage_team'] = isset($team1_data['total_seyv']) ? calculate_percentage($team1_data['total_seyv'], $team2_data['total_seyv']) : 50;
    $team2_data['seyv_percentage_team'] = 100 - $team1_data['seyv_percentage_team'];
    

    // Возвращаем структурированный массив
    return [
        'team1' => [
            'id' => $team1_id,
            'data' => $team1_data
        ],
        'team2' => [
            'id' => $team2_id,
            'data' => $team2_data
        ]
    ];
    
  }

  // Рассчитываем проценты
function calculate_percentage($value1, $value2) {
    $total = $value1 + $value2;
    return $total > 0 ? ($value1 / $total) * 100 : 50;
}

/**
 * Получает число - колчичество раз лучший игрок а матч в турнире
 * @param string|integer
 * @param string|integer
 * @return string
 */
function getBestPlayerCount($turnirId, $playerId)
{
    global $dbF;

    $sql = "SELECT COUNT(*) AS count_best_player 
        FROM `v9ky_match` 
        WHERE `turnir`= :turnir_id 
        AND `canseled` = 1 
        AND `best_player` = :player_id";
    
    $bestPlayerCount = $dbF->query($sql, [":turnir_id" => $turnirId, ":player_id" => $playerId])->find();

    return $bestPlayerCount['count_best_player'];
}



/**
 * Получает лучшего игрока матча
 * @param string|integer
 * @return array
 */
function getBestPlayerOfMatch($matchId)
{
    global $dbF;

    $sql = "SELECT 
        m.name1 AS lastname,
        m.name2 AS firstname,
        mf.`pict` AS player_photo
    FROM `v9ky_man` m
    LEFT JOIN `v9ky_man_face` mf
        ON mf.man = m.id
        
    WHERE m.`id`= (SELECT man FROM v9ky_player WHERE id = (SELECT best_player FROM v9ky_match WHERE id = :match_id AND `canseled` = 1))
    ORDER BY mf.id DESC
    LIMIT 1";
    
    $bestPlayer = $dbF->query($sql, [":match_id" => $matchId])->find();

    return $bestPlayer;
}


/**
 * Получает видео и фото итогов тура
 * @param integer|string
 * @param integer|string
 * @return array 
 */
function getResultOfTur($turnirId, $turId)
{
    global $dbF;

    $sql = "SELECT * FROM `v9ky_post_game` WHERE `turnir` = :turnir_id AND `tur` = :tur_id";

    return $dbF->query($sql, [":turnir_id" => $turnirId, ":tur_id" => $turId])->find();
}

/**
 * 
 */
function getRandomNews($limit = 20) 
{
    $limit = intval($limit);
    global $dbF;
    $sql = "SELECT * FROM `v9ky_news` ORDER BY `date1` DESC LIMIT $limit";
    $fields = $dbF->query($sql)->findAll();

    // Получаем случайный ключ массива
    $randomKey = array_rand($fields);

    // Получаем случайный элемент массива
    return $fields[$randomKey];
}

/**
 * 
 */
function getCountNews()
{
    global $dbF;
    $sql = "SELECT COUNT(*) AS count FROM `v9ky_news` ORDER BY `date1` DESC";
    $field = $dbF->query($sql)->find();
    return $field['count'];

}

/**
 * 
 */
function getNews($start = 0, $per_page = 10)
{
    global $dbF;
    $sql = "SELECT * FROM `v9ky_news` ORDER BY `date1` DESC LIMIT $start, $per_page";
    return $dbF->query($sql)->findAll();

}

/**
 * 
 */
function getOneNews($newsId)
{
    global $dbF;
    $newsId = intval($newsId);
    $sql = "SELECT * FROM `v9ky_news` WHERE id = :news_id";
    $fields = $dbF->query($sql, [":news_id" => $newsId])->find();
    return $fields;
}

/**
 * 
 */
function cleanString($str) {
    return trim(preg_replace("/\s*\([^)]*\)/", "", $str));
}

function getDateMatchesOfLeague($turnir){

    global $dbF;

    $sql = "SELECT `id`,`date`,`field`,`team1`,`team2`,`turnir`,`canseled`,`tur` FROM `v9ky_match` WHERE `turnir`= :turnir ORDER by `date`";

    $fields = $dbF->query($sql, [":turnir" => $turnir])->findAll();

    return($fields);
}

function getLastActiveDate($turnir){
    global $dbF;

    $sql = "SELECT `date` FROM `v9ky_match` WHERE `turnir`= :turnir AND `canseled` = 1 ORDER by `date` DESC LIMIT 1";

    $fields = $dbF->query($sql, [":turnir" => $turnir])->find();

    return($fields['date']);

}

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

/**
 * Определяет "сборную тура" по дате сыгранных матчей
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
    // dump($golkipers);

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

    // берем только первого 
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

    // берем только первого 
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

/**
 * Этот код находит значение ключа tur, которое встречается чаще всего. Если несколько значений встречаются одинаковое количество раз, выбирается наибольшее из них.
 * @return string
 */
function getMostFrequentTur($array) {
    // Проверяем, является ли $array массивом и не пуст ли он
    if (!is_array($array) || empty($array)) {
        return 1; // Возвращаем null, если передан не массив или он пуст
    }

    $maxTur = null; // Переменная для хранения максимального тура

    foreach ($array as $item) {
        // Проверяем, является ли $item массивом и содержит ли ключ "tur"
        if (!is_array($item) || !isset($item['tur'])) {
            continue; // Пропускаем некорректные элементы
        }

        $tur = (int)$item['tur']; // Преобразуем в число для корректного сравнения
        if ($maxTur === null || $tur > $maxTur) {
            $maxTur = $tur; // Обновляем максимальный тур
        }
    }

    return $maxTur;
}

/**
 * Создает массив отссортированных играков в рубрике Топ Игрок
 */
function getTopGravtsi($allStaticPlayers, $dataAllPlayers, $lastTur){

    // Преобразование массива
  $topPlayers = [];
  $row = [];

  foreach ($allStaticPlayers as $playerId => $matches) {
      $matchCount = count($matches); // Количество матчей
      $countGoals = array_sum(array_column($matches, 'count_goals'));
      $countAsists = array_sum(array_column($matches, 'count_asists'));
      
    //   $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
      $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
      $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
      $countRed_cards = array_sum(array_column($matches, 'red_cards'));  
      $matchIdKeys = array_keys($matches);
      // Ищем количество лучших игроков матча.
      $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
      $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;

      $totalGoals = array_sum(array_column($matches, 'count_goals'));
    //   $totalGolevoypas = array_sum(array_column($matches, 'count_asists'));
      $totalZagostrennia = array_sum(array_column($matches, 'zagostrennia'));
      $totalPasplus = array_sum(array_column($matches, 'pasplus'));
      $totalPasminus= array_sum(array_column($matches, 'pasminus'));
      $totalVtrata = array_sum(array_column($matches, 'vtrata'));
      $totalVstvor = array_sum(array_column($matches, 'vstvor'));
      $totalMimo = array_sum(array_column($matches, 'mimo'));
      $totalObvodkaplus = array_sum(array_column($matches, 'obvodkaplus'));
      $totalObvodkaminus = array_sum(array_column($matches, 'obvodkaminus'));
      $totalOtbor = array_sum(array_column($matches, 'otbor'));
      $totalOtbormin = array_sum(array_column($matches, 'otbormin'));
      $totalBlok = array_sum(array_column($matches, 'blok'));
      $totalSeyv = array_sum(array_column($matches, 'seyv'));
      $totalSeyvmin = array_sum(array_column($matches, 'seyvmin'));

      $totalKeySort = $totalGoals * 15 
          + $countAsists * 10 
          + $totalZagostrennia * 10
          + $totalPasplus * 3 
          - $totalPasminus * 3 
          - $totalVtrata * 3 
          + $totalVstvor * 7 
          - $totalMimo * 4 
          + $totalObvodkaplus * 5 
          - $totalObvodkaminus * 3 
          + $totalOtbor * 8 
          - $totalOtbormin * 5 
          + $totalBlok * 4 
          + $totalSeyv * 15 
          - $totalSeyvmin * 7;

      if(!is_array($totalKeySort)) {
  
          $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч
          
          $row = [
              'player_id' => $playerId,
              'match_count' => $matchCount,
              'total_key' => $totalKeySort,
              'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
              'match_ids' => implode(" ", $matchIdKeys),
      
              'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
              'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
              'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
              'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
              'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
              'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
  
              'count_goals' => $countGoals,
              'count_asists' => $countAsists,
            //   'golevoypas' => $countGolevoypas, 
              'yellow_cards' => $countYellowCards,
              'yellow_red_cards' => $countYellowRedCards,
              'red_cards' => $countRed_cards,
              'count_best_player_of_match' => $countBestPlayerOfMatch,
              'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
          ];
  
      } 


      // Добавляем значение для каждого матча
      foreach ($matches as $matchId => $stats) {
          if(isset($stats['tur'])){
              $row["match_{$stats[ 'tur' ]}_key"] = 
              $stats['count_goals'] * 15 
              + $stats['count_asists'] * 10
              + $stats['zagostrennia'] * 10
              + $stats['pasplus'] * 3 
              - $stats['pasminus'] * 3 
              - $stats['vtrata'] * 3
              + $stats['vstvor'] * 7 
              - $stats['mimo'] * 4 
              + $stats['obvodkaplus'] * 5
              - $stats['obvodkaminus'] * 3 
              + $stats['otbor'] * 8 
              - $stats['otbormin'] * 5
              + $stats['blok'] * 4 
              + $stats['seyv'] * 15 
              - $stats['seyvmin'] * 7;
          }
      }


      // Додаємо гравця у відповідний масив
      if ($row['v9ky']) {
        $outOfTopPlayers[] = $row;
    } else {
        $topPlayers[] = $row;
    }
  }

  // Сортируем игроков
  usort($topPlayers, function ($a, $b) use ($lastTur) {
      // 1. Сортировка по (total_key)
      if ($a['total_key'] != $b['total_key']) {
          return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
      }

      // 2. Сортировка по «Матчів» (count_matches)
      if ($a['match_count'] != $b['match_count']) {
          return ($b['match_count'] < $a['match_count']) ? 1 : -1; // По убыванию
      }
      // 3. Сортировка по последнему сыгранному матчу (total_3_match)
      if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {

      if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
          return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
      }

      }

      // Если все значения равны, оставить текущий порядок
      return 0;
  });

  // Присваиваем позиции
  $rank = 1; // Начальный порядковый номер
  foreach ($topPlayers as $index => &$player) {

      // если в последнем туре не играли оба савниваемых игрока
      if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
      
      // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
      if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
          $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
      ) {
          $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
      } else {
          $player['rank'] = $rank; // Новый ранг
      }

      } else {
      // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
      if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count']
      ) {
          $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
      } else {
          $player['rank'] = $rank; // Новый ранг
      }
      }
      $rank++; // Увеличиваем счетчик
  }

  if(isset($outOfTopPlayers)){
    $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
}
  return $topPlayers;
    
}

function getTopGolkiper($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{  
    $topPlayers = [];
    $outOfTopPlayers = [];

    foreach ($allStaticPlayers as $playerId => $matches) {
        $matchCount = 0; // Лічильник значущих матчів
        $totalKeySort = calculateArrayByColumn($keySort, $matches); // Загальна статистика

        if ($keySort == "golkiper" && is_array($totalKeySort)) {
            $totalSaves = $totalKeySort['seyv'] + $totalKeySort['seyvmin'];

            // Підрахунок значущих матчів (тільки якщо були сейви)
            foreach ($matches as $stats) {
                if ((isset($stats['seyv']) && $stats['seyv'] > 0) || (isset($stats['seyvmin']) && $stats['seyvmin'] > 0)) {
                    $matchCount++;
                }
            }

            // Не додаємо гравців, у яких немає матчів і сейвів
            if ($totalKeySort['total_value'] == 0 && $matchCount == 0) {
                continue;
            }

            // Середнє значення по ключу $keySort за матч
            $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0;

            $row = [
                'player_id' => $playerId,
                'match_count' => $matchCount,
                'total_key' => $totalKeySort['total_value'],
                'key_per_match' => round($keySortPerMatch, 2),
                'seyv' => $totalKeySort['seyv'],
                'seyvmin' => $totalKeySort['seyvmin'],
                'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
                'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
                'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
                'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
                'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
                'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
                'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
                'is_top' => ($matchCount >= 2 && $totalSaves >= 10) // Відзначаємо ТОП гравців
                
            ];

            // Додаємо значення для кожного матчу
            foreach ($matches as $matchId => $stats) {
                $seyv = isset($stats['seyv']) ? $stats['seyv'] : 0;
                $seyvmin = isset($stats['seyvmin']) ? $stats['seyvmin'] : 0;
                $denominator = $seyv + $seyvmin;

                if (isset($stats['tur'])) {
                    $row["match_{$stats['tur']}_key"] = ($denominator == 0) ? '-' : round((100 / $denominator) * $stats['seyv'], 1) . "%";
                }
            }

            // Додаємо гравця у відповідний масив
            if ($row['is_top'] && !$row['v9ky']) {
                $topPlayers[] = $row;
            } else {
                $outOfTopPlayers[] = $row;
            }

            // // Додаємо гравця у відповідний масив
            // if ($row['is_top'] && !$row['v9ky']) {
            //     $topPlayers[] = $row;
            // } elseif(!$row['is_top'] && !$row['v9ky']) {
            //     $outOfTopPlayers[] = $row;
            // } else {
            //     $v9kyPlayers = $row;
            // }
        }
    }

    // Сортуємо ТОП-воротарів
    usort($topPlayers, function ($a, $b) use ($lastTur) {

         // 2. Сортування за total_key (сумарні очки)
        if ($a['total_key'] != $b['total_key']) {
            return ($b['total_key'] > $a['total_key']) ? 1 : -1;
        }

        // 3. Сортування за кількістю матчів (match_count)
        if ($a['match_count'] != $b['match_count']) {
            return ($b['match_count'] > $a['match_count']) ? 1 : -1;
        }

        // 4. Сортування за останнім туром (якщо є значення)
        if (isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
            if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
                return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1;
            }
        }
        return 0;
    });

    // Сортуємо "не ТОП" воротарів, просто по матчах
    usort($outOfTopPlayers, function ($a, $b) {
        return $b['total_key'] - $a['total_key'];
    });

    $topPlayers = array_merge($topPlayers, $outOfTopPlayers);

    // Присвоюємо позиції ТОП гравцям
    $rank = 1;
    foreach ($topPlayers as $index => &$player) {
        if ($index > 0 && $topPlayers[$index - 1]['total_key'] === $player['total_key']
            && $topPlayers[$index - 1]['match_count'] === $player['match_count']
        ) {
            $player['rank'] = $topPlayers[$index - 1]['rank'];
        } else {
            $player['rank'] = $rank;
        }
        $rank++;
    }

    // Об'єднуємо ТОП і НЕ ТОП гравців (ТОП йдуть першими, потім всі інші)
    return $topPlayers;
}

function getTopDriblings($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{
     // Преобразование массива
     $topPlayers = [];
  
     foreach ($allStaticPlayers as $playerId => $matches) {
       
       $matchCount = count($matches); // Количество матчей
       $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
       $countGoals = array_sum(array_column($matches, 'count_goals'));
       $countAsists = array_sum(array_column($matches, 'count_asists'));
       $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
       $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
       $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
       $countRed_cards = array_sum(array_column($matches, 'red_cards'));
   
       $matchIdKeys = array_keys($matches);
       // Ищем количество лучших игроков матча.
       $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
       $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;
   
       // Дріблінг
       if ($keySort == "dribling" && is_array($totalKeySort)) {
         // Исключаем игроков с нулевыми показателями дриблинга
         if ($totalKeySort['obvodka_plus'] == 0 && $totalKeySort['obvodka_minus'] == 0) {
             continue; // Пропускаем этого игрока
         }
           
         $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
         
       
         $row = [
           'player_id' => $playerId,
           'match_count' => $matchCount,
           'total_key' => $totalKeySort['total_value'],
           'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
           'obvodkaplus' => $totalKeySort['obvodka_plus'],
           'obvodkaminus' => $totalKeySort['obvodka_minus'],  
           'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
           'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
           'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
           'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
           'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
           'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
           'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0
         ];
   
         
         // Добавляем значение для каждого матча
         foreach ($matches as $matchId => $stats) {
           if(isset($stats[ 'tur' ])) {
             $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'obvodkaplus' ] - $stats[ 'obvodkaminus' ];
           }  
         }
 
         // Додаємо гравця у відповідний масив
         if ($row['v9ky']) {
             $outOfTopPlayers[] = $row;
         } else {
             $topPlayers[] = $row;
         }
         
       }
   
     }
   
     // Сортируем игроков
     usort($topPlayers, function ($a, $b) use ($lastTur) {
       // 1. Сортировка по (total_key)
       if ($a['total_key'] != $b['total_key']) {
           return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
       }
   
       // 2. Сортировка по «Матчів» (count_matches)
       if ($a['match_count'] != $b['match_count']) {
           return ($b['match_count'] < $a['match_count']) ? 1 : -1; // По убыванию
       }
       // 3. Сортировка по последнему сыгранному матчу (total_3_match)
       if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
   
         if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
             return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
         }
       }
       // Если все значения равны, оставить текущий порядок
       return 0;
     });
   
     // Присваиваем позиции
     $rank = 1; // Начальный порядковый номер
     foreach ($topPlayers as $index => &$player) {
   
       // если в последнем туре не играли оба сравниваемых игрока
       if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
         
         // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
         if (
             $index > 0 &&
             $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
             $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
             $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
         ) {
             $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
         } else {
             $player['rank'] = $rank; // Новый ранг
         }
   
       } else {
         // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
         if (
           $index > 0 &&
           $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
           $topPlayers[$index - 1]['match_count'] === $player['match_count']
         ) {
             $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
         } else {
             $player['rank'] = $rank; // Новый ранг
         }
       }
         $rank++; // Увеличиваем счетчик
     }
 
     
     if( isset( $outOfTopPlayers ) ) {
        $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
      }
     return $topPlayers;
  
  
}  

function getTopUdars($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{
    // Преобразование массива
    $topPlayers = [];
  
    foreach ($allStaticPlayers as $playerId => $matches) {
      
      $matchCount = count($matches); // Количество матчей
      $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
      $countGoals = array_sum(array_column($matches, 'count_goals'));
      $countAsists = array_sum(array_column($matches, 'count_asists'));
      $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
      $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
      $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
      $countRed_cards = array_sum(array_column($matches, 'red_cards'));
  
      $matchIdKeys = array_keys($matches);
      // Ищем количество лучших игроков матча.
      $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
      $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;
  
        // Удар
      if ($keySort == "udar" && is_array($totalKeySort)) {
        // Исключаем игроков с нулевыми показателями дриблинга
        if ($totalKeySort['udar_plus'] == 0 && $totalKeySort['udar_minus'] == 0) {
            continue; // Пропускаем этого игрока
        }
          
        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'udarplus' => $totalKeySort['udar_plus'],
          'udarminus' => $totalKeySort['udar_minus'],

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
          'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0
        ];

        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if( isset( $stats[ 'tur' ] ) ) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'vstvor' ] - $stats[ 'mimo' ];
          }
        }
        
      }
  
       // Додаємо гравця у відповідний масив
       if ($row['v9ky']) {
            $outOfTopPlayers[] = $row;
        } else {
            $topPlayers[] = $row;
        }
  
    }
  
    // Сортируем игроков
    usort($topPlayers, function ($a, $b) use ($lastTur) {
      // 1. Сортировка по (total_key)
      if ($a['total_key'] != $b['total_key']) {
          return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
      }
  
      // 2. Сортировка по «Матчів» (count_matches)
      if ($a['match_count'] != $b['match_count']) {
          return ($b['match_count'] > $a['match_count']) ? 1 : -1; // По убыванию
      }
      // 3. Сортировка по последнему сыгранному матчу (total_3_match)
      if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
  
        if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
            return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
        }
      }
      // Если все значения равны, оставить текущий порядок
      return 0;
    });
  
    // Присваиваем позиции
    $rank = 1; // Начальный порядковый номер
    foreach ($topPlayers as $index => &$player) {
  
      // если в последнем туре не играли оба сравниваемых игрока
      if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
        
        // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
        if (
            $index > 0 &&
            $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
            $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
            $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
        ) {
            $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
        } else {
            $player['rank'] = $rank; // Новый ранг
        }
  
      } else {
        // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
        if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count']
        ) {
            $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
        } else {
            $player['rank'] = $rank; // Новый ранг
        }
      }
        $rank++; // Увеличиваем счетчик
    }
  
    if( isset( $outOfTopPlayers ) ) {
        $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
    }
  
    return $topPlayers;
}

function getTopBombardir($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{  

    // Преобразование массива
   $topPlayers = [];
  
   foreach ($allStaticPlayers as $playerId => $matches) {
       $matchCount = count($matches); // Количество матчей
       $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
       $countGoals = array_sum(array_column($matches, 'count_goals'));
       $countAsists = array_sum(array_column($matches, 'count_asists'));
       $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
       $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
       $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
       $countRed_cards = array_sum(array_column($matches, 'red_cards'));
  
       $matchIdKeys = array_keys($matches);
       // Ищем количество лучших игроков матча.
       $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
       $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;
            
       // Инициализируем строку для таблицы. Для Бомбардиров и Асистентов
       if(!is_array($totalKeySort)) {
         
         $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч
         
         $row = [
             'player_id' => $playerId,
             'match_count' => $matchCount,
             'total_key' => $totalKeySort,
             'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
             'match_ids' => implode(" ", $matchIdKeys),
  
             'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
             'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
             'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
             'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
             'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
             'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
  
             'count_goals' => $countGoals,
             'count_asists' => $countAsists,
             'golevoypas' => $countGolevoypas, 
             'yellow_cards' => $countYellowCards,
             'yellow_red_cards' => $countYellowRedCards,
             'red_cards' => $countRed_cards,
             'count_best_player_of_match' => $countBestPlayerOfMatch,
             'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
         ];
  
       } 

       // Добавляем значение для каждого матча
       foreach ($matches as $matchId => $stats) {
        if(isset($stats[ 'tur' ])) {
          $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'count_goals' ];
        }

      }
  
      // Додаємо гравця у відповідний масив
      if ($row['v9ky']) {
        $outOfTopPlayers[] = $row;
      } else {
        $topPlayers[] = $row;
      }
      
   }
  
  
     
   // Сортируем игроков
   usort($topPlayers, function ($a, $b) use ($lastTur) {
     // 1. Сортировка по (total_key)
     if ($a['total_key'] != $b['total_key']) {
         return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
     }
  
     // 2. Сортировка по «Матчів» (count_matches)
     if ($a['match_count'] != $b['match_count']) {
         return ($b['match_count'] < $a['match_count']) ? 1 : -1; // По убыванию
     }
     // 3. Сортировка по последнему сыгранному матчу (total_3_match)
     if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
  
       if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
           return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
       }
  
     }
  
     // Если все значения равны, оставить текущий порядок
     return 0;
   });
  
   // Присваиваем позиции
   $rank = 1; // Начальный порядковый номер
   foreach ($topPlayers as $index => &$player) {
  
     // если в последнем туре не играли оба сравниваемых игрока
     if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
       
       // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
       if (
           $index > 0 &&
           $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
           $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
           $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
       ) {
           $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
       } else {
           $player['rank'] = $rank; // Новый ранг
       }
  
     } else {
       // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
       if (
         $index > 0 &&
         $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
         $topPlayers[$index - 1]['match_count'] === $player['match_count']
       ) {
           $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
       } else {
           $player['rank'] = $rank; // Новый ранг
       }
     }
       $rank++; // Увеличиваем счетчик
   }
  
   if( isset( $outOfTopPlayers ) ) {
    $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
  }
    
    return $topPlayers;
  
}

function getTopAsists($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{  

    // Преобразование массива
   $topPlayers = [];
  
   foreach ($allStaticPlayers as $playerId => $matches) {
       $matchCount = count($matches); // Количество матчей
       $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
       $countGoals = array_sum(array_column($matches, 'count_goals'));
       $countAsists = array_sum(array_column($matches, 'count_asists'));
       $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
       $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
       $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
       $countRed_cards = array_sum(array_column($matches, 'red_cards'));
  
       $matchIdKeys = array_keys($matches);
       // Ищем количество лучших игроков матча.
       $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
       $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;
            
       // Инициализируем строку для таблицы. Для Бомбардиров и Асистентов
       if(!is_array($totalKeySort)) {
         
         $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч
         
         $row = [
             'player_id' => $playerId,
             'match_count' => $matchCount,
             'total_key' => $totalKeySort,
             'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
             'match_ids' => implode(" ", $matchIdKeys),
  
             'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
             'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
             'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
             'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
             'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
             'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
  
             'count_goals' => $countGoals,
             'count_asists' => $countAsists,
             'golevoypas' => $countGolevoypas, 
             'yellow_cards' => $countYellowCards,
             'yellow_red_cards' => $countYellowRedCards,
             'red_cards' => $countRed_cards,
             'count_best_player_of_match' => $countBestPlayerOfMatch,
             'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
         ];
  
       } 

         // Асист
      if( $keySort == 'count_goals' || $keySort == 'golevoypas' || $keySort == 'count_asists'){
        
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ] )) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ $keySort ];         
          }
        }

      }
  
      // Додаємо гравця у відповідний масив
      if ($row['v9ky']) {
        $outOfTopPlayers[] = $row;
      } else {
        $topPlayers[] = $row;
      }
      
   }  
     
   // Сортируем игроков
   usort($topPlayers, function ($a, $b) use ($lastTur) {
     // 1. Сортировка по (total_key)
     if ($a['total_key'] != $b['total_key']) {
         return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
     }
  
     // 2. Сортировка по «Матчів» (count_matches)
     if ($a['match_count'] != $b['match_count']) {
         return ($b['match_count'] < $a['match_count']) ? 1 : -1; // По убыванию
     }
     // 3. Сортировка по последнему сыгранному матчу (total_3_match)
     if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
  
       if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
           return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
       }
  
     }
  
     // Если все значения равны, оставить текущий порядок
     return 0;
   });
  
   // Присваиваем позиции
   $rank = 1; // Начальный порядковый номер
   foreach ($topPlayers as $index => &$player) {
  
     // если в последнем туре не играли оба сравниваемых игрока
     if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
       
       // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
       if (
           $index > 0 &&
           $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
           $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
           $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
       ) {
           $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
       } else {
           $player['rank'] = $rank; // Новый ранг
       }
  
     } else {
       // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
       if (
         $index > 0 &&
         $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
         $topPlayers[$index - 1]['match_count'] === $player['match_count']
       ) {
           $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
       } else {
           $player['rank'] = $rank; // Новый ранг
       }
     }
       $rank++; // Увеличиваем счетчик
   }
  
   if( isset( $outOfTopPlayers ) ) {
    $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
  }
    
    return $topPlayers;
  
}

function getTopZhusnuks($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{
    // Преобразование массива
    $topPlayers = [];
  
    foreach ($allStaticPlayers as $playerId => $matches) {
      
      $matchCount = count($matches); // Количество матчей
      $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
      $countGoals = array_sum(array_column($matches, 'count_goals'));
      $countAsists = array_sum(array_column($matches, 'count_asists'));
      $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
      $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
      $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
      $countRed_cards = array_sum(array_column($matches, 'red_cards'));
  
      $matchIdKeys = array_keys($matches);
      // Ищем количество лучших игроков матча.
      $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
      $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;

      $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч

      $row = [
        'player_id' => $playerId,
        'match_count' => $matchCount,
        'total_key' => $totalKeySort,
        'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
        'match_ids' => implode(" ", $matchIdKeys),

        'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
        'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
        'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
        'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
        'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
        'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',

        'count_goals' => $countGoals,
        'count_asists' => $countAsists,
        'golevoypas' => $countGolevoypas, 
        'yellow_cards' => $countYellowCards,
        'yellow_red_cards' => $countYellowRedCards,
        'red_cards' => $countRed_cards,
        'count_best_player_of_match' => $countBestPlayerOfMatch,
        'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
      ];


       // Захисник
       if( $keySort == 'zahusnuk') {
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if( isset( $stats[ 'tur' ] ) ) {            
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'otbor' ] + $stats[ 'blok' ];                            
          }
        }
      }
  
      // Додаємо гравця у відповідний масив
      if ($row['v9ky']) {
        $outOfTopPlayers[] = $row;
      } else {
        $topPlayers[] = $row;
      }
  
    }
  
    // Сортируем игроков
    usort($topPlayers, function ($a, $b) use ($lastTur) {
      // 1. Сортировка по (total_key)
      if ($a['total_key'] != $b['total_key']) {
          return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
      }
  
      // 2. Сортировка по «Матчів» (count_matches)
      if ($a['match_count'] != $b['match_count']) {
          return ($b['match_count'] < $a['match_count']) ? 1 : -1; // По убыванию
      }
      // 3. Сортировка по последнему сыгранному матчу (total_3_match)
      if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
  
        if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
            return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
        }
      }
      // Если все значения равны, оставить текущий порядок
      return 0;
    });
  
    // Присваиваем позиции
    $rank = 1; // Начальный порядковый номер
    foreach ($topPlayers as $index => &$player) {
  
      // если в последнем туре не играли оба сравниваемых игрока
      if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
        
        // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
        if (
            $index > 0 &&
            $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
            $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
            $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
        ) {
            $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
        } else {
            $player['rank'] = $rank; // Новый ранг
        }
  
      } else {
        // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
        if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count']
        ) {
            $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
        } else {
            $player['rank'] = $rank; // Новый ранг
        }
      }
        $rank++; // Увеличиваем счетчик
    }
  
    if( isset( $outOfTopPlayers ) ) {
        $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
      }
  
    return $topPlayers;
}

function getTopPases($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{  

    // Преобразование массива
   $topPlayers = [];
  
   foreach ($allStaticPlayers as $playerId => $matches) {
       $matchCount = count($matches); // Количество матчей
       $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
       $countGoals = array_sum(array_column($matches, 'count_goals'));
       $countAsists = array_sum(array_column($matches, 'count_asists'));
       $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
       $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
       $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
       $countRed_cards = array_sum(array_column($matches, 'red_cards'));
  
       $matchIdKeys = array_keys($matches);
       // Ищем количество лучших игроков матча.
       $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
       $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;
            
       // Инициализируем строку для таблицы. Для Бомбардиров и Асистентов
       if(!is_array($totalKeySort)) {
         
         $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч
         
         $row = [
             'player_id' => $playerId,
             'match_count' => $matchCount,
             'total_key' => $totalKeySort,
             'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
             'match_ids' => implode(" ", $matchIdKeys),
  
             'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
             'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
             'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
             'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
             'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
             'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
  
             'count_goals' => $countGoals,
             'count_asists' => $countAsists,
             'golevoypas' => $countGolevoypas, 
             'yellow_cards' => $countYellowCards,
             'yellow_red_cards' => $countYellowRedCards,
             'red_cards' => $countRed_cards,
             'count_best_player_of_match' => $countBestPlayerOfMatch,
             'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
         ];
  
       } 

      // Пас
      if ($keySort == "pas" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'obvodkaplus' => isset($totalKeySort['obvodka_plus']) ? $totalKeySort['obvodka_plus'] : 0,
          'obvodkaminus' => isset($totalKeySort['obvodka_minus']) ? $totalKeySort['obvodka_minus'] : 0,
          'zagostrennia' => isset($totalKeySort['zagostrennia']) ? $totalKeySort['zagostrennia'] : 0,
          'pasplus' => isset($totalKeySort['pasplus']) ? $totalKeySort['pasplus'] : 0,
          'pasminus' => isset($totalKeySort['pasminus']) ? $totalKeySort['pasminus'] : 0,

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
          'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
        ];
        
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ])){
            $row["match_{$stats[ 'tur' ]}_key"] = ($stats[ 'zagostrennia' ] * 5 + $stats[ 'pasplus' ]) - $stats[ 'pasminus' ] * 3;
          }
        }
        
      }    
  
      // Додаємо гравця у відповідний масив
      if ($row['v9ky']) {
        $outOfTopPlayers[] = $row;
      } else {
        $topPlayers[] = $row;
      }
      
   }  
     
   // Сортируем игроков
   usort($topPlayers, function ($a, $b) use ($lastTur) {
     // 1. Сортировка по (total_key)
     if ($a['total_key'] != $b['total_key']) {
         return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
     }
  
     // 2. Сортировка по «Матчів» (count_matches)
     if ($a['match_count'] != $b['match_count']) {
         return ($b['match_count'] > $a['match_count']) ? 1 : -1; // По убыванию
     }
     // 3. Сортировка по последнему сыгранному матчу (total_3_match)
     if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
  
       if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
           return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
       }
  
     }
  
     // Если все значения равны, оставить текущий порядок
     return 0;
   });
  
   // Присваиваем позиции
   $rank = 1; // Начальный порядковый номер
   foreach ($topPlayers as $index => &$player) {
  
     // если в последнем туре не играли оба сравниваемых игрока
     if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
       
       // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
       if (
           $index > 0 &&
           $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
           $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
           $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
       ) {
           $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
       } else {
           $player['rank'] = $rank; // Новый ранг
       }
  
     } else {
       // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
       if (
         $index > 0 &&
         $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
         $topPlayers[$index - 1]['match_count'] === $player['match_count']
       ) {
           $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
       } else {
           $player['rank'] = $rank; // Новый ранг
       }
     }
       $rank++; // Увеличиваем счетчик
   }
  
    if( isset( $outOfTopPlayers ) ) {
        $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
    }
    
    return $topPlayers;
  
}


function getCupData($turnir){
    global $dbF;

    $sql = "SELECT `cup_stage`
            FROM `v9ky_match`
            WHERE turnir=:turnir 
            AND `cup_stage` > 0 
            GROUP BY cup_stage";

    $cupgroup = $dbF->query($sql, [":turnir" => $turnir])->findAll();

    $cupData = [];

    if (!empty($cupgroup)){

        $cupData = [];
        foreach($cupgroup as $item){
            $sql1 = "SELECT 
                    m.`cup_stage`, 
                    m.`id` AS match_id, 
                    m.`canseled`,
                    m.`team1`,
                    t1.`name` AS team1_name,
                    m.`gols1` AS goals1,
                    m.`gols2` AS goals2,
                    m.`team2`,
                    t2.`name` AS team2_name
                    FROM `v9ky_match` m
                    LEFT JOIN `v9ky_team` t1 
                            ON t1.`id` = m.`team1`
                    LEFT JOIN `v9ky_team` t2 
                            ON t2.`id` = m.`team2`
                    WHERE m.turnir=:turnir
                    AND m.cup_stage=:item_cup_stage";

            $cups1 = $dbF->query($sql1, [":turnir" => $turnir, ":item_cup_stage" => $item['cup_stage']])->findAll();
            

            foreach($cups1 as $key => $cup){
                
                // Если нет результата кубка
                if(!$cup['team1'] || !$cup['team2']) {
                    $cup['team1_name'] = 'Команда 1';
                    $cup['team2_name'] = 'Команда 2';
                }

                switch ($item['cup_stage']){
                    case 1: $cupData["Фінал"][$key] = $cup; break;
                    case 2: $cupData["Матч за 3-е місце"][$key] = $cup; break;
                    case 3: $cupData["Півфінал"][$key] = $cup; break;
                    case 4: $cupData["1/4 КУБКУ"][$key] = $cup; break;
                    case 5: $cupData["1/8 КУБКУ"][$key] = $cup; break;
                    case 6: $cupData["1/16 КУБКУ"][$key] = $cup; break;
                    case 7: $cupData["1/32 КУБКУ"][$key] = $cup; break;
                    case 8: $cupData["1/64 КУБКУ"][$key] = $cup; break;
                    case 10: $cupData["Золотий матч"][$key] = $cup; break;
                }
            }               
                
        }   
        
        foreach($cupData as $k => $v){

            if($k == 'Фінал') {
                foreach($v as $v1){
                    if($v1['goals1'] || $v1['goals2']){
                        if( $v1['goals1'] > $v1['goals2']){
                                $cupData[$k][0]['cup1'] = 'goldcup.png';
                                $cupData[$k][0]['cup2'] = 'silvercup.png';
                        } else {
                                $cupData[$k][0]['cup1'] = 'silvercup.png';   
                                $cupData[$k][0]['cup2'] = 'goldcup.png';
                        }
                    }
                }
            }

            if($k == 'Матч за 3-е місце') {
                foreach($v as $v1){
                    if($v1['goals1'] || $v1['goals2']){
                        if($v1['goals1'] > $v1['goals2']){
                                $cupData[$k][0]['cup1'] = 'bronzecup.png';
                        } else {
                                $cupData[$k][0]['cup2'] = 'bronzecup.png';
                        }
                    }
                }
            }

        }
            
    }

    if(empty($cupData)) {                
            return 0;
    } 

    return $cupData;
}

/**
 * Проверяет, является ли текущий тур кубковым.
 */
function isCupCurrentTur($turnir, $currentTur) {
    global $dbF;
    $sql = "SELECT cup_stage FROM v9ky_match WHERE turnir = :turnir AND tur = :tur LIMIT 1";
    $result = $dbF->query($sql, ['turnir' => $turnir, 'tur' => $currentTur])->find();
    return isset($result['cup_stage']) && $result['cup_stage'] > 0;
}

/**
 * Получает карточки по типу (красные/желтые).
 */
function getCardsByType($dbF, $turnir, $cardType, $currentTur, $cupStage = 0) {
    $tableName = $cardType === 'red' ? 'v9ky_red' : 'v9ky_yellow';
    $cupStageCondition = $cupStage > 0 ? "AND mtc.cup_stage > 0" : "AND mtc.cup_stage = 0";
    
    $sql = "
        SELECT 
            c.`player` as player_id,
            p.`man`,
            m.`name1` as lastname,
            m.`name2` as firstname,
            mf.`pict` as player_photo,
            t.`pict` as team_logo,
            t.`name` as team,
            mtc.`tur`
        FROM $tableName c
        LEFT JOIN `v9ky_player` p ON p.`id` = c.`player`
        LEFT JOIN `v9ky_man` m ON m.`id` = p.`man`
        LEFT JOIN `v9ky_man_face` mf ON mf.`id` = (
            SELECT MAX(id) FROM `v9ky_man_face` mff WHERE mff.`man` = p.`man`
        )
        LEFT JOIN `v9ky_team` t ON t.`id` = p.`team`
        LEFT JOIN `v9ky_match` mtc ON mtc.`id` = c.`matc`
        WHERE mtc.`turnir` = :turnir
        $cupStageCondition
        ORDER BY mtc.`tur`, mtc.`date`
    ";

    $cards = $dbF->query($sql, ['turnir' => $turnir])->findAll();
    $result = [];

    foreach ($cards as $player) {
        $id = $player['player_id'];
        if (!isset($result[$id])) {
            $result[$id] = [
                'player_id' => $id,
                'lastname' => $player['lastname'],
                'firstname' => $player['firstname'],
                'team_logo' => $player['team_logo'],
                'team' => $player['team'],
                'player_photo' => $player['player_photo'],
                'red' => [],
                'yellow' => []
            ];
        }

        if ($player['tur'] <= $currentTur) {
            $result[$id][$cardType][] = $player['tur'];
        }
    }

    return $result;
}

/**
 * Получаем табличные дянные по дисквалификации - игроки с ЖК и КК 
 */
function getTableCards($redCards, $yellowCards) {
    $tableCards = [];

    foreach ([$redCards, $yellowCards] as $source) {
        foreach ($source as $id => $data) {
            if (!isset($tableCards[$id])) {
                $tableCards[$id] = [
                    'player_id' => $data['player_id'],
                    'lastname' => $data['lastname'],
                    'firstname' => $data['firstname'],
                    'team_logo' => $data['team_logo'],
                    'team' => $data['team'],
                    'player_photo' => $data['player_photo'],
                    'red' => [],
                    'yellow' => [],
                    'tur' => []
                ];
            }

            $tableCards[$id]['red'] = array_merge($tableCards[$id]['red'], $data['red']);
            $tableCards[$id]['yellow'] = array_merge($tableCards[$id]['yellow'], $data['yellow']);
            $tableCards[$id]['tur'] = array_merge($tableCards[$id]['tur'], $data['red'], $data['yellow']);
        }
    }


    // Сортировка по количеству карточек (общая)
    usort($tableCards, function($a, $b) {
        return count($b['tur']) - count($a['tur']);
    });

    return $tableCards;
}

/**
 * Получаем дисквалифицированных игроков по красным и желтым карточкам.
 * 
 * @param array $tableCards - Табличные данные игроков с карточками.
 * @param int $currentTur - Текущий тур.
 * @param int $countCards - Количество желтых карточек для дисквалификации.
 * @return array - Список дисквалифицированных игроков.
 */
function getDisqualifiedPlayers($tableCards, $currentTur, $countCards = 3) {
    $disqualifiedPlayers = [];

    foreach ($tableCards as $playerId => &$player) {
        $player['yellow_red'] = 0;
        $turYellowCounts = [];

        // Считаем количество жёлтых в каждом туре
        foreach ($player['yellow'] as $turNum) {
            if (!isset($turYellowCounts[$turNum])) {
                $turYellowCounts[$turNum] = 0;
            }
            $turYellowCounts[$turNum]++;
        }

        // Если в одном туре >=2 жёлтых — прибавляем 1 к yellow_red
        foreach ($turYellowCounts as $count) {
            if ($count >= 2) {
                $player['yellow_red'] += 1;
            }
        }
    }

    foreach ($tableCards as $playerId => $player) {
        // Проверка красной карточки
        if (!empty($player['red'])) {
            $lastRedTur = end($player['red']);
            if ($lastRedTur == $currentTur) {
                $disqualifiedPlayers[$player['player_id']] = [
                    'player_id' => $player['player_id'],
                    'lastname' => $player['lastname'],
                    'firstname' => $player['firstname'],
                    'team_logo' => $player['team_logo'],
                    'team' => $player['team'],
                    'player_photo' => $player['player_photo'],
                    'red' => $player['red'],
                    'yellow' => $player['yellow'],
                ];
            }
        }

        // Проверка желтых карточек
        $yellowTotal = count($player['yellow']) + $player['yellow_red'];
        if ($yellowTotal > 0 && $yellowTotal % $countCards == 0) {
            $lastYellowTur = end($player['yellow']);
            if ($lastYellowTur == $currentTur) {
                $disqualifiedPlayers[$player['player_id']] = [
                    'player_id' => $player['player_id'],
                    'lastname' => $player['lastname'],
                    'firstname' => $player['firstname'],
                    'team_logo' => $player['team_logo'],
                    'team' => $player['team'],
                    'player_photo' => $player['player_photo'],
                    'red' => $player['red'],
                    'yellow' => $player['yellow'],
                    
                ];
            }
        }
    }

    return array_values($disqualifiedPlayers);
}

