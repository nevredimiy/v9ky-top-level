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
        // $to = '380965582148';
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


<?php
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