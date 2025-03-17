<?php

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);


$turnir = getTurnir($tournament);  
$seasonName = getSeasonName($turnir);

// Получаем идентификатор команды из адресной строки
if (isset($params['id'])) {
  $teamId = $params['id'];
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




/** Для капитана  */
/** */
/** */

include_once HOME . '/telegramj.php' ;
include_once HOME . '/PHPLiveX.php' ;

$kep = 0;

class Validationc {

    public function sel_all($team, $error = "") {
        global $dbF;

        // Заголовки таблицы
        $test = "<tr>
                    <th colspan='5'>Дозаявки <font color='red'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</font></th>
                </tr>
                <tr>
                    <th>Тип</th>
                    <th>ПІБ</th>
                    <th>Рік народження</th>
                    <th>Дата подачі</th>
                    <th>Дія</th>
                </tr>";

        // Получаем заявки команды
        $recordzayavki = $dbF->query("SELECT * FROM `v9ky_capzayavki` WHERE `team`=:team AND `stan`=0 ORDER BY `date`", [
            ":team" => $team
        ])->findAll();

        foreach ($recordzayavki as $record) {
            $type = "";
            $recordname = [];

            // Определяем тип заявки и получаем ФИО + дату рождения
            switch ($record['type']) {
                case 0:
                    $type = "Відкликати";
                    $recordname = $dbF->query("SELECT CONCAT(`name1`, ' ', `name2`, ' ', `name3`) AS name, `age` AS birth 
                                                FROM `v9ky_man` WHERE id=(SELECT `man` FROM `v9ky_player` WHERE `id`=:player)", [
                        ":player" => $record['player']
                    ])->find();
                    break;
                case 1:
                    $type = "Заявити";
                    $recordname = $dbF->query("SELECT CONCAT(`name1`, ' ', `name2`, ' ', `name3`) AS name, `age` AS birth 
                                                FROM `v9ky_man` WHERE `id`=(SELECT `man` FROM `v9ky_player` WHERE `id`=:player)", [
                        ":player" => $record['player']
                    ])->find();
                    break;
                case 2:
                    $type = "Заявити нового";
                    $recordname = $dbF->query("SELECT CONCAT(`name1`, ' ', `name2`, ' ', `name3`) AS name, birth 
                                                FROM `v9ky_capzayavki` WHERE id=:id", [
                        ":id" => $record['id']
                    ])->find();
                    break;
            }

            // Преобразуем дату в часовой пояс Киев
            $dt = new DateTime($record['date'], new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Europe/Kiev'));
            $formatted_date = $dt->format('Y-m-d H:i:s');

            // Добавляем строку в таблицу
            $test .= '<tr>
                        <td class="jeka_yel" align="center">' . $type . '</td>
                        <td class="jeka_bord" align="center">' . htmlspecialchars(isset($recordname['name']) ? $recordname['name'] : '-', ENT_QUOTES, 'UTF-8') . '</td>
                        <td class="jeka_bord" align="center">' . (isset($recordname['birth']) ? $recordname['birth'] : '-') . '</td>
                        <td class="jeka_bord" align="center">' . $formatted_date . '</td>
                        <td class="jeka_bord" align="center">
                            <input type="button" style="color:red" value="ВИДАЛИТИ" onclick="delout(' . $record['id'] . ', ' . $team . ');">
                        </td>
                    </tr>';
        }

        return $test;
    }    

    public function _out($team, $player, $type) {
        global $dbF;

        // Подготовка массива данных
        $record = [
            ":type" => $type,
            ":team" => $team,
            ":player" => $player,
            ":date" => gmdate('Y-m-d H:i:s')
        ];

        // SQL-запрос с плейсхолдерами
        $sql = "INSERT INTO `v9ky_capzayavki` (`type`, `team`, `player`, `date`) 
                VALUES (:type, :team, :player, :date)";

        // Выполняем запрос через dbF
        $dbF->query($sql, $record);

        // Вызываем sel_all и возвращаем результат
        return $this->sel_all($team);
    }

    public function delout($id, $team) {
        global $dbF;

        // Удаляем запись с использованием подготовленного запроса
        $dbF->query("DELETE FROM `v9ky_capzayavki` WHERE id = :id", [":id" => $id]);

        // Вызываем метод sel_all для обновления данных
        return $this->sel_all($team);
    }

    public function newplay($team, $name1, $name2, $name3, $birth, $type) {
        global $dbF;

        $record['type'] = $type;
        $record['team'] = $team;
        $record['name1'] = addslashes(str_replace('"', "'", $name1));
        $record['name2'] = addslashes(str_replace('"', "'", $name2));
        $record['name3'] = addslashes(str_replace('"', "'", $name3));
        $record['birth'] = $birth;
        $record["date"] = date('Y-m-d H:i:s');

        $recordSet = $dbF->query("SELECT `id` FROM `v9ky_player` WHERE team=:team AND (man IN (SELECT id FROM v9ky_man WHERE name1=:name1 AND name2=:name2))", [
        ":team" => $team,
        ":name1" => addslashes($name1),
        ":name2" => addslashes($name2)        
        ])->findAll();

            // Объявляем переменную ошибки
        $error = "";

        if (empty($recordSet)) {
            // Если игрока нет в команде, добавляем его
            $sql = "INSERT INTO `v9ky_capzayavki` (`type`, `team`, `name1`, `name2`, `name3`, `birth`, `date`) 
                    VALUES (:type, :team, :name1, :name2, :name3, :birth, :date)";

            $dbF->query($sql, $record);
        } else {
            $error = "Такий гравець вже є в команді!";
        }     

        $test = $this->sel_all($team, $error);

        return $test;
    }

    public function newManager($team_id, $player_id){
        global $dbF;

        if($player_id == '0'){
            return;
        }

        // Объявляем переменную ошибки
        $error = "";

        $sql = "UPDATE `v9ky_team` SET `manager` = :player_id WHERE `id` = :team_id";
        if($dbF->query($sql, [":player_id" => $player_id, ":team_id" => $team_id])){
            $error = "Обновіть сторінку. менеджер перезаписаний";
        }else {
            $error = "Помилка! Менеджер не записаний";
        }

        $test = $this->sel_all($team_id, $error);

        return $test;

    }

    public function newTrainer($team_id, $player_id){
        global $dbF;

        if($player_id == '0'){
            return;
        }

        // Объявляем переменную ошибки
        $error = "";

        $sql = "UPDATE `v9ky_team` SET `trainer` = :player_id WHERE `id` = :team_id";
        if($dbF->query($sql, [":player_id" => $player_id, ":team_id" => $team_id])){
            $error = "Обновіть сторінку. тренер перезаписаний";
        }else {
            $error = "Помилка! Тренер не записаний";
        }

        $test = $this->sel_all($team_id, $error);

        return $test;

    }

    public function delstory($id, $team) {
        global $dbF;
        $tes = "";
        
        $exists = $dbF->query("SELECT COUNT(*) as count FROM `v9ky_cap_story` WHERE id=:id", [":id" => $id])->find();
        if ($exists['count'] > 0) {
            $dbF->query("DELETE FROM `v9ky_cap_story` WHERE id=:id", [":id" => $id]);
        }
        
        // Получаем обновленный список новостей
        $recordSet = $dbF->query("SELECT * FROM `v9ky_cap_story` WHERE `team`=:team ORDER BY `date` DESC", [":team" => $team])->findAll();

        // Проверяем, есть ли записи
        if (empty($recordSet)) {
            return "<p>Новин поки немає.</p>";
        }
        
        foreach ($recordSet as $item) {
        $tes .= $item['date'];
        $tes .= "<br><pre>";
        $tes .= $item['text'];
        $tes .= "</pre>";
        $tes .= "<input type='button' value='Видалити новину' onclick='delstory(" . $item['id'] . ", " . $team . ");' style='color:green'>";
        $tes .= "<br><br>";
        }

        return $tes;
    }

    public function newstory($team, $text) {
        global $dbF;
        $tes = "";
        $record['team'] = $team;
        $record['text'] = addslashes(str_replace('"', "'", $text));
        $record["date"] = date('Y-m-d H:i:s');

        // Проверяем, существует ли команда
        $recordSet = $dbF->query("SELECT id FROM v9ky_team WHERE id=:team", [":team" => $team])->findAll();

        if (!empty($recordSet) && isset($recordSet[0]['id'])) {
            $dbF->query("INSERT INTO `v9ky_cap_story` (`team`, `text`, `date`) VALUES (?, ?, ?)", 
                [$team, $record['text'], $record["date"]]);
        } else {
            return "Помилка!";
        }

        // Получаем все истории команды
        $recordSet = $dbF->query("SELECT * FROM `v9ky_cap_story` WHERE `team`=:team ORDER BY `date`", [":team" => $team])->findAll();

        foreach($recordSet as $item){
            $tes .= $item['date'];
            $tes .= "<br><pre>";
            $tes .= $item['text']; // Исправлено
            $tes .= "</pre>";
            $tes .= "<input type='button' value='Видалити новину' onclick='delstory(" . $item['id'] . ", " . $team . ");' style='color:green'>";
            $tes .= "<br><br>";
        }

        return $tes;
    }

    public function validation($team, $kod) {
        global $dbF;

        $recordSet = $dbF->query("SELECT * FROM v9ky_team WHERE id=:team", [":team" => $team])->findAll();

        if (!empty($recordSet) && isset($recordSet[0]['sms']) && ($recordSet[0]['sms'] > 99) && ($recordSet[0]['sms'] == $kod)) {
            $test = "<script>setTimeout(() => location.reload(), 500);</script>";

            $_SESSION['capitan'] = $recordSet[0]['tel1'];
            $popitok = intval(0);

            $dbF->query("UPDATE `v9ky_team` SET `popitok` = :popitok WHERE `id` = :team", [":popitok" => $popitok, ":team" => $team]);
        } else {
            $test = "";
        }

        return $test;
    }

    public function mantel($man, $tel) {
        global $dbF;

        $tel = 1 * $tel;
        if ($tel == 0) {
            $tel = "";
        }
        
        $dbF->query("UPDATE `v9ky_man` SET `tel` = :tel WHERE `id` = :man", [":tel" => $tel, ":man" => $man]);

        return $test;
    }

    public function amplua($man, $amplua) {
        global $dbF;
        
        $amplua = 1*$amplua;

        $dbF->query("UPDATE `v9ky_man` SET `amplua` = :amplua WHERE `id` = :man", [":amplua" => $amplua, ":man" => $man]);

        return $test;
    }

    public function sms($team) {
        global $dbF;
        $recordSet = $dbF->query("SELECT tel1, popitok, smssent FROM v9ky_team WHERE id=:team", [":team" => $team])->findAll();
        if ($recordSet[0]['tel1'] != "") {
            $length = 5;
            $characters = "123456789";
            $string = '';
            for ($p = 0; $p < $length; $p++) {
                $string .= $characters[mt_rand(0, strlen($characters))];
            }

            $url = 'https://gate.smsclub.mobi/token/?';
            // $url = 'http://v9ky.in.ua/capitan/http/?';
            $username = '380683609382';    // User ID (phone number)

            $from = 'VashZakaz';        // Sender ID (alpha-name)
            $to = $recordSet[0]['tel1'];
            $token = 'UIwUs6AvtZ-F5G_';
            $text = iconv('utf-8', 'windows-1251', 'Pass: ' . $string);
            $text = urlencode($text);       // Message text
            $url_result = $url . 'username=' . $username . '&from=' . urlencode($from) . '&to=' . $to . '&token=' . $token . '&text=' . $text;

            // TELEGRAM
            $recordcaptel = $dbF->query("SELECT count(*) AS kol, tg_id FROM v9ky_telegram_users WHERE tel=:tel AND tg_id > 0 AND active = 1", [":tel" => $recordSet[0]['tel1']])->findAll();
            if ($recordcaptel[0]['kol'] > 0) {
                define('TGKEY', '1754326106:AAF8LFoliaFRY9COd2J5bm0qaxNGumzoUAk');
                include_once HOME . '/telegramj.php';

                $body = file_get_contents('php://input');
                $arr = json_decode($body, true);

                $tg = new tg(TGKEY);

                $tg_id = $recordcaptel[0]['tg_id'];
                $rez_kb = array();

                $message_text = $arr['message']['text'];
                $tg->sendChatAction($tg_id);
                $sms_rev = 'Code ' . $string;

                $tg->send($tg_id, $sms_rev, $rez_kb);
                //exit('ok'); // Inform Telegram that everything is okay
            } else {
                // TELEGRAM
                if ($curl = curl_init()) {
                    curl_setopt($curl, CURLOPT_URL, $url_result);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    $out = curl_exec($curl);
                    echo $out;
                    curl_close($curl);
                }
            }

            $record['popitok'] = 0; // Reset the number of attempts
            $record['sms'] = $string;
            $record['smssent'] = gmdate('Y-m-d H:i:s');
            // $db->AutoExecute('v9ky_team', $record, 'UPDATE', 'id = ' . $team);
            $dbF->query('UPDATE v9ky_team SET `popitok` = :popitok, `sms` = :sms, `smssent` = :smssent WHERE `id` = :team', 
                [
                    ":popitok" => $record['popitok'],
                    ":sms" => $record['sms'],
                    ":smssent" => $record['smssent'],
                    ":team" => $team,
                ]);

            $test = "<script>var test = prompt('Пароль з SMS', ''); if (!isNaN(parseFloat(test)) && isFinite(test)) {validate(" . $team . ", test);}</script>";
            return $test;
        }
    }
}  

