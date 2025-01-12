<?php
if (isset($params['match'])) {
    $baza = '/match/' . $params['match'];
}
session_start();
// $_SESSION['capitan'] = "1";

include_once "turnir_head.php";


$record_pagestat["ip"] = $_SERVER['REMOTE_ADDR'];
// $record_pagestat["ip_forwarded"] = $_SERVER['HTTP_X_FORWARDED_FOR'];
$record_pagestat["agent"] = $_SERVER['HTTP_USER_AGENT'];
$record_pagestat["page"] = "team";
$db->AutoExecute('page_stat', $record_pagestat, 'INSERT');

// ajax
require_once('PHPLiveX.php');

$kep = 0;

class Validationc {

  public function sel_all($team, $error) {
      global $db;

      $test = "<tr>
                  <th colspan='5'>Дозаявки <font color='red'>" . $error . "</font></th>
                </tr>
                <tr>
                  <th>Тип</th>
                  <th>ПІБ</th>
                  <th>Рік народження</th>
                  <th>Дата подачі</th>
                  <th></th>
                </tr>";

      $recordzayavki = $db->Execute("SELECT * FROM v9ky_capzayavki WHERE team='" . $team . "' AND stan=0 ORDER BY date");
      while (!$recordzayavki->EOF) {

          switch ($recordzayavki->fields['type']) {
              case 0:
                  $type = "Відкликати";
                  $recordname = $db->Execute("SELECT CONCAT(name1, ' ', name2, ' ', name3) AS name, age AS birth 
                                              FROM v9ky_man WHERE id=(SELECT man FROM v9ky_player WHERE id=" . $recordzayavki->fields['player'] . ")");
                  break;
              case 1:
                  $type = "Заявити";
                  $recordname = $db->Execute("SELECT CONCAT(name1, ' ', name2, ' ', name3) AS name, age AS birth 
                                              FROM v9ky_man WHERE id=(SELECT man FROM v9ky_player WHERE id=" . $recordzayavki->fields['player'] . ")");
                  break;
              case 2:
                  $type = "Заявити нового";
                  $recordname = $db->Execute("SELECT CONCAT(name1, ' ', name2, ' ', name3) AS name, birth AS birth 
                                              FROM v9ky_capzayavki WHERE id=" . $recordzayavki->fields['id']);
                  break;
          }

          $dt = new DateTime($recordzayavki->fields['date'], new DateTimeZone('UTC'));
          $dt->setTimezone(new DateTimeZone('Europe/Kiev'));
          $dt->format('Y-m-d H:i:s');

          $test .= '<tr>
                      <td class="jeka_yel" align="center">' . $type . '</td>
                      <td class="jeka_bord" align="center">' . stripslashes($recordname->fields['name']) . '</td>
                      <td class="jeka_bord" align="center">' . $recordname->fields['birth'] . '</td>
                      <td class="jeka_bord" align="center">' . $dt->format('Y-m-d H:i:s') . ' ' . $recordzayavki->fields['date'] . '</td>
                      <td class="jeka_bord" align="center"><input type="button" style="color:red" value="ВИДАЛИТИ" onclick="delout(' . $recordzayavki->fields['id'] . ', ' . $team . ');"></td>
                  </tr>';
          $recordzayavki->MoveNext();
      }
      return $test;
  }

  public function _out($team, $player, $type) {
      global $db;

      $record['type'] = $type;
      $record['team'] = $team;
      $record['player'] = $player;
      $record["date"] = gmdate('Y-m-d H:i:s');
      $db->AutoExecute('v9ky_capzayavki', $record, 'INSERT');
      $test = $this->sel_all($team);

      return $test;
  }

  public function delout($id, $team) {
      global $db;

      $db->Execute("DELETE FROM v9ky_capzayavki WHERE id='" . $id . "'");
      $test = $this->sel_all($team);

      return $test;
  }

  public function newplay($team, $name1, $name2, $name3, $birth, $type) {
      global $db;

      $record['type'] = $type;
      $record['team'] = $team;
      $record['name1'] = addslashes(str_replace('"', "'", $name1));
      $record['name2'] = addslashes(str_replace('"', "'", $name2));
      $record['name3'] = addslashes(str_replace('"', "'", $name3));
      $record['birth'] = $birth;
      $record["date"] = date('Y-m-d H:i:s');

      $recordSet = $db->Execute("SELECT id FROM v9ky_player WHERE team='" . $team . "' AND (man IN (SELECT id FROM v9ky_man WHERE name1='" . addslashes($name1) . "' AND name2='" . addslashes($name2) . "'))");
      if (!$recordSet->fields['id']) {
          $db->AutoExecute('v9ky_capzayavki', $record, 'INSERT');
      } else {
          $error = "Такий гравець вже є в команді!";
      }

      $test = $this->sel_all($team, $error);

      return $test;
  }

  public function delstory($id, $team) {
      global $db;
      $tes = "";
      $db->Execute("DELETE FROM v9ky_cap_story WHERE id='" . $id . "'");
      $recordSet = $db->Execute("SELECT * FROM v9ky_cap_story WHERE team='" . $team . "' ORDER BY date");

      while (!$recordSet->EOF) {
          $tes .= $recordSet->fields['date'];
          $tes .= "<br><pre>";
          $tes .= $recordSet->fields['text'];
          $tes .= "</pre>";
          $tes .= "<input type='button' value='Видалити новину' onclick='delstory(" . $recordSet->fields['id'] . ", " . $team . ");' style='color:green'>";
          $tes .= "<br><br>";
          $recordSet->MoveNext();
      }

      return $tes;
  }

  public function newstory($team, $text) {
      global $db;
      $tes = "";
      $record['team'] = $team;
      $record['text'] = addslashes(str_replace('"', "'", $text));
      $record["date"] = date('Y-m-d H:i:s');

      $recordSet = $db->Execute("SELECT id FROM v9ky_team WHERE id='" . $team . "'");
      if ($recordSet->fields['id']) {
          $db->AutoExecute('v9ky_cap_story', $record, 'INSERT');
      } else {
          $error = "Помилка!";
      }

      $recordSet = $db->Execute("SELECT * FROM v9ky_cap_story WHERE team='" . $team . "' ORDER BY date");
      while (!$recordSet->EOF) {
          $tes .= $recordSet->fields['date'];
          $tes .= "<br><pre>";
          $tes .= $recordSet->fields['text'];
          $tes .= "</pre>";
          $tes .= "<input type='button' value='Видалити новину' onclick='delstory(" . $recordSet->fields['id'] . ", " . $team . ");' style='color:green'>";
          $tes .= "<br><br>";
          $recordSet->MoveNext();
      }

      return $tes;
  }

  public function validation($team, $kod) {
      global $db;

      $recordSet = $db->Execute("SELECT * FROM v9ky_team WHERE id='" . $team . "'");

      if (($recordSet->fields['sms'] > 99) && ($recordSet->fields['sms'] == $kod)) {
          $test = "<script>document.location.reload(true);</script>";

          $_SESSION['capitan'] = $recordSet->fields['tel1'];
          $record['popitok'] = 0;

          $db->AutoExecute('v9ky_team', $record, 'UPDATE', 'id = ' . $team . '');
      } else {
          $test = "";
      }

      return $test;
  }

  public function mantel($man, $tel) {
      global $db;

      $record['tel'] = 1 * $tel;
      if ($record['tel'] == 0) {
          $record['tel'] = "";
      }

      $db->AutoExecute('v9ky_man', $record, 'UPDATE', 'id = ' . $man . '');

      return $test;
  }

  public function amplua($man, $amplua) {
      global $db;
    
      $record[amplua] = 1*$amplua;

      $db->AutoExecute('v9ky_man',$record,'UPDATE', 'id = '.$man.'');

      return $test;
  }

  public function sms($team) {
    global $db;
    $recordSet = $db->Execute("SELECT tel1, popitok, smssent FROM v9ky_team WHERE id='" . $team . "'");
    if ($recordSet->fields['tel1'] != "") {
        $length = 5;
        $characters = "123456789";
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }

        $url = 'https://gate.smsclub.mobi/token/?';
        // $url = 'http://v9ky.in.ua/capitan/http/?';
        $username = '380683609382';    // User ID (phone number)

        $from = 'VashZakaz';        // Sender ID (alpha-name)
        $to = $recordSet->fields['tel1'];
        $token = 'UIwUs6AvtZ-F5G_';
        $text = iconv('utf-8', 'windows-1251', 'Pass: ' . $string);
        $text = urlencode($text);       // Message text
        $url_result = $url . 'username=' . $username . '&from=' . urlencode($from) . '&to=' . $to . '&token=' . $token . '&text=' . $text;

        // TELEGRAM
        $recordcaptel = $db->Execute("SELECT count(*) AS kol, tg_id FROM v9ky_telegram_users WHERE tel=" . $recordSet->fields['tel1'] . " AND tg_id > 0 AND active = 1");
        if ($recordcaptel->fields['kol'] > 0) {
            define('TGKEY', '1754326106:AAF8LFoliaFRY9COd2J5bm0qaxNGumzoUAk');
            include_once('telegramj.php');

            $body = file_get_contents('php://input');
            $arr = json_decode($body, true);

            $tg = new tg(TGKEY);

            $tg_id = $recordcaptel->fields['tg_id'];
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
        $db->AutoExecute('v9ky_team', $record, 'UPDATE', 'id = ' . $team);

        $test = "<script>var test = prompt('Пароль з SMS', ''); if (!isNaN(parseFloat(test)) && isFinite(test)) {validate(" . $team . ", test);}</script>";
        return $test;
    }
  }
}  

$ajax = new PHPLiveX();

$myClass = new Validationc();
$ajax->AjaxifyObjectMethods(array("myClass" => array("validation", "sms", "_out", "newplay", "delout", "newstory", "amplua", "delstory", "mantel")));
$ajax->Run(); // Must be called inside the 'html' or 'body' tags
//ajax

// -- Freedman --
if(isset($_GET['foo'])){

// TODO: freedman  Получаем данные для карточек игроков - "Место в лиге" и топ-таблиц
        
// Подключаю свой файл-помощник
// include_once('freedman/helpers.php');
  
// Получаем данные из БД. Статискика всех игроков учавствуюих в текущей лиге. Статистика вся, кроме забитых голов
$queryStaticPlayers = $db->Execute( 
  "SELECT p.team,m.tur,s.player,s.matc,s.seyv, s.seyvmin, s.vstvor, s.mimo, s.pasplus, s.pasminus, s.otbor, s.otbormin, s.obvodkaplus, s.obvodkaminus, s.golevoypas, s.zagostrennia, s.vkid, s.vkidmin, s.blok, s.vtrata
  FROM `v9ky_sostav` s 
  LEFT JOIN `v9ky_match` m ON m.id = s.matc 
  LEFT JOIN `v9ky_player` p ON p.id = s.player
  WHERE `player` IN 
    (SELECT `id` FROM `v9ky_player` WHERE `team` IN 
    (SELECT `id` FROM `v9ky_team` WHERE `turnir` = $turnir ))"
);

$queryBestPlayerOfMatch = $db->Execute(
  "SELECT `best_player`, id, tur FROM `v9ky_match` WHERE `turnir`=523 and `best_player`>0 ORDER by tur"
);
          
  
// Получаем данные из БД. Статистика забитых голов игроков.
$queryGoals = $db->Execute( 
  "SELECT `matc`, `player` FROM `v9ky_gol` WHERE `player` IN  
  (SELECT `id` FROM `v9ky_player` WHERE `team` IN 
  (SELECT `id` FROM `v9ky_team` WHERE `turnir` = '" . $turnir . "' ))"
);

// Получаем данные из БД. Статистика желтых карточек игроков.
$queryAsist = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS count_asists FROM v9ky_asist WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем данные из БД. Статистика желтых карточек игроков.
$queryYellowCards = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS yellow_cards FROM v9ky_yellow WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем данные из БД. Статистика желтых карточек игроков.
$queryYellowRedCards = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS yellow_red_cards FROM v9ky_yellow_red WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем данные из БД. Статистика красных карточек игроков.
$queryRedCards = $db->Execute( 
  "SELECT player, matc, COUNT(*) AS red_cards FROM v9ky_red WHERE player IN (
      SELECT id FROM v9ky_player WHERE team IN (
              SELECT id FROM v9ky_team WHERE turnir = '" . $turnir . "'
              )
      )
  GROUP BY player;"
);

// Получаем количество сыграных туров в турнире. Это нужно для отображения в таблице знака вопроса для несыграных матчей. 
$queryLastTur = $db->Execute(
  "SELECT tur as last_tur FROM `v9ky_match` WHERE `canseled`=1 and `turnir` = $turnir order by tur desc limit 1"
);

// Последний тур в турнире (в лиге).
$lastTur = intval($queryLastTur->fields[0]);

// Массив для лучшего игрока матча (иконка ведочка)
$nominationPlayerOfMatch = [];

// Заполняем массив лучшего игрока матча
while(!$queryBestPlayerOfMatch->EOF){

  foreach($queryBestPlayerOfMatch as $value){

    $player = $value['best_player'];
    $match = $value['id'];    
    
    // Заполняем массив. Проверки не нужно. Так как в одном матче лучший игрок может быть только один.
    $nominationPlayerOfMatch[$player][$match]['count_best_player_of_match'] = 1;
    
  }
  $queryBestPlayerOfMatch->MoveNext();
}

// Массив для cтастистики игроков учавствуюих в текущей лиге
$allStaticPlayers = array(); 

// Заполняем массив статистикой игроков, кроме статистики забитых голов
while(!$queryStaticPlayers->EOF){

  foreach ( $queryStaticPlayers->fields as $key => $value ) {
    if (is_string($key)){
      $allStaticPlayers[$queryStaticPlayers->fields['player']][$queryStaticPlayers->fields['matc']][$key] = $value;
    }
  }

  $queryStaticPlayers->MoveNext();
}


// Массив для статистики забитых голов в каждом матче отдельно
$playerMatchesGoals = array();

// Заполняем массив $queryGoals статистикой забитых голов. Массив [$player_id => $count_goals]
while(!$queryGoals->EOF){

  // $queryGoals->fields - массив содержит данные забитых голов. Одна запись = одному голу.
  // Создаем ассоциативный массив $playerGoals - [$player_id => (int) $count_goals]
  // Создаем ассоциативный массив $playerMatchesGoals - [ [$player_id] => [$match_id => (int) $count_goals] ]
  foreach ( $queryGoals as $key => $value ) {                
                    
    // Начало записи массива $playerMatchesGoals
    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];

    // Если такого player еще нет в итоговом массиве, создаем запись
    if (!isset($playerMatchesGoals[$player])) {
      $playerMatchesGoals[$player] = array();
    }

    // Если такой матч уже существует для игрока, увеличиваем его счетчик
    if (isset($playerMatchesGoals[$player][$match])) {
      $playerMatchesGoals[$player][$match]['count_goals'] ++;
    } else {
        // Если матч не существует, добавляем его с начальным значением 1
        $playerMatchesGoals[$player][$match]['count_goals'] = 1;
    }

  }

  $queryGoals->MoveNext();
}

$countAsist = [];

while(!$queryAsist->EOF){

  foreach ( $queryAsist as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];
    
    $countAsist[$player][$match]['count_asists'] = $value['count_asists'];
    
  }
  
  $queryAsist->MoveNext();
}

//массив для желтых карточек
$yellowCards = [];

// Цикл из запроса по желтым карточкам
while(!$queryYellowCards->EOF){


  foreach ( $queryYellowCards as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];
    
    $yellowCards[$player][$match]['yellow_cards'] = $value['yellow_cards'];
    
  }
  $queryYellowCards->MoveNext();
}

