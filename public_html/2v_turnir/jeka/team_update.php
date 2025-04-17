<?PHP
define('READFILE', true);
require_once ("menu.php");
require_once('ajax_forms/PHPLiveX.php');
session_start();



class Validation {	
  public function sel_teams($str){
	global $db;
	$str=filter_string($str);
	$recordSett = $db->Execute("select * from v9ky_teams where name LIKE '%".$str."%' ORDER BY name");
                
        $test = "<select name='teams' size=1>";
        while (!$recordSett->EOF)
	 {
           $recordcity = $db->Execute("select * from v9ky_city where id=".$recordSett->fields[city]."");
	   $test .= "<option value='".$recordSett->fields[id]."'>".$recordSett->fields[name]." - ".$recordcity->fields[name_ua]."</option> \n";
	   $recordSett->MoveNext();
	 }
        $test .= "</select>";
        return $test;
  }

}  




$ajax = new PHPLiveX();
//
$myClass = new Validation();
$ajax->AjaxifyObjectMethods(array("myClass" => array("sel_teams")));
//
$ajax->Run();

?>

<script type="text/javascript">
function sendForm(form){
  return PLX.Submit(form, {
    "preloader":"pr",
	"target":"pr1"
  });
}
</script>

<script type="text/javascript">
function findteam(str){
    myClass.sel_teams(str, {

        "onFinish": function(response){
            var msg = document.getElementById("sel_teams");
            msg.innerHTML = response;

        }
    });
}
</script>

