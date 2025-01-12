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
            (SELECT COUNT(*) AS count_goals FROM v9ky_gol g WHERE g.player= s.player and g.matc = s.matc) AS count_goals,
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
function getFields(){
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
	s.nomer AS nomer
    FROM `v9ky_player` p
    LEFT JOIN (
        SELECT player, nomer FROM v9ky_sostav WHERE `matc` = :match_id
    ) s ON s.player = p.id
    LEFT JOIN 
        v9ky_man m ON m.id = p.man

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
    (SELECT COUNT(*) FROM `v9ky_gol` WHERE matc = :match_id AND player = p.`id`) AS goals_scored,
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
        a.time AS event_time   
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
        g.time AS event_time
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
        y.time AS event_time
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
        r.time AS event_time
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
        yr.time AS event_time
    FROM v9ky_yellow_red yr
    LEFT JOIN v9ky_man m ON m.id = (SELECT man FROM v9ky_player WHERE id = yr.player)
    WHERE yr.matc = :match_id

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
function getPlayersOfTeam($teamId)
{
    global $dbF;

    $sql = "SELECT * 
        FROM `v9ky_player` p 
        WHERE `team` = :team_id 
        AND `active` = '1' 
        ORDER BY 
            `active` desc, 
            `vibuv`, 
            `id` = (
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
function getTeamHeads($teamId)
{
    global $dbF;

    $sql = "SELECT p.id as player_id,
                p.name1 as player_lastname, 
                p.name2 as player_firstname, 
                p.name3 as player_middlename,
                mf.pict as player_photo, 
                t.pict as team_photo,
                p.amplua as amplua
          FROM v9ky_team t 
          LEFT JOIN 
            v9ky_player p ON p.id IN (t.capitan, t.manager, t.trainer)
          LEFT JOIN
            v9ky_man_face mf ON p.man = mf.man
            WHERE t.id = :team_id
            GROUP BY p.id
            ORDER BY p.amplua DESC";

    $fields = $dbF->query($sql, [":team_id" => $teamId])->findAll();

    $teamHeads = [];
    $iconHeads = [ 0 => 'manager-icon.svg', 4 => 'coach-icon.svg', 5 => 'cap-icon.svg'];

    foreach ($fields as $field) {
        $amplua = $field['amplua'];
        $teamHeads[$amplua] = $field;  
        $teamHeads[$amplua]['icon'] = $iconHeads[$amplua];
    }


     return $teamHeads;
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
    t.`ru` AS name,
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
                                
        } else {
            $sql_man = "SELECT 
                mf.`pict` AS photo,
                m.`name1`AS lastname,
                m.`name2` AS firstname
                FROM `v9ky_man_face` mf
                LEFT JOIN `v9ky_man` m ON m.`id` = mf.`man`
                WHERE `man` = :man_id";
            $playerData = $dbF->query( $sql_man, [ ":man_id" => $field['man_id'] ] )->find();   
            // Создаем объект DateTime
            $date = new DateTime($field['date']);
            // Преобразуем дату в формат дд.мм.гггг
            $formattedDate = $date->format('d.m.Y');
            $transfers[$key]['date'] = $formattedDate;
            $transfers[$key]['lastname'] = $playerData['lastname'];
            $transfers[$key]['firstname'] = $playerData['firstname'];
            $transfers[$key]['photo'] = $playerData['photo'] ? $playerData['photo'] : 'avatar1.jpg';
            $transfers[$key]['action'] = $field['del_id'] ?  0 : 1; // если в $field['del_id'] = 0 - это добавление игрока в команду. Иначе удаление.         
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

//  /**
//   * Получает статистику мастча.
//   * @param string
//   * @return array
//   */
//   function getStaticMatch($matchId)
//   {
//     global $dbF;

//     $sql = "SELECT 
//         `stat_vladen1`, 
//         `stat_vladen2`, 
//         `stat_ydari1`, 
//         `stat_ydari2`, 
//         `stat_vstvor1`, 
//         `stat_vstvor2`, 
//         `stat_pas1`, 
//         `stat_pas2`, 
//         `stat_ygol1`, 
//         `stat_ygol2`, 
//         `stat_fols1`, 
//         `stat_fols2` 
//     FROM `v9ky_match` 
//     WHERE `id` = :match_id";

//     $fields = $dbF->query($sql, [":match_id" => $matchId])->find();

//     // Заменяем null на 0
//     foreach( $fields as $key => $field ){
//         if( $field == null ) {
//             $fields[$key] = 0;
//         }
//     }

//     // Владение мячом
//     $stat_vladen_percent = getPercentAge($fields['stat_vladen1'], $fields['stat_vladen2']);
//     $fields['stat_vladen1_percent'] = $stat_vladen_percent[0];
//     $fields['stat_vladen2_percent'] = $stat_vladen_percent[1];

//     // Удары
//     $stat_ydari_percent = getPercentAge($fields['stat_ydari1'], $fields['stat_ydari2']);
//     $fields['stat_ydari1_percent'] = $stat_ydari_percent[0];
//     $fields['stat_ydari2_percent'] = $stat_ydari_percent[1];

//     // Удары в створ ворот
//     $stat_vstvor_percent = getPercentAge($fields['stat_vstvor1'], $fields['stat_vstvor2']);
//     $fields['stat_vstvor1_percent'] = $stat_vstvor_percent[0];
//     $fields['stat_vstvor2_percent'] = $stat_vstvor_percent[1];

//     // Пасы
//     $stat_pas_percent = getPercentAge($fields['stat_pas1'], $fields['stat_pas2']);
//     $fields['stat_pas1_percent'] = $stat_pas_percent[0];
//     $fields['stat_pas2_percent'] = $stat_pas_percent[1];

//     // Угловые
//     $stat_ygol_percent = getPercentAge($fields['stat_ygol1'], $fields['stat_ygol2']);
//     $fields['stat_ygol1_percent'] = $stat_ygol_percent[0];
//     $fields['stat_ygol2_percent'] = $stat_ygol_percent[1];

//     // Фолы
//     $stat_fols_percent = getPercentAge($fields['stat_fols1'], $fields['stat_fols2']);
//     $fields['stat_fols1_percent'] = $stat_fols_percent[0];
//     $fields['stat_fols2_percent'] = $stat_fols_percent[1];
    
//     return $fields;
//   }

//   /**
//    * Получает процентное соотношение из двух цифр
//    * @param string|integer
//    * @param string|integer
//    * @return array
//    */
//   function getPercentAge($ageTeam1, $ageTeam2)
//   {
    
//     if ($ageTeam1 + $ageTeam2 > 0) {
//         // Вычисляем процентное соотношение
//         $team1_percentage = round( ( $ageTeam1 / ($ageTeam1 + $ageTeam2) * 100 ), 0 );
//         $team2_percentage = round( ($ageTeam2 / ($ageTeam1 + $ageTeam2) * 100), 0 );
    
//     } else {
//         $team1_percentage = 50;
//         $team2_percentage = 50;
//     }

//     return [$team1_percentage, $team2_percentage ];
//   }

  function getStaticMatch($matchId, $team1_id, $team2_id)
  {
    global $dbF;

    $sql = "SELECT 
        SUM(s.`vstvor`) AS total_vstvor,
        SUM(s.`mimo`) AS total_mimo,
        SUM(s.`pasplus`) AS total_pasplus,
        SUM(s.`pasminus`) AS total_pasminus,
        p.`team`
    FROM `v9ky_sostav` s
    LEFT JOIN `v9ky_player` p
        ON p.`id` = s.`player`
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
    $team1_data['total_udar'] = $team1_data['total_vstvor'] + $team1_data['total_mimo'];
    $team2_data['total_udar'] = $team2_data['total_vstvor'] + $team2_data['total_mimo'];

    $team1_data['vstvor_percentage_team'] = calculate_percentage($team1_data['total_vstvor'], $team2_data['total_vstvor']);
    $team2_data['vstvor_percentage_team'] = 100 - $team1_data['vstvor_percentage_team'];
    
    $team1_data['pasplus_percentage_team'] = calculate_percentage($team1_data['total_pasplus'], $team2_data['total_pasplus']);
    $team2_data['pasplus_percentage_team'] = 100 - $team1_data['pasplus_percentage_team'];
    
    $team1_data['udar_percentage_team'] = calculate_percentage($team1_data['total_udar'], $team2_data['total_udar']);
    $team2_data['udar_percentage_team'] = 100 - $team1_data['udar_percentage_team'];

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