$ajax = new PHPLiveX();
$myClass = new Validationc();
$ajax->AjaxifyObjectMethods(array("myClass" => array("validation", "sms", "_out", "newplay", "delout", "newstory", "amplua", "delstory", "mantel", "newManager", "newTrainer")));
$ajax->Run(); // Must be called inside the 'html' or 'body' tags
function faceupload($man, $team, $tour){
    echo '<form class="upload-image" method="post" action="http://v9ky.in.ua/capitan/crop/" enctype="multipart/form-data">
        <label>Фото гравця</label>
      <input type="file" name="rule_picture" value="Загрузить новую" onchange="this.form.submit();">
      <input type="hidden" name="man" value="'.$man.'">
      <input type="hidden" name="team" value="'.$team.'">
      <input type="hidden" name="tour" value="'.$tour.'">
    </form>';
}


$recordSet = $dbF->query("SELECT * FROM v9ky_team WHERE id=:id", [":id" => $teamId])->find();
if ( ( isset($_SESSION['capitan']) && $_SESSION['capitan'] > 0 ) && ( $_SESSION['capitan'] == $recordSet['tel1'] ) ) {
    $kep = 1;
}  
/**
 * 
 */
/**  END Для капитана  */

$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем количество сыграных туров в турнире. Это нужно для отображения в таблице знака вопроса для несыграных матчей. 
$queryLastTur = $db->Execute(
  "SELECT tur as last_tur FROM `v9ky_match` WHERE `canseled`=1 and `turnir` = $turnir order by tur desc limit 1"
);

