<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

$teamId = "5257";

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

$kep = 1;

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

?>

<section class="team-page">
 <div class="container">
    <div class="team-page__header">
      <div id="teamCard" class="team-page__team-card">
        <p class="team-page__title"><?= isset($teamData['name']) ? $teamData['name'] : 'Команда не знайдена' ?></p>
        <img class="team-page__logo" src="<?= $team_logo_path . $teamData['pict'] ?>" alt="Team logo">

        <div class="team-page__stars">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
        </div>

        <div class="team-page__skills">
          <div class="team-page__skills-container">
            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/star-icon.png" alt="skills-icon">
              <span><?= $bestGravetc ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/gloves-icon.png" alt="skills-icon">
              <span><?= $bestGolkiper ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/football-icon.png" alt="skills-icon">
              <span><?= $bestBombardi ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/boots-icon.png" alt="skills-icon">
              <span><?= $bestAssist ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/pitt-icon.png" alt="skills-icon">
              <span><?= $bestZhusnuk ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/player-icon.png" alt="skills-icon">
              <span><?= $bestDribling ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/rocket-ball-icon.png" alt="skills-icon">
              <span><?= $bestUdar ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/ball-icon.png" alt="skills-icon">
              <span><?= $bestPas ?></span>
            </div>
          </div>
          <div class="team-page__skills-container">
            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/football-icon.png" alt="Загальна кількість забитих м'ячів" title="Загальна кількість забитих м'ячів">
              <span><?= $totalGoalsByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/boots-icon.png" alt="Загальна кількість гольових пасів" title="Загальна кількість гольових пасів">
              <span><?= $totalAsistByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/yellow-shirt.png" alt="">
              <span><?= $countInTurOfTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/golden-star-icon.png" alt="Загальна кількість гравець матчу" title="Загальна кількість гкравець матчу">
              <span><?= $totalBestPlayerByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/green-field-icon.png" alt="">
              <span><?= $totalMatchesByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/yellow-card-icon.png" alt="">
              <span><?= $totalYellowByTeam; ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/yellow-red-icon.png" alt="">
              <span><?= $totalYellowRedByTeam; ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/red-card-icon.png" alt="">
              <span><?= $totalRedByTeam ?></span>
            </div>
          </div>
        </div>

        <a data-target="teamCard" class="team-page__share-button save-image">
          <img src="/css/components/team-page/assets/images/share-icon.svg" alt="share">
        </a>
      </div>

      <div class="team-page__photo">
      <?php  if(isset($teamData['photo']) && !empty($teamData['photo'])) :?>
          <img src="<?= $teamData['photo'] ?>" alt="Фото команди">
      <?php else :?>
          <img src="/css/components/team-page/assets/images/team-photo.jpg" alt="Фото команди">
      <?php endif ?>
      </div>
    </div>

    <div class="team-page__management">
      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads['manager']['player_photo']) ? $player_face_path . $teamHeads['manager']['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="manager-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/manager-icon.svg"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= $team_logo_path . $teamData['pict']  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads['manager']['player_lastname'] ) ? $teamHeads['manager']['player_lastname'] : 'менеджер' ?></span></p>
          <p><?=  isset($teamHeads['manager']) ? $teamHeads['manager']['player_firstname'] : '' ?></p>
        </div>
      </div>

      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads['trainer']['player_photo']) ? $player_face_path . $teamHeads['trainer']['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="trainer-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/coach-icon.png"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= $team_logo_path . $teamData['pict']  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads['trainer']['player_lastname'] ) ? $teamHeads['trainer']['player_lastname'] : 'тренер' ?></span></p>
          <p><?=  isset($teamHeads['trainer']) ? $teamHeads['trainer']['player_firstname'] : '' ?></p>
        </div>
      </div>
      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads['capitan']['player_photo']) ? $player_face_path . $teamHeads['capitan']['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="Фото капітана"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/cap-icon.svg"
          alt="Іконка капітана"
          onclick="sms();"
        ><span id="msg"></span>
        <img
          class="team-page__manager-team-logo"
          src="<?= $team_logo_path . $teamData['pict']  ?>"
          alt="Лого команди"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads['capitan']['player_lastname'] ) ? $teamHeads['capitan']['player_lastname'] : 'місце для капітана' ?></span></p>
          <p><?=  isset($teamHeads['capitan']) ? $teamHeads['capitan']['player_firstname'] : '' ?></p>
        </div>
      </div>
      
    </div>

    <table class="team-page__calendar-table">
      <thead>
        <tr>
          <th>ТУР</th>
          <th>ДАТА</th>
          <th>ЧАС</th>
          <th>поле</th>
          <th colspan="5">матч</th>
        </tr>
      </thead>
      <tbody> 
        <?php foreach($matches as $match) :?>
        <tr class="info_of_match">
            <td><?= $match['tur'] ?> тур</td>
            <td><?= $match['match_day'] ?></td>
            <td><?= $match['match_time'] ?></td>
            <td><?= $match['field_name']?></td>

            <td class="team__info team1" style="text-align: right">
                <span class="team_name truncate"><?=$match['team1'];?></span>
                <img style="margin-left: 10px" src="<?= $team_logo_path ?><?= $match['team1_photo'] ?>" alt="" width=30>
            </td>
            <?php if(empty($match['goals1'])):?>
              <td>VS</td>
            <?php else :?>
              <td data-match-id="<?= $match['id'] ?>" class="score-of-match"><?= $match['goals1'] ?> :  <?= $match['goals2'] ?></td>
            <?php endif ?>

            <td class="team__info team2" style="text-align: left">
                <img style="margin-right: 10px" src="<?=$team_logo_path?><?= $match['team2_photo'] ?>" alt="" width=30>
                <span class="team_name truncate"><?=$match['team2'];?></span>
            </td>

        </tr>
        <?php endforeach ?>
      </tbody>
    </table>

    <div id="matchStatsModal" class="stats_modal">
        <div class="stats_modal-content">
            <span class="close-btn">&times;</span>
            <div id="stats_modal-loading">Загрузка...</div>
            <div id="stats_modal-content"></div>
        </div>
    </div>