//массив для желто-красных карточек
$yellowRedCards = [];

// Цикл из запроса по желтым карточкам
while(!$queryYellowRedCards->EOF){


  foreach ( $queryYellowRedCards as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];
    
    $yellowRedCards[$player][$match]['yellow_red_cards'] = $value['yellow_red_cards'];
    
  }
  $queryYellowRedCards->MoveNext();
}


//массив для красных карточек
$redCards = [];

// Цикл из запроса по карточкам карточкам
while(!$queryRedCards->EOF){


  foreach ( $queryYellowCards as $value ){

    // Получаем player и matc
    $player = $value['player'];
    $match = $value['matc'];

    if(!isset($yellowCards[$player][$match])) {
      
      $yellowCards[$player][$match]['red_cards'] = $value['red_cards'];

    }
    
    
  }
  $queryRedCards->MoveNext();
}


// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  забитых мячей
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $playerMatchesGoals);

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  красных карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $countAsist, 'count_asists');

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  желтых карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $yellowCards, 'yellow_cards');

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  желтых карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $yellowRedCards, 'yellow_red_cards');

// Получаем общий массив. Добавляем в массив с основной статистикой, статистику  красных карточек
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $queryRedCards, 'red_cards');

// Получаем общий массив. Добавляем в основной массив статистику лучший игрок матча.
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $nominationPlayerOfMatch, 'count_best_player_of_match');