// Последний тур в турнире (в лиге).
$lastTur = intval($queryLastTur->fields[0]);

$lastTur = getLastTur($turnir);

$dataAllPlayers = getDataPlayers($allStaticPlayers);

// Отсортированный массив по рубрике Топ-Гравець
$topGravetc = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'topgravetc', $lastTur);

// dump_arr($topGravetc);

// Отсортированный массив по рубрике Топ-Голкипер
$topGolkiper = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golkiper', $lastTur);


// Отсортированный массив по рубрике Топ-Бомбардир
$topBombardi = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_goals', $lastTur);

// Отсортированный массив по рубрике Топ-Асистент
$topAsists = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_asists', $lastTur);

// Отсортированный массив по рубрике Топ-Захистник
$topZhusnuk = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'zahusnuk', $lastTur);

// Отсортированный массив по рубрике Топ-Дриблинг
$topDribling = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'dribling', $lastTur);

// Отсортированный массив по рубрике Топ-Удар
$topUdar = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'udar', $lastTur);

// Отсортированный массив по рубрике Топ-Пас
$topPas = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'pas', $lastTur);

// Берем первый элемент массива - топ-игрок
$topTopGravetc = reset($topGravetc);

// Берем первый элемент массива - топ-игрок
$topTopGolkiper = reset($topGolkiper);