<?php if ($kep){ ?>

<div class="jeka_content">
  <p></p>
  <br>
  <br>
  <table class="capitan-table" id="msgzaya">
      <?php echo $myClass->sel_all($teamId); ?>			
  </table>
</div>

<p></p>
<br>
<br>
<table class="capitan-table">
  <tr>
      <th colspan='5'>Заявити нового гравця (українською мовою)</th>
  </tr>
  <tr>
    <th>Прізвище</th>
    <th>Ім'я</th>
    <th>По батькові</th>
    <th>Дата народження</th>
    <th>Дія</th>
  </tr>
  <tr>
    <td align="center"><input type='text' id='name1' value='' style="color:blue"></td>
    <td align="center"><input type='text' id='name2' size='20' value='' style="color:blue"></td>
    <td align="center"><input type='text' id='name3' size='20' value='' style="color:blue"></td>
    <td align="center"><input type='date' id='age' size='60' value='<?=date_format($age, 'Y-m-d')?>' style="color:blue"></td>
    <td align="center"><input type='button' value='Заявити нового' onclick='newplay(<?=$teamId?>);' style="color:green"></td>
  </tr>
</table>


<br>
<table class="capitan-table">
  <tr>
      <th colspan='2'><?= isset( $teamHeads['manager']['player_id']) ? 'Змінити' : 'Додати' ?> менеджера</th>
  </tr>
  <tr>
    <th>Менеджер</th>
    <th>Дія</th>
  </tr>
  <tr>
    <td align="center">
        <select name="players_list" id="manager_list">
            <option value="0">-- Призначити менеджера --</option>
            <?php foreach($players as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $teamHeads['manager']['player_id'] ==  $p['id'] ? 'selected' : '' ?> >
                    <?= $p['name1'] ?> <?= $p['name2'] ?>
                </option>
            <?php endforeach ?>
        </select>
    </td>
    <td align="center"><input type='button' value='Призначити менеджера' onclick='newManager(<?=$teamId?>);' style="color:green"></td>
  </tr>
</table>

<br>

<table class="capitan-table">
  <tr>
      <th colspan='2'><?= isset( $teamHeads['manager']['player_id']) ? 'Змінити' : 'Додати' ?> тренера</th>
  </tr>
  <tr>
    <th>Тренер</th>
    <th>Дія</th>
  </tr>
  <tr>
    <td align="center">
        <select name="player_list" id="trainer_list">
            <option value="0">-- Призначити тренера --</option>
            <?php foreach($players as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $teamHeads['trainer']['player_id'] ==  $p['id'] ? 'selected' : '' ?> >
                    <?= $p['name1'] ?> <?= $p['name2'] ?>
                </option>
            <?php endforeach ?>
        </select>
    </td>
    <td align="center"><input type='button' value='Призначити тренера' onclick='newTrainer(<?=$teamId?>);' style="color:green"></td>
  </tr>
</table>

<br>

<br>
<center><h2 style="color: #dfdfdf; background-color: #575757;">Напишіть нижче щотижневі новини або цікаві факти команди які можуть бути використані в наших публікаціях і відеоматеріалах</h2>
  <textarea type="text" id="cap_story" rows="3" cols="100">Текст новини</textarea><br>
  <input type='button' value='Зберегти новину команди' onclick='newstory(<?=$teamId?>);' style="color:green">

  <div id="msgstory">
    <?php
    $recordSet = $dbF->query("SELECT * FROM `v9ky_cap_story` WHERE `team` = :id ORDER BY `date`", [":id" => $teamId])->findAll();
    $i=0;
    foreach($recordSet as $item) {
        $i ++;
          echo $item[date];
          echo "<br><pre>";
          echo $item[text];
          echo "</pre>";
          echo "<input type='button' value='Видалити новину' onclick='delstory(".$item[id].", ".$teamId.");' style='color:green'>";
          echo "<br><br>";
    }
    ?>
  </div>
</center>
<br>
<?php } ?>
<div class="team-page__players">
    <?php $idx = 0 ?>
    <div style="display: none"><?php dump_arr_first($players) ?></div>
    <?php foreach ($players as $player): ?>

      <?php          
          // Получение индивидуальной статистики. Player Card.
          if (isset($allStaticPlayers)) { $indStaticPlayer = getIndStaticPlayer($allStaticPlayers, $player['id']); }  
      ?>
      <!-- Если тренер или менеджер, то не показываем карточку игрока -->
      <?php if ( $player['amplua'] != 4 && $player['amplua'] != 5 )  :?>      
        
      <div data-player-id="<?=$player['id']?>" id="playerCard<?= $idx ?>" class="card-player-full content-image">
          <?php 
            // Место в рейтинге
            $bestGravetc   = getBestPlayer($topGravetc, $player['id'], 'player_id');
            $bestGolkiper  = getBestPlayer($topGolkiper, $player['id'], 'player_id');
            $bestBombardi  = getBestPlayer($topBombardi, $player['id'], 'player_id');
            $bestAsists    = getBestPlayer($topAsists, $player['id'], 'player_id');
            $bestZhusnuk   = getBestPlayer($topZhusnuk, $player['id'], 'player_id');
            $bestDribling  = getBestPlayer($topDribling, $player['id'], 'player_id');
            $bestUdar      = getBestPlayer($topUdar, $player['id'], 'player_id');
            $bestPas       = getBestPlayer($topPas, $player['id'], 'player_id');

            // Определения значка в карточке игрока (верхний правый угол) - находим то, в чем игрок лучший.
            if($player['amplua'] != 1) {
              $arrayCPB = [$bestGravetc, $bestGolkiper, $bestBombardi, $bestAsists, $bestZhusnuk, $bestDribling, $bestUdar, $bestPas];
              $minValueCPB = min($arrayCPB);          
              $indexCPB = array_search($minValueCPB, $arrayCPB); 
            } else {
              $arrayCPB = [$bestGolkiper, $bestBombardi, $bestAsists, $bestZhusnuk, $bestDribling, $bestUdar, $bestPas];          
              $minValueCPB = min($arrayCPB);          
              $indexCPB = array_search($minValueCPB, $arrayCPB); 
              $indexCPB ++; 
            }

            // 0 - star-icon.png, 1 - gloves-icon.png, 2 - football-icon.png, 3 - boots-icon.svg, 4 - pitt-icon.svg, 5 - player-icon.svg
            // 6 - rocket-ball-icon.png, 7 - ball-icon.png
            $arrayCategoryPlayers = ['star-icon.png', 'gloves-icon.png', 'football-icon.png', 'boots-icon.png', 'pitt-icon.png', 'player-icon.png', 'rocket-ball-icon.png', 'ball-icon.png'];
            $categoryBestPlayers = $arrayCategoryPlayers[$indexCPB];
          ?>        
        
        <?php if( isset( $dataAllPlayers[$player['id']] ) ): ?>
            <div class="card-player-full__photo">
                <img src="<?=$player_face_path?><?= $dataAllPlayers[$player['id']]['player_photo'] ?>" alt="Фото гравця">

                <img width="24" height="24" class="card-player-full__best-icon" src="/css/components/card-player-full/assets/images/<?= $categoryBestPlayers ?>" alt="icon">

                <img src="<?=$team_logo_path?><?= $dataAllPlayers[$player['id']]['team_photo'] ?>" alt="Логотип команди">
                
                <?php if ($player['v9ky']):?>
                  <img src="/css/components/team-page/assets/images/player-v9ku.png" alt="В9КУ">
                  <?php elseif ($player['dubler']): ?>
                  <img src="/css/components/team-page/assets/images/dubler-icon.png" alt="Дублер">
                  <?php elseif ($player['vibuv']): ?>
                  <img class="vibuv" src="/css/components/team-page/assets/images/vibuv.png" alt="Вибув">
                <?php endif ?>
                                  
              </div>
                <!-- Информация для капитанов -->
                <div class="<?= $kep ? 'capitan-block' : '' ?>">
                        <div class="<?= $kep ? 'border p-2' : '' ?>">
                        <?php  if($kep) faceupload($player['man'], $teamId, $tournament); ?>
                        </div>                
                        <div class="<?= $kep ? 'mb-2' : '' ?>">
                        <?php if($kep){ ?>
                        <div class="mt-2 mb-2 border p-2">
                        <label>Тел.</label>
                        <input type='text' id='mantel<?=$player['id'];?>' value='<?=$player['tel'];?>' style='color:blue; width: 100px' placeholder='38068XXXXXXX'>
                        </div>
                        <div class="mt-2 mb-2 border p-2 bg-green">
                        <input type='button' value='Зберегти' onclick="mantel(<?=$player['id'];?>, <?=$player['man'];?>);">
                        </div>
                        <div class="mb-2 border p-2">
                        <lable>Амплуа</label>
                        <select class='form-control' id="amplua_<?=$player['id'];?>" size=1 onchange="amplua(<?=$player['id'];?>, <?=$player['man'];?>);">
                                <option value='0' <?php if($player['amplua']==0) echo "selected";?>>-</option>
                                <option value='2' <?php if($player['amplua']==2) echo "selected";?>>Гравець</option>
                                <option value='3' <?php if($player['amplua']==3) echo "selected";?>>Воротар</option>
                        </select>
                        </div> 
                        <div class="mb-2 border p-2">
                        <?php 
                        if($kep)
                        {
                                if ($player['active'] == 1) {
                                echo '<div class="mt-2 mb-2">';
                                echo"<font color='green'>Заявлений</font>";
                                echo'<input type="button" value="Відкликати" onclick="out('.$teamId.', '.$player["id"].', 0);">';
                                echo '</div>';
                                }else {
                                echo '<div class="mt-2 mb-2">';
                                echo'<font color="red">Відкликаний</font>';
                                echo'<input type="button" value="Заявити" onclick="out('.$teamId.', '.$player["id"].', 1);">';
                                echo '</div>';
                                }
                        } 
                        ?>
                        
                        </div>
                        <?php } ?>
                        </div>                
                </div>
                <!-- КОНЕЦ Информация для капитанов -->

              <div class="card-player-full__name">
                <p><span><?= $dataAllPlayers[$player['id']]['last_name'] ?> </span></p>
                <p><?= $dataAllPlayers[$player['id']]['first_name'] ?></p>                
              </div>

        <?php else: ?>
            <?php $dataPlayer = getPlayerData($player['id']) ?>
            <div class="card-player-full__photo">
              <img src="<?=$player_face_path?><?= empty($dataPlayer['player_photo']) ? 'avatar.jpg' : $dataPlayer['player_photo'] ?>" alt="Фото гравця">
                

                <img width="24" height="24" class="card-player-full__best-icon" src="/css/components/card-player-full/assets/images/<?= $categoryBestPlayers ?>" alt="icon">

                <img src="<?=$team_logo_path?><?= $dataPlayer['team_logo'] ?>" alt="Логотип команди">
                
                <?php if ($player['v9ky']):?>
                  <img src="/css/components/team-page/assets/images/player-v9ku.png" alt="В9КУ">
                  <?php elseif ($player['dubler']): ?>
                  <img src="/css/components/team-page/assets/images/dubler-icon.png" alt="Дублер">
                  <?php elseif ($player['vibuv']): ?>
                  <img class="vibuv" src="/css/components/team-page/assets/images/vibuv.png" alt="Вибув">
                <?php endif ?>

              </div>

                <!-- Информация для капитанов -->
                <div class="<?= $kep ? 'capitan-block' : '' ?>">
                        <div class="<?= $kep ? 'border p-2' : '' ?>">
                        <?php  if($kep) faceupload($player['man'], $teamId, $tournament); ?>
                        </div>                
                        <div class="<?= $kep ? 'mb-2' : '' ?>">
                        <?php if($kep){ ?>
                        <div class="mt-2 mb-2 border p-2">
                        <label>Тел.</label>
                        <input type='text' id='mantel<?=$player['id'];?>' value='<?=$player['tel'];?>' style='color:blue; width: 100px' placeholder='38068XXXXXXX'>
                        </div>
                        <div class="mt-2 mb-2 border p-2 bg-green">
                        <input type='button' value='Зберегти' onclick="mantel(<?=$player['id'];?>, <?=$player['man'];?>);">
                        </div>
                        <div class="mb-2 border p-2">
                        <lable>Амплуа</label>
                        <select class='form-control' id="amplua_<?=$player['id'];?>" size=1 onchange="amplua(<?=$player['id'];?>, <?=$player['man'];?>);">
                                <option value='0' <?php if($player['amplua']==0) echo "selected";?>>-</option>
                                <option value='2' <?php if($player['amplua']==2) echo "selected";?>>Гравець</option>
                                <option value='3' <?php if($player['amplua']==3) echo "selected";?>>Воротар</option>
                        </select>
                        </div> 
                        <div class="mb-2 border p-2">
                        <?php 
                        if($kep)
                        {
                                if ($player['active'] == 1) {
                                echo '<div class="mt-2 mb-2">';
                                echo"<font color='green'>Заявлений</font>";
                                echo'<input type="button" value="Відкликати" onclick="out('.$teamId.', '.$player["id"].', 0);">';
                                echo '</div>';
                                }else {
                                echo '<div class="mt-2 mb-2">';
                                echo'<font color="red">Відкликаний</font>';
                                echo'<input type="button" value="Заявити" onclick="out('.$teamId.', '.$player["id"].', 1);">';
                                echo '</div>';
                                }
                        } 
                        ?>
                        
                        </div>
                        <?php } ?>
                        </div>                
                </div>
                <!-- КОНЕЦ Информация для капитанов -->

              <div class="card-player-full__name">
                <p><span><?= $dataPlayer['player_lastname'] ?> </span></p>
                <p><?= $dataPlayer['player_firstname'] ?></p>                  
              </div>
        <?php endif ?>
        


          <!--  иконки первого ряда: мяч, звездочка, футболка ... -->
        <ul class="card-player-full__top-statistic">
          
            <li>
              <img src="/css/components/card-player-full/assets/images/football-icon.png" alt="Кількість забитих м'ячів">
              <p><?= $indStaticPlayer['count_goals'] ?></p>
            </li>
            
            <li>
              <img src="/css/components/card-player-full/assets/images/golden-star-icon.png" alt="Кількість отриманих номінацій гравець матчу">
              <p><?= $indStaticPlayer['count_best_player_of_match'] ?></p>
              
            </li>   
            
            <li>
              <img src="/css/components/card-player-full/assets/images/yellow-shirt.png" alt="Кількість разів учасник збірна туру">
              <p><?= getCountInTur($playersOfAllTurs, $player['id']) ?> </p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/boots-icon.png" alt="">
              <p><?= $indStaticPlayer['count_asists'] ?></p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/green-field-icon.png" alt="">
              <p><?= $indStaticPlayer['count_matches'] ?></p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/yellow-card-icon.png" alt="">
              <p><?= $indStaticPlayer['yellow_cards'] ?></p>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/yellow-red-icon.png" alt="">
              <p><?= $indStaticPlayer['yellow_red_cards'] ?></p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/red-card-icon.png" alt="">
              <p><?= $indStaticPlayer['red_cards'] ?></p>
            </li>

        </ul>

        <ul class="card-player-full__middle-statistic">
          <li>
            <p>Точність удару</p>
            <p><?= $indStaticPlayer['accuracy_of_kicking'] ?>%</p>
          </li>
          <li>
          <li>
            <p>Точність пасів</p>
            <p><?= $indStaticPlayer['accuracy_of_passing'] ?>%</p>
          </li>

          <li>
            <p>Вдалі обводки</p>
            <p><?= $indStaticPlayer['accuracy_of_dribbles'] ?>%</p>
          </li>

          <!-- <li>
            <p>Загострень за матч</p>
            <p><?= $indStaticPlayer['count_of_aggravations'] ?></p>
          </li> -->

          <li>
            <p>Відборів за матч</p>
            <p><?= $indStaticPlayer['accuracy_of_tackles'] ?></p>
          </li>
        </ul>
        
        <?php if( isset( $dataAllPlayers[$player['id']] ) ): ?>       
          <h4>Місце в рейтингу ліги</h4>

          <ul class="card-player-full__skills">
            <!-- Если не вратарь, то не показываем -->
            <?php if($dataAllPlayers[$player['id']]['amplua'] != 1): ?>  
              <li>
                <img src="/css/components/card-player-full/assets/images/star-icon.png" alt="skills-icon" title="Топ-Гравець">
                <span><?= $bestGravetc ?></span>
              </li>
            <?php endif ?>
            
            <!-- Если вратарь, то показываем -->
            <?php if($dataAllPlayers[$player['id']]['amplua'] == 1): ?>
              <li>
                <img src="/css/components/card-player-full/assets/images/gloves-icon.png" alt="skills-icon" title="Топ-Голкіпер">
                <span><?= $bestGolkiper ?></span>
              </li>
            <?php endif ?>            
            
            <li>
              <img src="/css/components/card-player-full/assets/images/football-icon.png" alt="skills-icon" title="Топ-Бомбардир">              
              <span><?= $bestBombardi ?></span>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/boots-icon.png" alt="skills-icon" title="Топ-Асист">
              <span><?= $bestAsists ?></span>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/pitt-icon.png" alt="skills-icon" title="Топ-Захист">
              <span><?= $bestZhusnuk ?></span>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/player-icon.png" alt="skills-icon" title="Топ-Дріблінг">
              <span><?= $bestDribling ?></span>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/rocket-ball-icon.png" alt="skills-icon" title="Топ-Удар">
              <span><?= $bestUdar ?></span>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/ball-icon.png" alt="skills-icon" title="Топ-Пас">
              <span><?= $bestPas ?></span>
            </li>
          </ul>
          <?php else : ?>
          <h4>Місце в рейтингу ліги</h4>
          <ul class="card-player-full__skills"> 
              <li>
                  <img src="/css/components/card-player-full/assets/images/star-icon.png" alt="skills-icon" title="Топ-Гравець">
                  <span>-</span>
                </li>
                <li>
                    <img src="/css/components/card-player-full/assets/images/football-icon.png" alt="skills-icon" title="Топ-Бомбардир">              
                    <span>-</span>
                  </li>

                  <li>
                    <img src="/css/components/card-player-full/assets/images/boots-icon.png" alt="skills-icon" title="Топ-Асист">
                    <span>-</span>
                  </li>

                  <li>
                    <img src="/css/components/card-player-full/assets/images/pitt-icon.png" alt="skills-icon" title="Топ-Захист">
                    <span>-</span>
                  </li>

                  <li>
                    <img src="/css/components/card-player-full/assets/images/player-icon.png" alt="skills-icon" title="Топ-Дріблінг">
                    <span>-</span>
                  </li>

                  <li>
                    <img src="/css/components/card-player-full/assets/images/rocket-ball-icon.png" alt="skills-icon" title="Топ-Удар">
                    <span>-</span>
                  </li>

                  <li>
                    <img src="/css/components/card-player-full/assets/images/ball-icon.png" alt="skills-icon" title="Топ-Пас">
                    <span>-</span>
                  </li>
            </ul>
        <?php endif ?>
        <div class="card-player-full__footer">
          <img src="/css/components/card-player-full/assets/images/v9ku-logo-on-white-back.png" alt="">
          
          <div>
            
            <p><?= $seasonName ?></p>
            
          </div>
          <div id="dump" style="display: none"><?php dump($player) ?></div>
          
          <!-- Если игрок голкипер -->
          <?php if($player['amplua'] == 1 || $player['amplua'] == 3): ?>
            <img src="/css/components/team-page/assets/images/goalkeeper-icon.svg" alt="" title="Голкіпер">
            <?php else : ?>
              <img src="/css/components/team-page/assets/images/player-boots-icon.svg" alt="" title="Гравець">
              <?php endif ?>
              
            </div>
            <div class="card-player-full__share">
              <button data-target="playerCard<?= $idx ?>" data-card-player class="card-player-full__button-share save-image">
                <img 
                src="/css/components/card-player-full/assets/images/button-share-icon.svg" 
                alt="button-share-icon"
                >
              </button>
            </div>
            <div class="card-player-full__footer">
        </div>

      </div><!-- card-player-full__photo -->
      <?php endif ?>
      <?php $idx++ ?>
    <?php endforeach?>

    </div><!-- team-page__players -->

    <div class="team-page__reference">
      <ul class="team-page__reference--top">
        <li>
          <img src="/css/components/team-page/assets/images/star-icon.png" alt="">
          <p>ККД гравця</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/gloves-icon.png" alt="">
          <p>ККД голкіпера</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/football-icon.png" alt="">
          <p>Бомбардири</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/boots-icon.png" alt="">
          <p>Асисти</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/pitt-icon.png" alt="">
          <p>Захист</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/player-icon.png" alt="">
          <p>Дріблінг</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/ball-icon.png" alt="">
          <p>Пас</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/rocket-ball-icon.png" alt="">
          <p>Удар</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/manager-icon.png" alt="">
          <p>Президент команди</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/coach-icon.png" alt="">
          <p>Тренер</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/goalkeeper-icon.svg" alt="" title="Голкіпер">
          <p>Голкіпер</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/player-boots-icon.svg" alt="" title="Гравець">
          <p>Гравець в полі</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/cap-icon.svg" alt="">
          <p>Капітан</p>
        </li>

      </ul>

      <ul class="team-page__reference--bottom">
        <li>
          <div>
            <img src="/css/components/team-page/assets/images/football-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість голів за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/boots-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість гольових пасів за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/dubler-icon.png" alt="">
            <span></span>
          </div>
          <p>Дублери</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/player-v9ku.png" alt="">
            <span></span>
          </div>
          <p>Гравець «В9КУ» (орендований гравець)</p>
        </li>

        <li>
          <div>
            <img class="team-page__card-icon" src="/css/components/team-page/assets/images/red-card-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість червоних карток за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/green-field-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість проведених матчів за сезон</p>
        </li>

        <li>
          <div>
            <img class="team-page__card-icon" src="/css/components/team-page/assets/images/yellow-card-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість жовтих карток за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість отриманих номінацій «Гравець Матчу»</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/yellow-shirt.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість потраплянь в збірну туру за сезон</p>
        </li>
      </ul>
    </div> <!-- team-page__reference -->
  </div><!-- container --> 
</section><!-- team-page --> 


<script type="text/javascript">

  function sms(){
    myClass.sms(<?=$teamId?>,  {
        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;
        }
    });
  }
  function validate(id, code){
    id = id * 1;
    code = code * 1;
    myClass.validation(id, code,  {
        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;
        }
    });
  }
  function out(team, player, type){
    myClass._out(team, player, type,  {
        "onFinish": function(response){
            var msgzaya = document.getElementById("msgzaya");
            msgzaya.innerHTML = response;
        }
    });
  }
  function mantel(player, man){
    val1 = document.getElementById("mantel" + player).value;
    val2 = parseInt(val1);
    if (val2<99999999999) alert("Повний формат телефону будь-ласка!"); else
    myClass.mantel(man, val2,  {
        "onFinish": function(response){
            var msgzaya = document.getElementById("msgzaya");
            msgzaya.innerHTML = response;
        }
    });
  }

  function amplua(player, man){
    val1 = document.getElementById("amplua_" + player).value;
    myClass.amplua(man, val1,  {
        "onFinish": function(response){
            var msgzaya = document.getElementById("msgzaya");
            msgzaya.innerHTML = response;
        }
    });
  }

  function delout(id, team){

    myClass.delout(id, team,  {

        "onFinish": function(response){
            var msgzaya = document.getElementById("msgzaya");
            msgzaya.innerHTML = response;
        }
    });
  }
  function delstory(id, team){

    myClass.delstory(id, team,  {

        "onFinish": function(response){
            var msgstory = document.getElementById("msgstory");
            msgstory.innerHTML = response;
        }
    });
  }
  function newplay(team){
    val1 = document.getElementById("name1").value;
    val2 = document.getElementById("name2").value;
    val3 = document.getElementById("name3").value;
    val4 = document.getElementById("age").value;
    if ((val1=='')||(val2=='')||(val3=='')||(val4=='')) alert("Заповніть всі поля"); else
    myClass.newplay(team, val1, val2, val3, val4, 2, {

        "onFinish": function(response){
            var msgzaya = document.getElementById("msgzaya");
            msgzaya.innerHTML = response;

        }
    });
  }


  function newManager(team){
    const selectElement = document.getElementById("manager_list");  
    const player = selectElement.value; 

    myClass.newManager(team, player, {

        "onFinish": function(response){
            document.location.reload();
        }
    });
  }

  function newTrainer(team){
    const selectElement = document.getElementById("trainer_list");  
    const player = selectElement.value; 

    myClass.newTrainer(team, player, {

        "onFinish": function(response){
            document.location.reload();
        }
    });
  }

  function newstory(team){
    val1 = document.getElementById("cap_story").value;

    if (val1=='Текст новини') alert("Заповніть поле тексту новин команди"); else
      myClass.newstory(team, val1, {

          "onFinish": function(response){
              var msgstory = document.getElementById("msgstory");
              msgstory.innerHTML = response;

          }
      });
  }
</script>


<?php 
    require_once CONTROLLERS . '/footer.php';
?>