<center>
<h1>Редагування команди</h1>
<form action="team_update.php" method="GET" ENCTYPE='multipart/form-data'>
<?
if ((!empty($_GET))){
	//копирование команды
   if (isset($_GET['copy'])){
	   if (isset($_GET['id'])) {$team_id=intval($_GET['id']);
	 $recor = $db->Execute("select teams, date, name, pict, capitan, tel1, photo, email, tshirt from v9ky_team where id='".$team_id."'");
	 $db->AutoExecute('v9ky_team',$recor->fields,'INSERT');
	 $recordSet10 = $db->Execute("select * from v9ky_team where id=(SELECT LAST_INSERT_ID())");
     $id_to=$recordSet10->fields['id'];
	 $recor1 = $db->Execute("select team, nomer, name1, name2, name3, age, face, man, active, v9ky, amplua, dubler from v9ky_player where team='".$team_id."' and vibuv=0");
	 while (!$recor1->EOF)
	 {
	    $recor1->fields['team'] = $id_to;
	    $db->AutoExecute('v9ky_player',$recor1->fields,'INSERT');
	    $recor1->MoveNext();
	 }
	 
							   }
   }
  //копирование команды

  if (isset($_GET['red'])){
    if (isset($_GET['id'])) $team_id=intval($_GET['id']);
    if (isset($_GET['teams'])) $teams=intval($_GET['teams']);
    
    if (isset($_GET['borg_comment'])) {$borg_comment=filter_string($_GET['borg_comment']);
      } ELSE {$borg_comment="";}
    if ($teams > 0) {
      $recordteams = $db->Execute("select * from v9ky_teams where id='".$teams."'");
      $name = $recordteams->fields[name];
    }else{
      if (isset($_GET['name'])) {$name=filter_string($_GET['name']);
      } ELSE {$name="";}
    }
    if (isset($_GET['email'])) {$email=filter_string($_GET['email']);
      } ELSE {$email="";}
    if (isset($_GET['tel1'])) {$tel1=filter_string($_GET['tel1']);
      } ELSE {$tel1="";}
    if (isset($_GET['capitan'])) {$capitan=filter_string($_GET['capitan']);
      } ELSE {$capitan="";}
    if (isset($_GET['trainer'])) {$trainer=filter_string($_GET['trainer']);
      } ELSE {$trainer="";}
    if (isset($_GET['manager'])) {$manager=filter_string($_GET['manager']);
      } ELSE {$manager="";}
    if (isset($_GET['pict'])) {$picture=filter_string($_GET['pict']);
      } ELSE {$picture="";}
    if (isset($_GET['photo'])) {$photo=filter_string($_GET['photo']);
      } ELSE {$photo="";}
	if (isset($_GET['group'])) {$group=filter_string($_GET['group']);
      } ELSE {$group="";}
	  if (isset($_GET['turnir'])) {$turnir_id=intval($_GET['turnir']);} else $turnir_id=0;
    if (isset($_GET['tcolor'])) {$tcolor=1*($_GET['tcolor']);} else $tcolor=0;
    if (isset($_GET['skidka_match_team'])) {$skidka_match_team=1*($_GET['skidka_match_team']);} else $skidka_match_team=0;
    if (isset($_GET['skidka_zayavky'])) {$skidka_zayavky=1*($_GET['skidka_zayavky']);} else $skidka_zayavky=0;
    if (isset($_GET['dolg'])) {$dolg=1.0*($_GET['dolg']);} else $dolg=0;
    if (isset($_GET['last_call1'])) {$last_call1=($_GET['last_call1']); } ELSE {$last_call1="";}
    if (isset($_GET['last_call2'])) {$last_call2=($_GET['last_call2']); } ELSE {$last_call2="";}
    if (isset($_GET['last_call3'])) {$last_call3=($_GET['last_call3']); } ELSE {$last_call3="";}
    if (isset($_GET['tshirt_select'])) {$tshirt_select=($_GET['tshirt_select']); } ELSE {$tshirt_select="";}

    $record["name"] = $name;
    $record["email"] = $email;
    $record["borg_comment"] = $borg_comment;
    $record["capitan"] = $capitan;
    $record["trainer"] = $trainer;
    $record["manager"] = $manager;
    $record["pict"] = $picture;
    $record["tel1"] = $tel1;
    $record["photo"] = $photo;
    $record["grupa"] = $group;
    $record["turnir"] = $turnir_id;
    $record["teams"] = $teams;
    $record["tcolor"] = $tcolor;
    $record["skidka_match_team"] = $skidka_match_team;
    $record["skidka_zayavky"] = $skidka_zayavky;
    $record["dolg"] = $dolg;
    $record["last_call1"] = $last_call1;
    $record["last_call2"] = $last_call2;
    $record["last_call3"] = $last_call3;
    $record["tshirt"] = $tshirt_select;

	//запись в базу
	if (isset($_GET['red'])) {$redatirovat_or_else=intval($_GET['red']);
        } ELSE {$redatirovat_or_else=0;}
	if ($redatirovat_or_else==1)
	  {
    	 $db->AutoExecute('v9ky_team',$record,'UPDATE', 'id = '.$team_id.'');
	  }else {$db->AutoExecute('v9ky_team',$record,'INSERT');}
	  
	$recordt["upd_teams_match_stat"] = 1;
	$recordt["upd_table"] = 1;
    $db->AutoExecute('v9ky_turnir',$recordt,'UPDATE', 'id = '.$turnir_id.'');
  }
  $name="";
  $email="";
  $capitan=0;
  $picture="";
  $tel1="";
  $photo="";
  $group="";
  $turnir_id="";
  $teams="";
  $tcolor=0;
  $skidka_match_team=0;$skidka_zayavky=0;
  $dolg=0; $last_call1=""; $last_call2=""; $last_call3="";
  $borg_comment = "";

  if ((isset($_GET['id']))&&(intval($_GET['id'])*1>0)&&(($redatirovat_or_else==1)||(!isset($_GET['red']))))
  {
  	 $id_to_update=intval($_GET['id'])*1;
	 $recordSet1 = $db->Execute("select * from v9ky_team where id='".$id_to_update."'");
  }else {
    $recordSet1 = $db->Execute("select * from v9ky_team where id=(SELECT LAST_INSERT_ID())");
    $id_to_update=$recordSet1->fields['id'];
  }

  $last_call1 = date_create($recordSet1->fields['last_call1']);
  $last_call2 = date_create($recordSet1->fields['last_call2']);
  $last_call3 = date_create($recordSet1->fields['last_call3']);
	 $recordSet1 = $db->Execute("select * from v9ky_team where id='".$id_to_update."'");
         $recordteams = $db->Execute("select * from v9ky_teams where id='".$recordSet1->fields[teams]."'");
	 //копирование команды
	 echo"<a href='team_update.php?id=".$id_to_update."&copy=1 '>ЗКОПіЮВАТИ КОМАНДУ</a><br>";
	 //копирование команды
	 echo"Назва: <input type='text' placeholder='Пошук' name='name' size='20' value='".stripcslashes($recordSet1->fields[name])."' onchange='findteam(this.value);'><span id='sel_teams'><select name='teams' size=1>
           <option value='".$teams."' selected>".$recordteams->fields[name]."</option></select></span>";
         echo'Вибирати зі списку<br><br>';
         echo" Капітан <select name='capitan' size=1> ";
         $recordSet5 = $db->Execute("select * from v9ky_player where team=".$id_to_update." ORDER BY nomer");
         
         $teamOfPlayers = [];
         
         while (!$recordSet5->EOF)
         {
           if ($recordSet5->fields[id]==$recordSet1->fields['capitan']) print "<option value='".$recordSet5->fields[id]."' selected>
           ".$recordSet5->fields[nomer]." ".$recordSet5->fields[name1]." ".$recordSet5->fields[name2]."</option> \n";
           else print "<option value='".$recordSet5->fields[id]."'>".$recordSet5->fields[nomer]." ".$recordSet5->fields[name1]." ".$recordSet5->fields[name2]."</option> \n";
           $teamOfPlayers[] = ($recordSet5->fields);
		 $recordSet5->MoveNext();
	 }
	 echo"</select> Tel: <input type='text' name='tel1' size='15' value='".($recordSet1->fields['tel1'])."'>Email: <input type='text' name='email' size='15' value='".($recordSet1->fields['email'])."'> ";
   
   // Тренер 
   echo  '<br><br>';
   echo " Тренер <select name='trainer' size=1> ";
    foreach ($teamOfPlayers as $player) {
      if ($player['id']==$recordSet1->fields['trainer']) print "<option value='".$player['id']."' selected>
        ".$player['nomer']." ".$player['name1']." ".$player['name2']."</option> \n";
      else print "<option value='".$player['id']."'>".$player['nomer']." ".$player['name1']." ".$player['name2']."</option> \n";
    }   
   echo '</select>';

   // Менеджер 
   echo  '<br><br>';
   echo " Менеджер <select name='manager' size=1> ";
    foreach ($teamOfPlayers as $player) {
      if ($player['id']==$recordSet1->fields['manager']) print "<option value='".$player['id']."' selected>
        ".$player['nomer']." ".$player['name1']." ".$player['name2']."</option> \n";
      else print "<option value='".$player['id']."'>".$player['nomer']." ".$player['name1']." ".$player['name2']."</option> \n";
    }   
   echo '</select>';


         echo"<br><br>Знижка на заявку на турнір, грн: <input type='text' name='skidka_zayavky' size='1' value='".($recordSet1->fields['skidka_zayavky'])."'> Знижка на матч для команди, грн: <input type='text' name='skidka_match_team' size='1' value='".($recordSet1->fields['skidka_match_team'])."'>
           Долг прошлых турниров, грн: <input type='text' name='dolg' size='1' value='".($recordSet1->fields['dolg'])."'>";

         echo"<br><br>Попередження1: <input type='date' name='last_call1' size='60' value='".date_format($last_call1, 'Y-m-d')."'>  ";
         echo"Попередження2: <input type='date' name='last_call2' size='60' value='".date_format($last_call2, 'Y-m-d')."'>  ";
         echo"Попередження3: <input type='date' name='last_call3' size='60' value='".date_format($last_call3, 'Y-m-d')."'>  ";
         echo"<br><br>Коментар до боргу: <input type='text' name='borg_comment' size='100' value='".($recordSet1->fields['borg_comment'])."'>";
	 echo"<br><br>Група: <input type='text' name='group' size='1' value='".($recordSet1->fields['grupa'])."'> ";
	 echo "";

	 echo"Турнір: <select name='turnir' size=1> ";
	 $recordSet3 = $db->Execute("select * from v9ky_turnir where active>0");
	 while (!$recordSet3->EOF)
	 {
		 if ($recordSet3->fields[id]==$recordSet1->fields['turnir']) print "<option value='".$recordSet3->fields[id]."' selected>".$recordSet3->fields[name]."  ".$recordSet3->fields[season]."</option> \n";
		 else print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[name]."  ".$recordSet3->fields[season]."</option> \n";
		 $recordSet3->MoveNext();
	 }
	 echo"</select><br><br>";
         for ($i = 0; $i <= 12; $i++) {
           $colors[$i]="";
           if($recordSet1->fields['tcolor']==$i) $colors[$i]=" checked";
         }
        //  echo'<input id="img1" type="radio" name="tcolor" value="1"'.$colors[1].'>
        //       <label for="img1"><img width=30px src="picts/1t.jpg" ></label>
        //       <input id="img2" type="radio" name="tcolor" value="2"'.$colors[2].'>
        //       <label for="img2"><img width=30px src="picts/2t.jpg" ></label>
        //       <input id="img3" type="radio" name="tcolor" value="3"'.$colors[3].'>
        //       <label for="img3"><img width=30px src="picts/3t.jpg" ></label>
        //       <input id="img4" type="radio" name="tcolor" value="4"'.$colors[4].'>
        //       <label for="img4"><img width=30px src="picts/4t.jpg" ></label>
        //       <input id="img5" type="radio" name="tcolor" value="5"'.$colors[5].'>
        //       <label for="img5"><img width=30px src="picts/5t.jpg" ></label>
        //       <input id="img6" type="radio" name="tcolor" value="6"'.$colors[6].'>
        //       <label for="img6"><img width=30px src="picts/6t.jpg" ></label>
        //       <input id="img7" type="radio" name="tcolor" value="7"'.$colors[7].'>
        //       <label for="img7"><img width=30px src="picts/7t.jpg" ></label>
        //       <input id="img8" type="radio" name="tcolor" value="8"'.$colors[8].'>
        //       <label for="img8"><img width=30px src="picts/8t.jpg" ></label>
        //       <input id="img9" type="radio" name="tcolor" value="9"'.$colors[9].'>
        //       <label for="img9"><img width=30px src="picts/9t.jpg" ></label>
        //       <input id="img10" type="radio" name="tcolor" value="10"'.$colors[10].'>
        //       <label for="img10"><img width=30px src="picts/10t.jpg" ></label>
        //       <input id="img11" type="radio" name="tcolor" value="11"'.$colors[11].'>
        //       <label for="img11"><img width=30px src="picts/11t.jpg" ></label>
        //       <input id="img12" type="radio" name="tcolor" value="12"'.$colors[12].'>
        //       <label for="img12"><img width=30px src="picts/12t.jpg" ></label>
        //       <input id="img13" type="radio" name="tcolor" value="0"'.$colors[0].'>
        //       <label for="img13"><img width=30px src="picts/0m.jpg" ></label>
              
        //       <br><br>';

  $tshirtDir = '../../img/t-shirt/';
  $tshirtFiles = glob($tshirtDir . '*.png'); 
  // var_dump($tshirtFiles);
  echo '<ul style="list-style: none; display:flex; flex-wrap:wrap; gap: 10px; padding: 0; margin: 0;">';
  foreach ($tshirtFiles as $file) {
    // Отримуємо тільки частину шляху від /img...
    // $relativePath = str_replace('../..', '', $file);  
    $relativePath = basename($file);  
    
    $checked = ($recordSet1->fields['tshirt'] == $relativePath) ? 'checked' : '';
  
      echo '<li style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 8px;">';
      echo '<label style="display:flex; flex-direction: column; align-items: center; justify-content: center;">';
      echo '<input type="radio" name="tshirt_select" value="' . $relativePath . '" ' . $checked . ' style="margin-right: 10px;">';
      echo '<img src="/img/t-shirt/' . $relativePath . '" style="width: 30px; height: auto; border:1px solid #ccc; padding:4px; margin-right:10px;">';
      echo '<div style="font-size: 10px">' .$relativePath .'</div>';
      echo '</label>';
      // Кнопка удаления
  echo '<button type="button" onclick="deleteTshirt(\'' . htmlspecialchars($relativePath) . '\')" style="font-size:10px; margin-left: 10px; background:red; color:white; border:none; padding:4px 8px; cursor:pointer;">Видалити</button>';
      echo '</li>';
  }  
  echo '</ul>';

          
              
	 echo"Посилання на фото команди: <input type='text' name='photo' size='100' value='".($recordSet1->fields['photo'])."'>";
	 echo "<br><br>";
     echo"<img src='../team_logo/".$recordSet1->fields['pict']."' id=pict_id2_for_java alt='' width='100' />";
     echo "Картинка: <input type='text' id='pict_id_for_java' name='pict' size='15' value='".$recordSet1->fields['pict']."'>";
	 echo"<br> <br><input type='submit' value='  Змінити  '><input type='radio' name='red' value='1' checked>
	 Внести зміни в команду ".stripcslashes($recordSet1->fields['name'])."<input type='radio' name='red' value='0' >
	 Додати як нову<input type='hidden' name='id' value='".$recordSet1->fields[0]."'></form> ";
	 if (isset($_GET['red'])){ echo "Команда: <H2> ".$recordSet1->fields['name']." </H2> изменения приняты";}
}else {
echo"Назва: <input type='text' placeholder='Пошук' name='name' size='20'  onchange='findteam(this.value);'><span id='sel_teams'><select name='teams' size=1>
           </select></span>";
echo"Вибирати зі списку<br><br>
<br><br>

<img src='' id=pict_id2_for_java alt='' width='100' />
Картинка: <input type='text' id='pict_id_for_java' name='pict' size='15'>
<input type='hidden' name='red' value='0'>
<br><br>
<input type='submit' value='  Створити  '>
</form>  ";
}
?>
<span id="pr" style="visibility:hidden;"><i>Loading...</i></span>
<span id="pr1"></span>
<br>