// Берем первый элемент массива - топ-игрок
$topTopBombardi = reset($topBombardi);

// Берем первый элемент массива - топ-игрок
$topTopAsists = reset($topAsists);

// Берем первый элемент массива - топ-игрок
$topTopZhusnuk = reset($topZhusnuk);

// Берем первый элемент массива - топ-игрок
$topTopDribling = reset($topDribling);

// Берем первый элемент массива - топ-игрок
$topTopUdar = reset($topUdar);

// Берем первый элемент массива - топ-игрок
$topTopPas = reset($topPas);



// Получаем идентификатор команды из адресной строки
if (isset($params['id'])) {
    $teamId = $params['id'];  
    $id = $params['id'];  
   
} 





// Сумма статистики команды
$totalGoalsByTeam = getTotalStaticByTeam($topBombardi, $teamId);
$totalAsistByTeam = getTotalStaticByTeam($topAsists, $teamId);
// $totalMatchesByTeam = getTotalStaticByTeam($topAsists, $teamId, 'match_count');
$totalMatchesByTeam = getCountMatchesOfTurnir($allStaticPlayers, $teamId);
$totalYellowByTeam = getTotalStaticByTeam($topAsists, $teamId, 'yellow_cards');
$totalYellowRedByTeam = getTotalStaticByTeam($topAsists, $teamId, 'yellow_red_cards');
$totalRedByTeam = getTotalStaticByTeam($topAsists, $teamId, 'red_cards');
$totalBestPlayerByTeam = getTotalStaticByTeam($topAsists, $teamId, 'count_best_player_of_match');

