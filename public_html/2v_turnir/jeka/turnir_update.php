<?PHP
define('READFILE', true);
require_once ("menu.php");
require_once('config.php');
?>
<center>
<h1>Редактирование турнира</h1>
<form action="turnir_update.php" method="GET" onSubmit="return sendForm(this);" ENCTYPE='multipart/form-data'>
<?
if ((!empty($_GET))){
  if (isset($_GET['red'])){
    if (isset($_GET['id'])) $team_id=intval($_GET['id']);
    if (isset($_GET['name'])) {$name=filter_string($_GET['name']); } ELSE {$name="";}
    if (isset($_GET['ru'])) {$ru=filter_string($_GET['ru']); } ELSE {$ru="";}
   // if (isset($_GET['season'])) {$season=filter_string($_GET['season']); } ELSE {$season="";}
    if (isset($_GET['seasons'])) $seasons=intval($_GET['seasons']);
    if (isset($_GET['active'])) $active=intval($_GET['active']);
    if (isset($_GET['cup'])) $cup=intval($_GET['cup']);
    if (isset($_GET['city'])) $city=intval($_GET['city']);
    if (isset($_GET['priority'])) $priority=intval($_GET['priority']);
    if (isset($_GET['ves'])) $ves=intval($_GET['ves']);
    if (isset($_GET['zayavka_price'])) $zayavka_price=1.0 * $_GET['zayavka_price'];
    if (isset($_GET['team_match_price'])) $team_match_price=1.0 * $_GET['team_match_price'];
	if (isset($_GET['field'])) $field=intval($_GET['field']);
	if (isset($_GET['niz_turnirki'])) $niz_turnirki=intval($_GET['niz_turnirki']);

    $record["name"] = $name;
    $record["ru"] = $ru;
    $recordsez = $db->Execute("select * from v9ky_seasons where id=$seasons");
    $record["season"] = $recordsez->fields['name'];
    $record["seasons"] = $seasons;
    $record["active"] = $active;
    $record["cup"] = $cup;
    $record["city"] = $city;
    $record["priority"] = $priority;
    $record["ves"] = $ves;
    $record["zayavka_price"] = $zayavka_price;
    $record["team_match_price"] = $team_match_price;
	$record["field"] = $field;
    $record["niz_turnirki"] = $niz_turnirki;

    //запись турнира в базу
    if (isset($_GET['red'])) {$redatirovat_or_else=intval($_GET['red']);
    } ELSE {$redatirovat_or_else=0;}
    if ($redatirovat_or_else==1)
    {
    	 $db->AutoExecute('v9ky_turnir',$record,'UPDATE', 'id = '.$team_id.'');
    }else {$db->AutoExecute('v9ky_turnir',$record,'INSERT');}
  }
  $name = "";
  $ru = "";
  $season = "";
  $seasons = 0;
  $active = "1";
  $cup = "0";
  $city = "0";
  $priority = "0";
  $ves = "0";
  $zayavka_price = "0";
  $team_match_price = "0";
  $field = "0";
  $niz_turnirki = "0";

  if ((isset($_GET['id']))&&(intval($_GET['id'])*1>0)&&(($redatirovat_or_else==1)||(!isset($_GET['red']))))
  {
  	 $id_to_update=intval($_GET['id'])*1;
	 $recordSet1 = $db->Execute("select * from v9ky_turnir where id='".$id_to_update."'");
  }else {
    $recordSet1 = $db->Execute("select * from v9ky_turnir where id=(SELECT LAST_INSERT_ID())");
    $id_to_update=$recordSet1->fields['id'];
  }

	 $name = $recordSet1->fields['name'];
	 $ru = $recordSet1->fields['ru'];
	 $season = $recordSet1->fields['season'];
         $seasons = $recordSet1->fields['seasons'];
	 $active = $recordSet1->fields['active'];
         $cup = $recordSet1->fields['cup'];
         $city = $recordSet1->fields['city'];
         $priority = $recordSet1->fields['priority'];
         $ves = $recordSet1->fields['ves'];
         $zayavka_price = $recordSet1->fields['zayavka_price'];
         $team_match_price = $recordSet1->fields['team_match_price'];
		 $field = $recordSet1->fields['field'];
		 $niz_turnirki = $recordSet1->fields['niz_turnirki'];

	 echo"Название полное: <input type='text' name='ru' size='100' value='".$ru."'><br><br>";
	 echo"Название символьное: <input type='text' name='name' size='100' value='".$name."'><br><br>";

         echo"Город: <select name='city' size=1> ";
	 $recordSet3 = $db->Execute("select * from v9ky_city ORDER BY name_ua");
	 while (!$recordSet3->EOF)
	 {
		 if ($recordSet3->fields[id]==$city) print "<option value='".$recordSet3->fields[id]."' selected>".$recordSet3->fields[name_ua]."</option> \n";
		 else print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[name_ua]."</option> \n";
		 $recordSet3->MoveNext();
	 }
	 echo"</select>";

         echo"Вага в рейтингу: <select name='ves' size=1> ";
	 $recordves = $db->Execute("select * from v9ky_ratings_baly ORDER BY win desc");
	 while (!$recordves->EOF)
	 {
		 if ($recordves->fields[id]==$ves) print "<option value='".$recordves->fields[id]."' selected>".$recordves->fields[name]."</option> \n";
		 else print "<option value='".$recordves->fields[id]."'>".$recordves->fields[name]."</option> \n";
		 $recordves->MoveNext();
	 }
	 echo"</select>";

         echo"Очередность в меню (0->1000): <input type='text' name='priority' size='10' value='".$priority."'><br><br>";
		 
	echo"Поле: <select name='field' size=1> ";
	 $recordSet3 = $db->Execute("select b.id, b.name, (select a.name_ua from v9ky_city a where a.id=b.city) as cityn from v9ky_fields b where b.visible=1 ORDER BY b.priority, b.name");
	 while (!$recordSet3->EOF)
	 {
		 if ($recordSet3->fields[id]==$field) print "<option value='".$recordSet3->fields[id]."' selected>".$recordSet3->fields[cityn]." ".$recordSet3->fields[name]."</option> \n";
		 else print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[cityn]." ".$recordSet3->fields[name]."</option> \n";
		 $recordSet3->MoveNext();
	 }
	 echo"</select>";
		 
         echo"Ціна заявки на турнір: <input type='text' name='zayavka_price' size='10' value='".$zayavka_price."'>Ціна матчу для команди: <input type='text' name='team_match_price' size='10' value='".$team_match_price."'><br><br>";

     echo"Сезон (2014/2015): <input type='text' name='season' size='100' value='".$season."'><br><br>";
         
         echo"Сезон: <select name='seasons' size=1> ";
	 $recordsez = $db->Execute("select * from v9ky_seasons ORDER BY data desc");
	 while (!$recordsez->EOF)
	 {
		 if ($recordsez->fields[id]==$seasons) print "<option value='".$recordsez->fields[id]."' selected>".$recordsez->fields[name]."</option> \n";
		 else print "<option value='".$recordsez->fields[id]."'>".$recordsez->fields[name]."</option> \n";
		 $recordsez->MoveNext();
	 }
	 echo"</select>";
	 
	 echo"Команд вылетят с конца турнирки, шт.: <input type='text' name='niz_turnirki' size='1' value='".$niz_turnirki."'>&nbsp;&nbsp;";

	 echo"Активный (1-да/0-нет): <input type='text' name='active' size='1' value='".$active."'>&nbsp;&nbsp;";
         echo"Тип <select name='cup'>
                <option ";
         if ($cup==0){echo"selected";}
         echo" value='0'>Чемпіонат 1 коло</option>
                <option ";
         if ($cup==2){echo"selected";}
         echo" value='2'>Чемпіонат 2 коло</option>
                <option ";
         if ($cup==1){echo"selected";}
         echo" value='1'>Кубок</option>
         
              </select><br><br>";
     echo"<br> <br><input type='submit' value='  Изменить  '><input type='radio' name='red' value='1' checked>
	   Внести изменения в турнир ".$name."<input type='radio' name='red' value='0'>
	   Добавить как новую";
	 echo "<input type='hidden' name='id' value='".$id_to_update."'>";
	 echo "</form> ";
 


  if (isset($_GET['red'])){ echo "Турнир: <H2> ".$name." </H2> изменения приняты";}
   //@header("Location: $_SERVER[РНР_SELF]" ) ;

}else {
   echo"Название полное: <input type='text' name='ru' size='100' ><br><br>";
   echo"Название символьное: <input type='text' name='name' size='100' ><br><br>";

   echo"Город: <select name='city' size=1> ";
	 $recordSet3 = $db->Execute("select * from v9ky_city ORDER BY name_ua");
	 while (!$recordSet3->EOF)
	 {
            print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[name_ua]."</option> \n";
	    $recordSet3->MoveNext();
	 }
	 echo"</select>";
   echo"Вага в рейтингу: <select name='ves' size=1> ";
	 $recordSet3 = $db->Execute("select * from v9ky_ratings_baly ORDER BY win desc");
	 while (!$recordSet3->EOF)
	 {
            print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[name]."</option> \n";
	    $recordSet3->MoveNext();
	 }
	 echo"</select>";
   echo"Очередность в меню (0->1000): <input type='text' name='priority' size='10'><br><br>";
   echo"Ціна заявки на турнір: <input type='text' name='zayavka_price' size='10' value='0'>Ціна матчу для команди: <input type='text' name='team_match_price' size='10' value='0'><br><br>";

   echo"Сезон: <select name='seasons' size=1> ";
	 $recordsez = $db->Execute("select * from v9ky_seasons ORDER BY data desc");
	 while (!$recordsez->EOF)
	 {
		print "<option value='".$recordsez->fields[id]."'>".$recordsez->fields[name]."</option> \n";
		 $recordsez->MoveNext();
	 }
	 echo"</select><br><br>";
   echo"Активный (1-да/0-нет): <input type='text' name='active' size='1' value='1'><input type='hidden' name='red' value='0'>";
   echo"&nbsp;&nbsp;Тип <select name='cup'><option selected value='0'>Чемпионат</option>
           <option value='1'>Кубок</option>   
        </select><br><br>";
   echo"<br> <br><input type='submit' value='  Создать турнир  '></form> ";

}
?>

</center>
</body>
</html>