<script>
function putpict(name) {
   document.getElementById('pict_id_for_java').value = name;
   document.getElementById('pict_id2_for_java').src = '<?echo $pict_path;?>'+name;
}
</script>

<form action="add_pict.php" method="POST" onSubmit="return sendForm(this);" ENCTYPE='multipart/form-data'>
  Добавить картинку: <input type="file" name="rule_picture" size="50" >
  <input type="submit" value="  Загрузить картинку  ">
</form>


<?php
// Повідомлення валідації
if (!empty($_SESSION['upload_errors'])) {
  echo '<div style="color: red; margin-bottom: 10px;">';
  foreach ($_SESSION['upload_errors'] as $error) {
      echo htmlspecialchars($error) . "<br>";
  }
  echo '</div>';
  unset($_SESSION['upload_errors']);
}
if (!empty($_SESSION['upload_success'])) {
  echo '<div style="color: green; margin-bottom: 10px;">' . htmlspecialchars($_SESSION['upload_success']) . '</div>';
  unset($_SESSION['upload_success']);
}
?>
<form action="add_tshirt.php" method="POST" ENCTYPE='multipart/form-data'>
  <h2 style="margin-bottom: 0">Додати футболку</h2>
  <p style="font-size: 10px">тільки png, розміром до 1 Мб</p>
  <input type="file" name="tshirt_upload" size="50" id="tshirt_upload" >  
  <input type="hidden" name="team_id" value="<?=$_GET['id']?>">
  <input type="submit" value="  Завантажити футблоку  ">