// Лучшие показатели в команде 
$bestGravetc = getBestPlayer($topGravetc, $teamId);
$bestGolkiper = getBestPlayer($topGolkiper, $teamId);
$bestBombardi = getBestPlayer($topBombardi, $teamId);
$bestAssist = getBestPlayer($topAsists, $teamId);
$bestZhusnuk = getBestPlayer($topZhusnuk, $teamId);
$bestDribling = getBestPlayer($topDribling, $teamId);
$bestUdar = getBestPlayer($topUdar, $teamId);
$bestPas = getBestPlayer($topPas, $teamId);

$requestUri = $_SERVER['REQUEST_URI'];

// Разделение пути по "/"
$partsUri = explode('/', $requestUri);

$playersOfAllTurs = getPlayersOfAllTurs($allStaticPlayers, $lastTur);

$playerIds = getPlayerIds($dataAllPlayers, $teamId);
$countInTurOfTeam = getCountInTurOfTeam($playersOfAllTurs, $playerIds);


$teamData = getTeamData($teamId);
$players = getPlayersOfTeam($teamId, $kep);
$teamHeads = getTeamHeads($teamId);
$matches = getMatches($turnir, $teamId);

// Добавляем два элемента в массив с форматированными датами: match_day и match_time
$matches = getArrayWithFormattedDate($matches);

require_once CONTROLLERS . '/head.php';
require_once CONTROLLERS . '/menu.php';

require_once VIEWS . '/team_page.tpl.php';

require_once CONTROLLERS . '/footer.php';