// Массив только идентификаторов игроков
$allPlayersId = array_keys($allStaticPlayers);

// Делаем строку для апроса в БД.
$strAllPlayersId = implode(", ", $allPlayersId);


// Получаем данные по id - ФИО, фото,  и т.д.
$queryAllPlayersData = $db->Execute(
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
      mf.pict AS player_photo,
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
      p.id IN ($strAllPlayersId)  
");  

// Данные всех игроков типа Имя, Фамилия, Фото и т.д
$dataAllPlayers = [];  

// Меняем структуру массива - для удобства работы с ним
while(!$queryAllPlayersData->EOF){
  foreach ($queryAllPlayersData as $key => $value) {
    $playerId = $value['player_id'];
    if(!isset($dataAllPlayers[$playerId])) {      
        $dataAllPlayers[$playerId] = $value;             
    }
  }
  $queryAllPlayersData->MoveNext();
}

// Отсортированный массив по рубрике Топ-Гравець
// $trainer = getTopPlayers( $allStaticPlayers, $dataAllPlayers, 'trainer', $lastTur );

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



}// -- END Freedman --

?>

<div class="content">

<?php  
if (isset($params['id'])) {
    $id = $params['id'];
    $recordSet = $db->Execute("SELECT * FROM v9ky_team WHERE id='" . $id . "'");

    if (($_SESSION['capitan'] > 0) && ($_SESSION['capitan'] == $recordSet->fields['tel1'])) {
        $kep = 1;
    }

    $teamname = stripslashes($recordSet->fields['name']);
    
    if ($recordSet->fields['site'] != "") {
        $site = "<a href='" . $recordSet->fields['site'] . "'>" . $recordSet->fields['site'] . "</a>";
    }

    $teampict = $recordSet->fields['pict'];
?>


<!-- //////////// Note 29.11.2024 ///////////// -->
<?php if(!isset($_GET['foo'])) : ?>
<!-- ///////////////////////// -->

    <h2 class="team-titles"><?= $teamname ?></h2>
    <div class="top-images">
        <img src="<?= $team_logo_path . $teampict ?>" alt="">
        
        <?php if ($recordSet->fields['photo'] != "") { ?>
            <img src="<?= $recordSet->fields['photo'] ?>" alt="">
        <?php } ?>
    </div>

    <h2 class="team-titles"></h2>
    <p class="team-history"><?= $site ?></p>


  
  <div class="jeka_content">
      <table class="jeka_calendar">
          <tr>
              <th>Тур</th>
              <th>Дата</th>
              <th>Час</th>
              <th>Поле</th>
              <th colspan="5">Матч</th>
          </tr>

          <?php
          $recordmatch = $db->Execute("SELECT id, canseled, date, tur, field, 
              (SELECT name FROM v9ky_fields WHERE id=a.field) AS field_ru, 
              gols1, gols2, 
              (SELECT ru FROM v9ky_turnir WHERE id=a.turnir) AS turnir_ru, 
              (SELECT name FROM v9ky_team WHERE id=a.team1) AS team_1, 
              (SELECT pict FROM v9ky_team WHERE id=a.team1) AS pict_1, 
              (SELECT name FROM v9ky_team WHERE id=a.team2) AS team_2, 
              (SELECT pict FROM v9ky_team WHERE id=a.team2) AS pict_2 
              FROM v9ky_match a 
              WHERE turnir='".$turnir."' 
              AND (team1='".$id."' OR team2='".$id."') 
              ORDER BY date DESC");

          while (!$recordmatch->EOF) {
              if ($recordmatch->fields['canseled'] == 1) {
                  if ($goly1 != "-") {
                      $score = $goly1 . ":" . $goly2;
                  } else {
                      $score = "VS";
                  }
                  $gols = $recordmatch->fields['gols1'] . " : " . $recordmatch->fields['gols2'];
              } else {
                  $gols = " VS ";
              }
          ?>

          <tr>
                <td class="jeka_yel" align="center"><?= $recordmatch->fields['tur'] ?> тур</td>
                <td class="jeka_bord" align="center"><?= rus_date("d F", strtotime($recordmatch->fields['date'])) ?></td>
                <td class="jeka_bord" align="center"><?= rus_date("H:i", strtotime($recordmatch->fields['date'])) ?></td>
                <td class="jeka_bord" align="center" border-right-color="#050505"><?= $recordmatch->fields['field_ru'] ?></td>
                <td align="right"><?= $recordmatch->fields['team_1'] ?></td>
                <td><img src="<?= $team_logo_path . $recordmatch->fields['pict_1'] ?>" alt="" width="30"></td>
                <td><a href="<?= $site_url ?>/<?= $tournament ?>/teams_match_stat/match/<?= $recordmatch->fields['id'] ?>" style="color:#FFFFFF;"><?= $gols ?></a></td>
                <td><img src="<?= $team_logo_path . $recordmatch->fields['pict_2'] ?>" alt="" width="30"></td>
                <td><?= $recordmatch->fields['team_2'] ?></td>
          </tr>

          <?php
              $recordmatch->MoveNext();
          }
          ?>

      </table>
  </div>



<!-- ///////////////////////// -->
<?php endif ?>
<!-- ///////////////////////// -->




<!-- //////////// СТАРТ Основная Верстка  ///////////// -->
<?php if(isset($_GET['foo'])) : ?>
<!-- ///////////////////////// -->

<!-- Note 28.11.2024 -->
<section class="team-page">
  <div class="container">

  <?php 
  ?>

    <div class="team-page__header">
      <div id="teamCard" class="team-page__team-card">
        <p class="team-page__title"><?= $teamname ?></p>

        <img class="team-page__logo" src="<?= $team_logo_path . $teampict ?>" alt="Team logo">

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
        <img src="/css/components/team-page/assets/images/team-photo.jpg" alt="">
      </div>
    </div>

    <?php 
      if ($kep) $active=""; else $active="and active='1'";
      $recordSet = $db->Execute("select * from v9ky_player a where team='".$id."' ".$active." ORDER BY active desc, vibuv, id=(select capitan from v9ky_team where id='".$id."') desc, (select count(*) as kol from v9ky_gol where player=a.id and team='".$id."') desc, (select count(*) from v9ky_sostav where player=a.id) desc, (select name1 from v9ky_man where id=a.man)");
      $recordSet1 = $db->Execute("select capitan, tel1 from v9ky_team where id='".$id."'");          
      $recordface = $db->Execute("select * from v9ky_man_face where man='".$recordSet->fields[man]."' ORDER BY data desc LIMIT 1");          
      if ($recordface->fields[pict]) $face = $recordface->fields[pict]; else $face = "avatar1.jpg";

      // Находим Менеджера, Тренера, Капитана
      $queryTeamHeads = $db->Execute(
        "SELECT p.id as player_id,
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
            WHERE t.id = $id
            GROUP BY p.id
            ORDER BY p.amplua DESC
        ");   

        $teamHeads = [];
        $iconHeads = [ 0 => 'manager-icon.svg', 4 => 'coach-icon.svg', 5 => 'cap-icon.svg'];

        
      while(!$queryTeamHeads->EOF){
        $amplua = $queryTeamHeads->fields['amplua'];
        $teamHeads[$amplua] = $queryTeamHeads->fields;  
        $teamHeads[$amplua]['icon'] = $iconHeads[$amplua];
        
        $queryTeamHeads->MoveNext();
      }


    ?>

    <div class="team-page__management">
      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads[5]['player_photo']) ? $player_face_path . $teamHeads[5]['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="manager-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/manager-icon.svg"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= isset($teamHeads[5]) ? $team_logo_path . $teamHeads[5]['team_photo'] : $player_face_path . 'avatar.jpg'  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads[5]['player_lastname'] ) ? $teamHeads[5]['player_lastname'] : 'менеджер' ?></span></p>
          <p><?=  isset($teamHeads[5]) ? $teamHeads[5]['player_firstname'] .' '.  $teamHeads[5]['player_middlename'] : '' ?></p>
        </div>
      </div>

      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads[4]['player_photo']) ? $player_face_path . $teamHeads[4]['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="trainer-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/coach-icon.png"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= isset($teamHeads[4]) ? $team_logo_path . $teamHeads[4]['team_photo'] : $player_face_path . 'avatar.jpg'  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads[4]['player_lastname'] ) ? $teamHeads[4]['player_lastname'] : 'тренер' ?></span></p>
          <p><?=  isset($teamHeads[4]) ? $teamHeads[4]['player_firstname'] .' '.  $teamHeads[4]['player_middlename'] : '' ?></p>
        </div>
      </div>
      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads[0]['player_photo']) ? $player_face_path . $teamHeads[0]['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="capitan-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/cap-icon.svg"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= isset($teamHeads[0]) ? $team_logo_path . $teamHeads[0]['team_photo'] : $player_face_path . 'avatar.jpg'  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads[0]['player_lastname'] ) ? $teamHeads[0]['player_lastname'] : 'місце для капітана' ?></span></p>
          <p><?=  isset($teamHeads[0]) ? $teamHeads[0]['player_firstname'] .' '.  $teamHeads[0]['player_middlename'] : '' ?></p>
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
          <th colspan="3">матч</th>
        </tr>
      </thead>
      <tbody> 
        <?php
        $recordmatch1 = $db->Execute("select id, canseled, date, tur, field, (select name from v9ky_fields where id=a.field) as field_ru, gols1, gols2, (select ru from v9ky_turnir where id=a.turnir) as turnir_ru, (select name from v9ky_team where id=a.team1) as team_1, (select pict from v9ky_team where id=a.team1) as pict_1, (select name from v9ky_team where id=a.team2) as team_2, (select pict from v9ky_team where id=a.team2) as pict_2 from v9ky_match a where turnir='".$turnir."' and (team1='".$id."' or team2='".$id."') order by date desc");
        while (!$recordmatch1->EOF) {
          if ($recordmatch1->fields[canseled]==1) {
            if ($goly1<>"-") $score = $goly1.":".$goly2; else $score = "VS";
            $gols = $recordmatch1->fields[gols1]." : ".$recordmatch1->fields[gols2];
          } else $gols = " VS ";
        ?>        
          <tr>
            <td><?=$recordmatch1->fields[tur]?> тур</td>
            <td><?=rus_date("d F", strtotime($recordmatch1->fields[date]))?></td>
            <td><?=rus_date("H:i", strtotime($recordmatch1->fields[date]))?></td>
            <td><?=$recordmatch1->fields[field_ru]?></td>
            <td  style="text-align: right"><span class="team_name"><?=$recordmatch1->fields[team_1];?></span><img style="margin-left: 10px" src="<?=$team_logo_path?><?=$recordmatch1->fields[pict_1]?>" alt="" width=30></td>
            <td><a href="<?=$site_url?>/<?=$tournament?>/teams_match_stat/match/<?=$recordmatch1->fields[id];?>"><?=$gols?></a></td>
            <td style="text-align: left"><img style="margin-right: 10px" src="<?=$team_logo_path?><?=$recordmatch1->fields[pict_2]?>" alt="" width=30><span class="team_name"><?=$recordmatch1->fields[team_2]?></span></td>
          </tr>
        <?php $recordmatch1->MoveNext(); }?>
      </tbody>
    </table>

    <?php if ($kep){ ?>

      <div class="jeka_content">
        <p></p>
        <br>
        <br>
        <table class="jeka_calendar" id="msgzaya">
            <?php echo $myClass -> sel_all($id); ?>			
        </table>
      </div>

      <p></p>
      <br>
      <br>


      <table class="jeka_calendar">
        <tr>
            <th colspan='5'>Заявити нового гравця (українською мовою)</th>
        </tr>
        <tr>
          <th>Прізвище</th>
          <th>Ім'я</th>
          <th>По батькові</th>
          <th>Дата народження</th>
          <th></th>
        </tr>
        <tr>
          <td align="center"><input type='text' id='name1' value='' style="color:blue"></td>
          <td align="center"><input type='text' id='name2' size='20' value='' style="color:blue"></td>
          <td align="center"><input type='text' id='name3' size='20' value='' style="color:blue"></td>

         
          <td align="center"><input type='date' id='age' size='60' value='<?=date_format($age, 'Y-m-d')?>' style="color:blue"></td>
          <td align="center"><input type='button' value='Заявити нового' onclick='newplay(<?=$id?>);' style="color:green"></td>
        </tr>
      </table>

      <br>
      <center><h2 style="color: #043e64; background-color: #d8dd1b;">Напишіть нижче щотижневі новини або цікаві факти команди які можуть бути використані в наших публікаціях і відеоматеріалах</h2>
        <textarea type="text" id="cap_story" rows="3" cols="100">Текст новини</textarea><br>
        <input type='button' value='Зберегти новину команди' onclick='newstory(<?=$id?>);' style="color:green">

        <div id="msgstory">
          <?
          $recordSet = $db->Execute("select * from v9ky_cap_story where team='".$id."' ORDER BY date");
          $i=0;
          while (!$recordSet->EOF) {
            $i ++;
            echo $recordSet->fields[date];
            echo "<br><pre>";
            echo $recordSet->fields[text];
            echo "</pre>";
            echo "<input type='button' value='Видалити новину' onclick='delstory(".$recordSet->fields[id].", ".$id.");' style='color:green'>";
            echo "<br><br>";
            $recordSet->MoveNext();
          }
          ?>
        </div>
      </center>
    <?php } ?>
    <br>

    <div class="team-page__players">
      <?php
        $idx = 0;
        while (!$recordSet->EOF) {
          $recordsostav = $db->Execute("select count(*) as kol from v9ky_sostav where player='".$recordSet->fields['id']."' ");
          $recordgols = $db->Execute("select count(*) as kol from v9ky_gol where player='".$recordSet->fields['id']."' and team='".$id."' ");
          $recordagols = $db->Execute("select count(*) as kol from v9ky_gol where player='".$recordSet->fields['id']."' and team<>'".$id."' ");
          $recordyel = $db->Execute("select count(*) as kol from v9ky_yellow where player='".$recordSet->fields['id']."' ");
          $recordasist = $db->Execute("select count(*) as kol from v9ky_asist where player='".$recordSet->fields['id']."' ");
          $recordred = $db->Execute("select count(*) as kol from v9ky_red where player='".$recordSet->fields['id']."' ");
          $recordface = $db->Execute("select * from v9ky_man_face where man='".$recordSet->fields['man']."' ORDER BY data desc LIMIT 1");
          $recordname = $db->Execute("select * from v9ky_man where id='".$recordSet->fields['man']."' LIMIT 1");
          
          if ($recordface->fields['pict']) $face = $recordface->fields['pict']; else $face = "avatar1.jpg";
          
          // Получение индивидуальной статистики. Player Card.
          if (isset($allStaticPlayers)) { $indStaticPlayer = getIndStaticPlayer($allStaticPlayers, $recordSet->fields['id']); }
            
      ?>

    <!-- Если тренер или менеджер, то не показываем карточку игрока -->
    <?php if ($recordSet->fields['amplua'] != 4 && $recordSet->fields['amplua'] != 5):?>

      <div id="playerCard<?= $idx ?>" class="card-player-full content-image">

          <?php 
            // Место в рейтинге
            $bestGravetc   = getBestPlayer($topGravetc, $recordSet->fields['id'], 'player_id');
            $bestGolkiper  = getBestPlayer($topGolkiper, $recordSet->fields['id'], 'player_id');
            $bestBombardi  = getBestPlayer($topBombardi, $recordSet->fields['id'], 'player_id');
            $bestAsists    = getBestPlayer($topAsists, $recordSet->fields['id'], 'player_id');
            $bestZhusnuk   = getBestPlayer($topZhusnuk, $recordSet->fields['id'], 'player_id');
            $bestDribling  = getBestPlayer($topDribling, $recordSet->fields['id'], 'player_id');
            $bestUdar      = getBestPlayer($topUdar, $recordSet->fields['id'], 'player_id');
            $bestPas       = getBestPlayer($topPas, $recordSet->fields['id'], 'player_id');

            // Определения значка в карточке игрока (верхний правый угол) - находим то, в чем игрок лучший.
            if($recordSet->fields['amplua'] != 1) {
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
        
        <div class="card-player-full__photo">
          <img src="<?=$player_face_path?><?=$face?>" alt="photo_player">
          <?php if($kep) faceupload($recordSet2->fields[man], $id, $tournament); ?>

          <img class="card-player-full__best-icon" src="/css/components/card-player-full/assets/images/<?= $categoryBestPlayers ?>" alt="icon">

          <img src="<?=$team_logo_path?><?=$teampict?>" alt="Team Logo">
          
          <?php if ($recordSet->fields['v9ky']):?>
            <img src="/css/components/team-page/assets/images/player-v9ku.png" alt="В9КУ">
            <?php elseif ($recordSet->fields['dubler']): ?>
            <img src="/css/components/team-page/assets/images/dubler-icon.png" alt="Дублер">
            <?php elseif ($recordSet->fields['vibuv']): ?>
            <img class="vibuv" src="/css/components/team-page/assets/images/vibuv.png" alt="Вибув">
          <?php endif ?>
            
          
        </div>

        <div class="card-player-full__name">
          <p><span><?=stripslashes($recordname->fields['name1'])?> </span></p>
          <p><?=stripslashes($recordname->fields['name2'])?></p>
          
          <?php if($kep){ ?>
            <span>Тел.
              <input type='text' id='mantel<?=$recordSet2->fields['id'];?>' value='<?=$recordname->fields[tel];?>' style='color:blue' placeholder='38068XXXXXXX'>
              <input type='button' value='Зберегти' onclick='mantel(<?=$recordSet2->fields[id];?>, <?=$recordSet2->fields[man];?>);'>
            </span>
            <span>Амплуа
              <select class='form-control' id='amplua_<?=$recordSet2->fields[id];?>' size=1 onchange='amplua(<?=$recordSet2->fields[id];?>, <?=$recordSet2->fields[man];?>);'>
                <option value='0' <?php if($recordname->fields[amplua]==0) echo "selected";?>>-</option>
                <option value='1' <?php if($recordname->fields[amplua]==1) echo "selected";?>>Нападник</option>
                <option value='2' <?php if($recordname->fields[amplua]==2) echo "selected";?>>Захисник</option>
                <option value='3' <?php if($recordname->fields[amplua]==3) echo "selected";?>>Воротар</option>
              </select>
            </span> 
          <?php } ?>
        </div>

          <!--  иконки первого ряда: мяч, звездочка, футболка ... -->
        <ul class="card-player-full__top-statistic">
          
            <li>
              <img src="/css/components/card-player-full/assets/images/football-icon.png" alt="Кількість забитих м'ячів">
              <p><?= $indStaticPlayer['count_goals'] ?></p>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/golden-star-icon.png" alt="Кількість гольових пасів">
              <p><?= $indStaticPlayer['count_best_player_of_match'] ?></p>
            </li>   
            
            <li>
              <img src="/css/components/card-player-full/assets/images/yellow-shirt.png" alt="Кількість раів учасник збірна туру">
              <p><?= getCountInTur($playersOfAllTurs, $recordSet->fields['id']) ?> </p>
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

          <li>
            <p>Загострень за матч</p>
            <p><?= $indStaticPlayer['count_of_aggravations'] ?></p>
          </li>

          <li>
            <p>Відборів за матч</p>
            <p><?= $indStaticPlayer['accuracy_of_tackles'] ?></p>
          </li>
        </ul>

        <h4>Місце в рейтингу ліги</h4>


        <ul class="card-player-full__skills">
          <!-- Если не вратарь, то не показываем -->
          <?php if($recordSet->fields['amplua'] != 1): ?>  
            <li>
              <img src="/css/components/card-player-full/assets/images/star-icon.png" alt="skills-icon" title="Топ-Гравець">
              <span><?= $bestGravetc ?></span>
            </li>
          <?php endif ?>
          
          <!-- Если вратарь, то показываем -->
          <?php if($recordSet->fields['amplua'] == 1): ?>
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

        <div class="card-player-full__footer">
          <img src="/css/components/card-player-full/assets/images/v9ku-logo-on-white-back.png" alt="">
          
          <div>
            
            <p><?=$season_name->fields['season']?></p>
            
          </div>
          
          <!-- Если игрок голкипер -->
          <?php if($recordSet->fields['amplua'] == 1): ?>
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
              <?php
            if ($kep){
              if ($recordSet->fields['active'] == 1) {
                echo"<font color='green'>Заявлений</font>";
                echo"<input type='button' value='Відкликати' onclick='out(".$id.", ".$recordSet->fields['id'].", 0);'>";
              }else {
                  echo"<font color='red'>Відкликаний</font>";echo"<input type='button' value='Заявити' onclick='out(".$id.", ".$recordSet->fields['id'].", 1);'>";
              }
            } 
          ?>
        </div>

      </div><!-- card-player-full__photo -->

    <?php endif ?>


        <?
          $idx++;
          $recordSet->MoveNext();
        }
        $time = microtime(true) - $start;
        error_log($_SERVER['REQUEST_URI']." ->Gen in ".$time."sec");
      ?>
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
<!-- End Note 28.11.2024 -->

<!-- ///////////////////////// -->
<?php endif ?>
<!-- ///////////////////////// -->






<?php if ($kep){ ?>

  <div class="jeka_content">
    <p></p>
    <br>
    <br>
    <table class="jeka_calendar" id="msgzaya">
        <?php echo $myClass -> sel_all($id); ?>			
    </table>
  </div>

  <p></p>
  <br>
  <br>


  <table class="jeka_calendar">
    <tr>
        <th colspan='5'>Заявити нового гравця (українською мовою)</th>
    </tr>
    <tr>
      <th>Прізвище</th>
      <th>Ім'я</th>
      <th>По батькові</th>
      <th>Дата народження</th>
      <th></th>
    </tr>
    <tr>
      <td align="center"><input type='text' id='name1' value='' style="color:blue"></td>
      <td align="center"><input type='text' id='name2' size='20' value='' style="color:blue"></td>
      <td align="center"><input type='text' id='name3' size='20' value='' style="color:blue"></td>

      <?php //$age =date_create("1900-01-01"); ?>
      
      <td align="center"><input type='date' id='age' size='60' value='<?=date_format($age, 'Y-m-d')?>' style="color:blue"></td>
      <td align="center"><input type='button' value='Заявити нового' onclick='newplay(<?=$id?>);' style="color:green"></td>
    </tr>
  </table>

  <br>
  <center><h2 style="color: #043e64; background-color: #d8dd1b;">Напишіть нижче щотижневі новини або цікаві факти команди які можуть бути використані в наших публікаціях і відеоматеріалах</h2>
    <textarea type="text" id="cap_story" rows="3" cols="100">Текст новини</textarea><br>
    <input type='button' value='Зберегти новину команди' onclick='newstory(<?=$id?>);' style="color:green">

    <div id="msgstory">
      <?php
      $recordSet = $db->Execute("select * from v9ky_cap_story where team='".$id."' ORDER BY date");
      $i=0;
      while (!$recordSet->EOF) {
        $i ++;
        echo $recordSet->fields[date];
        echo "<br><pre>";
        echo $recordSet->fields[text];
        echo "</pre>";
        echo "<input type='button' value='Видалити новину' onclick='delstory(".$recordSet->fields[id].", ".$id.");' style='color:green'>";
        echo "<br><br>";
        $recordSet->MoveNext();
      }
      ?>
    </div>
  </center>
<?php } ?>
<br>

    <!-- //////////// Note 29.11.2024 ///////////// -->
    <?php if(!isset($_GET['foo'])) { ?>
    <!-- ///////////////////////// -->

      <table class="sostav-tab">  
        <?php
          if ($kep) $active=""; else $active="and active='1'";
          $recordSet = $db->Execute("select * from v9ky_player a where team='".$id."' ".$active." ORDER BY active desc, id=(select capitan from v9ky_team where id='".$id."') desc, (select count(*) as kol from v9ky_gol where player=a.id and team='".$id."') desc, (select count(*) from v9ky_sostav where player=a.id) desc, (select name1 from v9ky_man where id=a.man)");
          $recordSet1 = $db->Execute("select capitan, tel1 from v9ky_team where id='".$id."'");



          while (!$recordSet->EOF) {
            $recordsostav = $db->Execute("select count(*) as kol from v9ky_sostav where player='".$recordSet->fields[id]."' ");
            $recordgols = $db->Execute("select count(*) as kol from v9ky_gol where player='".$recordSet->fields[id]."' and team='".$id."' ");
            $recordagols = $db->Execute("select count(*) as kol from v9ky_gol where player='".$recordSet->fields[id]."' and team<>'".$id."' ");
            $recordyel = $db->Execute("select count(*) as kol from v9ky_yellow where player='".$recordSet->fields[id]."' ");
            $recordasist = $db->Execute("select count(*) as kol from v9ky_asist where player='".$recordSet->fields[id]."' ");
            $recordred = $db->Execute("select count(*) as kol from v9ky_red where player='".$recordSet->fields[id]."' ");
            $recordface = $db->Execute("select * from v9ky_man_face where man='".$recordSet->fields[man]."' ORDER BY data desc LIMIT 1");
            $recordname = $db->Execute("select * from v9ky_man where id='".$recordSet->fields[man]."' LIMIT 1");

            $managerAndTrainer = $db->Execute("SELECT manager, trainer FROM v9ky_team WHERE id = $id ");

            if ($recordface->fields[pict]) $face = $recordface->fields[pict]; else $face = "avatar1.jpg";

        ?>
  
      <tr>
        <td>
          <a href="<?=$site_url?>/<?=$tournament?>/player/info/<?=$recordSet->fields[man]?>">
                  <img src="<?=$player_face_path?><?=$face?>">
          </a>
          <?php  if($kep) faceupload($recordSet->fields[man], $id, $tournament); ?>
        </td>
        <td>
            <a href="<?=$site_url?>/<?=$tournament?>/player/info/<?=$recordSet->fields[man]?>">
              <span><?=stripslashes($recordname->fields[name1])?> <?=stripslashes($recordname->fields[name2])?> <?=stripslashes($recordname->fields[name3])?></span>
              <span><?=$recordname->fields[age]?></span>
            </a>
            <?php if($kep){ ?>
              <span>Тел.
                <input type='text' id='mantel<?=$recordSet->fields[id];?>' value='<?=$recordname->fields[tel];?>' style='color:blue' placeholder='38068XXXXXXX'>
                <input type='button' value='Зберегти' onclick='mantel(<?=$recordSet->fields[id];?>, <?=$recordSet->fields[man];?>);'>
              </span>
              <span>Амплуа
                <select class='form-control' id='amplua_<?=$recordSet->fields[id];?>' size=1 onchange='amplua(<?=$recordSet->fields[id];?>, <?=$recordSet->fields[man];?>);'>
                  <option value='0' <?php if($recordname->fields[amplua]==0) echo "selected";?>>-</option>
                  <option value='1' <?php if($recordname->fields[amplua]==1) echo "selected";?>>Нападник</option>
                  <option value='2' <?php if($recordname->fields[amplua]==2) echo "selected";?>>Захисник</option>
                  <option value='3' <?php if($recordname->fields[amplua]==3) echo "selected";?>>Воротар</option>
                </select>
              </span> 
            <?php } ?>
        </td>
        <td>
          <?php
            if ($recordSet->fields[id] == $recordSet1->fields[capitan]) {
          ?>
            <script type="text/javascript">

              function sms(){

                  myClass.sms(<?=$id?>,  {

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
            <img src='/img/capitan.jpg' onclick='sms();' /><span id="msg"></span>
          <?php } ?>
          <?php  if ($recordSet->fields[amplua] == 1) {
                  echo"<img src='/img/perchatki.png' />";
                }
            if ($recordSet->fields[v9ky] == 1) {
                  echo"V9KY";
                }
            if ($recordSet->fields[dubler] == 1) {
                  echo"ДублерA";
                }
            if ($recordSet->fields[dubler] == 2) {
                  echo"ДублерB";
                }
            if ($recordSet->fields[vibuv] == 1) {
                  echo"<font style = 'font-size:36px; color:red;'>ВИБУВ</font>";
                }
            ?>
        </td>

        <td class="imgNumber">
          <?php if ($recordsostav->fields[kol] > 0){?>
              <img src="/img/icon-field.png" alt="">
              <span><?=$recordsostav->fields[kol]?></span>
          <?php } ?>
        </td>
        <td class="imgNumber footballNumber">
          <?php if ($recordgols->fields[kol] > 0){?>
              <img src="/img/football.png" alt="">
              <span><?=$recordgols->fields[kol]?></span>
          <?php } ?>
        </td>
        <td class="imgNumber">
          <?php if ($recordasist->fields[kol] > 0){?>
              <img src="/img/asist_dark.png" alt="">
              <span><?=$recordasist->fields[kol]?></span>
          <?php } ?>
        </td>
        <td class="imgNumber">
          <?php if ($recordyel->fields[kol] > 0){?>
            <img src="/img/yellow-rectangle.png" alt="">
            <span><?=$recordyel->fields[kol]?></span>
          <?php } ?>
        </td>
        <td class="imgNumber">
          <?php if ($recordred->fields[kol] > 0){?>
            <img src="/img/red-rectangle.png" alt="">
            <span><?=$recordred->fields[kol]?></span>
          <?php } ?>
        </td>

        <td>
          <?php if ($kep){if ($recordSet->fields[active] == 1) {echo"<font color='green'>Заявлений</font></td><td>";
          echo"<input type='button' value='Відкликати' onclick='out(".$id.", ".$recordSet->fields[id].", 0);'>";}else {echo"<font color='red'>Відкликаний</font></td><td>";echo"<input type='button' value='Заявити' onclick='out(".$id.", ".$recordSet->fields[id].", 1);'>";}} ?>
        </td>
      </tr>

        <?
            
            $recordSet->MoveNext();
          } // while (!$recordSet->EOF) 
      } // if(!isset($_GET['foo'])...

    $time = microtime(true) - $start;
    error_log($_SERVER['REQUEST_URI']." ->Gen in ".$time."sec");
        ?>

      </table>
		</div>
	</div>
</article>

<!-- //////////// Note 29.11.2024 ///////////// -->
<?php } ?>
<!-- ///////////////////////// -->

<?
  function faceupload($man, $team, $tour){
    echo '<form method="post" action="http://v9ky.in.ua/capitan/crop/" enctype="multipart/form-data">
      <input type="file" name="rule_picture" value="Загрузить новую" onchange="this.form.submit();">
      <input type="hidden" name="man" value="'.$man.'">
      <input type="hidden" name="team" value="'.$team.'">
      <input type="hidden" name="tour" value="'.$tour.'">
    </form>';
  }

  include_once "footer.php";
?>