</form>

<form id="deleteForm" method="POST" action="delete_tshirt.php" style="display:none;">
  <input type="hidden" name="filename" id="deleteFilename">
  <input type="hidden" name="team_id" value="<?=$_GET['id']?>">
</form>

<?
  $dir = $pict_path; // Папка с изображениями
  $cols = 8; // Количество столбцов в будущей таблице с картинками
//  $files = scandir($dir); // Берём всё содержимое директории


  $ignored = array('.', '..', '.svn', '.htaccess');

  $files = array();    
  foreach (scandir($dir) as $file) {
    if (in_array($file, $ignored)) continue;
    $files[$file] = filemtime($dir . '/' . $file);
  }

  arsort($files);
  $files = array_keys($files);


  echo "<table>"; // Начинаем таблицу
  $k = 0; // Вспомогательный счётчик для перехода на новые строки
  for ($i = 0; $i < count($files); $i++) { // Перебираем все файлы
    if (($files[$i] != ".") && ($files[$i] != "..")&&($files[$i] !=".htaccess")) { // Текущий каталог и родительский пропускаем
      if ($k % $cols == 0) echo "<tr>"; // Добавляем новую строку
      echo "<td>"; // Начинаем столбец
      $path = $dir.$files[$i]; // Получаем путь к картинке
      echo '<a href="#" onclick="putpict(';
	  echo "'".$files[$i]."'";
	  echo ');">'; // Делаем ссылку на картинку

      echo "<img src='$path' alt='' width='100' />"; // Вывод превью картинки
      echo "</a>"; // Закрываем ссылку
	  echo '<br><center><a href="pict_del.php?pict='.$path.'">|x|</a></center>';

      echo "</td>"; // Закрываем столбец
      /* Закрываем строку, если необходимое количество было выведено, либо данная итерация последняя */
      if ((($k + 1) % $cols == 0) || (($i + 1) == count($files))) echo "</tr>";
      $k++; // Увеличиваем вспомогательный счётчик
    }
  }
  echo "</table>"; // Закрываем таблицу

?>

<script>
function deleteTshirt(filename) {
  if (confirm("Видалити футболку: " + filename + "?")) {
    document.getElementById("deleteFilename").value = filename;
    document.getElementById("deleteForm").submit();
  }
}
</script>

</center>
</body>
</html>