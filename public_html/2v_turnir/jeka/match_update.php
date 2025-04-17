<?PHP
define('READFILE', true);
require_once ("menu.php");
require_once('config.php');

$colors = array('picts/0m.jpg',
                'picts/1t.jpg',
                'picts/2t.jpg',
                'picts/3t.jpg',
                'picts/4t.jpg',
                'picts/5t.jpg',
                'picts/6t.jpg',
                'picts/7t.jpg',
                'picts/8t.jpg',
                'picts/9t.jpg',
                'picts/10t.jpg',
                'picts/11t.jpg',
                'picts/12t.jpg');

require_once('ajax_forms/PHPLiveX.php');
$ajax = new PHPLiveX(array("getTown", "getColor", "getmColor", "getmColor2" , 'getTshirtTeam'));  
$ajax->Run(); // Must be called inside the 'html' or 'body' tags  



function getTown($city_id){  
    global $db;  
        
    $recordSet2 = $db->Execute("select * from v9ky_team where turnir='".$city_id."' ORDER BY name " );
    while (!$recordSet2->EOF)
	 {
           $options[] = array("value" => $recordSet2->fields[id], "text" => $recordSet2->fields[name]);
           $recordSet2->MoveNext();
         }
    return $options;  
}  
function getColor($team_id){  
    global $db, $colors;  
        
    $recordSet = $db->Execute("select * from v9ky_team where id='".$team_id."'" );
    $color = 1 * $recordSet->fields[tcolor];
    
    $tcolor = $colors[$color]; 
    $tcolor = '<img width=30px src="'.$tcolor.'" >';
    //$options[] = array("src" => $tcolor);
    return $tcolor;  
}  

function getTshirtTeam($team_id){
  global $db, $colors;  
  $recordSet = $db->Execute("select * from v9ky_team where id='".$team_id."'" );
  $tshirt = $recordSet->fields['tshirt'];
  $tshirt = '<img width=30px src="/img/t-shirt/'.$tshirt.'" >';
  return $tshirt;
}

function getmColor($team_id){  
    global $db, $colors;  
        
    $recordSet = $db->Execute("select * from v9ky_team where id='".$team_id."'" );
    $color = 1 * $recordSet->fields[tcolor];
    for ($i = 0; $i <= 12; $i++) {
           $ccolors[$i]="";
           if($color==$i) $ccolors[$i]=" checked";
         }
    $tcolor='<input id="img1" type="radio" name="tcolor1" value="1"'.$ccolors[1].'>
              <label for="img1"><img width=30px src="picts/1t.jpg" ></label>
              <input id="img2" type="radio" name="tcolor1" value="2"'.$ccolors[2].'>
              <label for="img2"><img width=30px src="picts/2t.jpg" ></label>
              <input id="img3" type="radio" name="tcolor1" value="3"'.$ccolors[3].'>
              <label for="img3"><img width=30px src="picts/3t.jpg" ></label>
              <input id="img4" type="radio" name="tcolor1" value="4"'.$ccolors[4].'>
              <label for="img4"><img width=30px src="picts/4t.jpg" ></label>
              <input id="img5" type="radio" name="tcolor1" value="5"'.$ccolors[5].'>
              <label for="img5"><img width=30px src="picts/5t.jpg" ></label>
              <input id="img6" type="radio" name="tcolor1" value="6"'.$ccolors[6].'>
              <label for="img6"><img width=30px src="picts/6t.jpg" ></label>
              <input id="img7" type="radio" name="tcolor1" value="7"'.$ccolors[7].'>
              <label for="img7"><img width=30px src="picts/7t.jpg" ></label>
              <input id="img8" type="radio" name="tcolor1" value="8"'.$ccolors[8].'>
              <label for="img8"><img width=30px src="picts/8t.jpg" ></label>
              <input id="img9" type="radio" name="tcolor1" value="9"'.$ccolors[9].'>
              <label for="img9"><img width=30px src="picts/9t.jpg" ></label>
              <input id="img10" type="radio" name="tcolor1" value="10"'.$ccolors[10].'>
              <label for="img10"><img width=30px src="picts/10t.jpg" ></label>
              <input id="img11" type="radio" name="tcolor1" value="11"'.$ccolors[11].'>
              <label for="img11"><img width=30px src="picts/11t.jpg" ></label>
              <input id="img12" type="radio" name="tcolor1" value="12"'.$ccolors[12].'>
              <label for="img12"><img width=30px src="picts/12t.jpg" ></label>
              <input id="img13" type="radio" name="tcolor1" value="0"'.$ccolors[0].'>
              <label for="img13"><img width=30px src="picts/0m.jpg" ></label>';
    return $tcolor;  
}  

function getmColor2($team_id){  
    global $db, $colors;  
        
    $recordSet = $db->Execute("select * from v9ky_team where id='".$team_id."'" );
    $color = 1 * $recordSet->fields[tcolor];
    for ($i = 0; $i <= 12; $i++) {
           $ccolors[$i]="";
           if($color==$i) $ccolors[$i]=" checked";
         }
    $tcolor='<input id="img21" type="radio" name="tcolor2" value="1"'.$ccolors[1].'>
              <label for="img21"><img width=30px src="picts/1t.jpg" ></label>
              <input id="img22" type="radio" name="tcolor2" value="2"'.$ccolors[2].'>
              <label for="img22"><img width=30px src="picts/2t.jpg" ></label>
              <input id="img23" type="radio" name="tcolor2" value="3"'.$ccolors[3].'>
              <label for="img23"><img width=30px src="picts/3t.jpg" ></label>
              <input id="img24" type="radio" name="tcolor2" value="4"'.$ccolors[4].'>
              <label for="img24"><img width=30px src="picts/4t.jpg" ></label>
              <input id="img25" type="radio" name="tcolor2" value="5"'.$ccolors[5].'>
              <label for="img25"><img width=30px src="picts/5t.jpg" ></label>
              <input id="img26" type="radio" name="tcolor2" value="6"'.$ccolors[6].'>
              <label for="img26"><img width=30px src="picts/6t.jpg" ></label>
              <input id="img27" type="radio" name="tcolor2" value="7"'.$ccolors[7].'>
              <label for="img27"><img width=30px src="picts/7t.jpg" ></label>
              <input id="img28" type="radio" name="tcolor2" value="8"'.$ccolors[8].'>
              <label for="img28"><img width=30px src="picts/8t.jpg" ></label>
              <input id="img29" type="radio" name="tcolor2" value="9"'.$ccolors[9].'>
              <label for="img29"><img width=30px src="picts/9t.jpg" ></label>
              <input id="img210" type="radio" name="tcolor2" value="10"'.$ccolors[10].'>
              <label for="img210"><img width=30px src="picts/10t.jpg" ></label>
              <input id="img211" type="radio" name="tcolor2" value="11"'.$ccolors[11].'>
              <label for="img211"><img width=30px src="picts/11t.jpg" ></label>
              <input id="img212" type="radio" name="tcolor2" value="12"'.$ccolors[12].'>
              <label for="img212"><img width=30px src="picts/12t.jpg" ></label>
              <input id="img213" type="radio" name="tcolor2" value="0"'.$ccolors[0].'>
              <label for="img213"><img width=30px src="picts/0m.jpg" ></label>';
    return $tcolor;  
}  

function getShirts() {
  $dir = '../../img/t-shirt/';
  $files = array_diff(scandir($dir), array('.', '..'));
  $shirts = [];
  foreach ($files as $file) {
      if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
          $shirts[] = $file;
      }
  }
  return $shirts;
}

$shirts = getShirts();
// var_dump($shirts);

?>
<center>
<h1>Редактирование матча</h1>
<form action="match_update.php" method="GET" onSubmit="return sendForm(this);" ENCTYPE='multipart/form-data'>
<?
if ((!empty($_GET))){
  if (isset($_GET['red'])){
    if (isset($_GET['id'])) $team_id=intval($_GET['id']);
    if (isset($_GET['tur'])) $tur=intval($_GET['tur']);
    if (isset($_GET['cup_stage'])) $cup_stage=intval($_GET['cup_stage']);
    if (isset($_GET['date'])) {$date=($_GET['date']); } ELSE {$date="";}
    if (isset($_GET['field'])) {$field=filter_string($_GET['field']); } ELSE {$field="";}
    if (isset($_GET['team1'])) {$team1=filter_string($_GET['team1']); } ELSE {$team1="";}
    if (isset($_GET['team2'])) {$team2=filter_string($_GET['team2']); } ELSE {$team2="";}
    if (isset($_GET['turnir'])) {$turnir=filter_string($_GET['turnir']); } ELSE {$turnir="";}
    if (isset($_GET['canseled'])) $canseled=intval($_GET['canseled']);
    if (isset($_GET['refery1'])) $refery1=1*intval($_GET['refery1']);
    if (isset($_GET['refery2'])) $refery2=1*intval($_GET['refery2']);
    if (isset($_GET['tcolor1'])) $tcolor1=1*intval($_GET['tcolor1']);
    if (isset($_GET['tcolor2'])) $tcolor2=1*intval($_GET['tcolor2']);
    if (isset($_GET['shirt1'])) {$shirt1=filter_string($_GET['shirt1']); } ELSE {$shirt1="gray-manish.png";}
    if (isset($_GET['shirt2'])) {$shirt2=filter_string($_GET['shirt2']); } ELSE {$shirt2="gray-manish.png";}
    if (isset($_GET['tshirt1'])) {$tshirt1=filter_string($_GET['tshirt1']); } ELSE {$tshirt1="gray-manish.png";}
    if (isset($_GET['tshirt2'])) {$tshirt2=filter_string($_GET['tshirt2']); } ELSE {$tshirt2="gray-manish.png";}
    if (isset($_GET['correction_price_team1'])) $correction_price_team1 = 1*intval($_GET['correction_price_team1']);
    if (isset($_GET['correction_price_team2'])) $correction_price_team2 = 1*intval($_GET['correction_price_team2']);

    $record["date"] = $date;
    $record["field"] = $field;
    $record["team1"] = $team1;
    $record["team2"] = $team2;
    $record["turnir"] = $turnir;
    $record["canseled"] = $canseled;
    $record["tur"] = $tur;
    $record["cup_stage"] = $cup_stage;
    $record["refery1"] = $refery1;
    $record["refery2"] = $refery2;
    $record["tcolor1"] = $tcolor1;
    $record["tcolor2"] = $tcolor2;

    if($_GET['red']){
      $record["tshirt1"] = $shirt1;
      $record["tshirt2"] = $shirt2;
    } else {
      $record["tshirt1"] = $tshirt1;
      $record["tshirt2"] = $tshirt2;
    }
    
    $record["correction_price_team1"] = $correction_price_team1;
    $record["correction_price_team2"] = $correction_price_team2;
	  $record["upd_teams_match_stat"] = 1;

    if (isset($_GET['red'])) {$redatirovat_or_else=intval($_GET['red']);
    } ELSE {$redatirovat_or_else=0;}
    if ($redatirovat_or_else==1)
    {    //$sql = "UPDATE v9ky_match SET field='1' WHERE id = ".$team_id;
	     //$rs = $db->Execute($sql);
    	 $db->AutoExecute('v9ky_match',$record,'UPDATE', 'id = '.$team_id.'');
    }else {
      $db->AutoExecute('v9ky_match',$record,'INSERT');
    }
    	
		
	  $record1["upd_table"] = 1;
    $record1["updatet"] = gmdate('Y-m-d H:i:s');
    $db->AutoExecute('v9ky_turnir',$record1,'UPDATE', 'id = '.$turnir.'');
  }
  $date = "";
  $field = "";
  $team1 = "";
  $team2 = "";
  $turnir = "";
  $canseled = "0";
  $tur = "1";
  $cup_stage = "0";
  $refery1 = "0";
  $refery2 = "0";
  $tcolor1 = "0";
  $tcolor2 = "0";
  $tcolor1 = "gray-manish.png";
  $tcolor2 = "gray-manish.png";
  $correction_price_team1 = "0";
  $correction_price_team2 = "0";

  if ((isset($_GET['id']))&&(intval($_GET['id'])*1>0)&&(($redatirovat_or_else==1)||(!isset($_GET['red']))))
  {
  	 $id_to_update=intval($_GET['id'])*1;
	 $recordSet1 = $db->Execute("select * from v9ky_match where id='".$id_to_update."'");
  }else {
    $recordSet1 = $db->Execute("select * from v9ky_match where id=(SELECT LAST_INSERT_ID())");
    $id_to_update=$recordSet1->fields['id'];
  }
  $record1["updatet"] = gmdate('Y-m-d H:i:s');
  $record1["upd_table"] = 1;
  $record1["upd_teams_match_stat"] = 1;
  $db->AutoExecute('v9ky_turnir',$record1,'UPDATE', 'id = '.$recordSet1->fields[turnir].'');
	$turnir_id = $recordSet1->fields['turnir'];

	 $date = date_create($recordSet1->fields['date']);
	 $field = $recordSet1->fields['field'];
	 $team1 = $recordSet1->fields['team1'];
	 $team2 = $recordSet1->fields['team2'];
	 $turnir = $recordSet1->fields['turnir'];
	 $canseled = $recordSet1->fields['canseled'];
	 $tur = $recordSet1->fields['tur'];
         $cup_stage = $recordSet1->fields['cup_stage'];
         $refery1 = $recordSet1->fields['refery1'];
         $refery2 = $recordSet1->fields['refery2'];
         $tcolor1 = $recordSet1->fields['tcolor1'];
         $tcolor2 = $recordSet1->fields['tcolor2'];
         $tshirt1 = $recordSet1->fields['tshirt1'];
         $tshirt2 = $recordSet1->fields['tshirt2'];
         $correction_price_team1 = $recordSet1->fields['correction_price_team1'];
         $correction_price_team2 = $recordSet1->fields['correction_price_team2'];
    $currentShirt1 = $recordSet1->fields['tshirt1'];
    $currentShirt2 = $recordSet1->fields['tshirt2'];

	 echo"Дата: <input type='datetime-local' name='date' size='60' value='".date_format($date, 'Y-m-d\TH:i:s')."'>  ";
         
         echo"Поле: <select name='field' size=1> ";
	 $recordSet2 = $db->Execute("select * from v9ky_fields where visible=1 and city=(select city from v9ky_turnir where id=".$turnir_id.") ORDER BY name " );
	 while (!$recordSet2->EOF)
	 {
		 if ($recordSet2->fields[id]==$field) print "<option value='".$recordSet2->fields[id]."' selected>".$recordSet2->fields[name]."</option> \n";
		 else print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 $recordSet2->MoveNext();
	 }
	 echo"</select>";

	 echo"№ Тура: <input type='text' name='tur' size='3' value='".$tur."'> ";

         echo"Кубковая ступень: <select name='cup_stage' size=1>";
	 if ($cup_stage==0) $ifselected="selected"; else $ifselected="";
	 print "<option value='0' ".$ifselected.">Не кубок</option> \n";
	 if ($cup_stage==1) $ifselected="selected"; else $ifselected="";
	 print "<option value='1' ".$ifselected.">ФІНАЛ КУБКУ</option> \n";
         if ($cup_stage==2) $ifselected="selected"; else $ifselected="";
	 print "<option value='2' ".$ifselected.">МАТЧ ЗА 3-е МІСЦЕ</option> \n";
         if ($cup_stage==3) $ifselected="selected"; else $ifselected="";
	 print "<option value='3' ".$ifselected.">ПІВФІНАЛ КУБКУ</option> \n";
         if ($cup_stage==4) $ifselected="selected"; else $ifselected="";
	 print "<option value='4' ".$ifselected.">1/4 КУБКУ</option> \n";
         if ($cup_stage==5) $ifselected="selected"; else $ifselected="";
	 print "<option value='5' ".$ifselected.">1/8 КУБКУ</option> \n";
         if ($cup_stage==6) $ifselected="selected"; else $ifselected="";
	 print "<option value='6' ".$ifselected.">1/16 КУБКУ</option> \n";
         if ($cup_stage==7) $ifselected="selected"; else $ifselected="";
	 print "<option value='7' ".$ifselected.">1/32 КУБКУ</option> \n";
         if ($cup_stage==8) $ifselected="selected"; else $ifselected="";
	 print "<option value='8' ".$ifselected.">1/64 КУБКУ</option> \n";
         if ($cup_stage==10) $ifselected="selected"; else $ifselected="";
	 print "<option value='10' ".$ifselected.">Золотий матч</option> \n
	  </select><br><br>";

         echo"Турнир: <select name='turnir' size=1 id='city' onchange='";
         echo'getTown(this.value, {content_type:"json", target:"town", preloader:"prl"});
           getTown(this.value, {content_type:"json", target:"town2", preloader:"prl"})';
         echo "'>";
	 $recordSet3 = $db->Execute("select * from v9ky_turnir where active>0");
	 while (!$recordSet3->EOF)
	 {
		 if ($recordSet3->fields[id]==$turnir) print "<option value='{$recordSet3->fields[id]}' selected>".$recordSet3->fields[name]."  ".$recordSet3->fields[season]."</option> \n";
		 else print "<option value='{$recordSet3->fields[id]}'>".$recordSet3->fields[name]."  ".$recordSet3->fields[season]."</option> \n";
		 $recordSet3->MoveNext();
	 }
	 echo"</select>";
         echo'<br><img id="prl" src="../design/spinner.gif" style="visibility:hidden;">';

         $recordSetmcolor1 = $db->Execute("select tcolor from v9ky_team where id=$team1");
         $recordSetmcolor2 = $db->Execute("select tcolor from v9ky_team where id=$team2");


	echo" <span id='tshirt1' ><img width=30px src='/img/t-shirt/".$currentShirt1."' ></span>";
  echo '<input type="hidden" name="tshirt1" id="tshirt1_input">';

  echo"<span style='visibility: hidden;' id='color1' ><img width=30px src='".$colors[$recordSetmcolor1->fields[tcolor]]."' ></span>";
  
  echo"Команда1: <select id='town' name='team1' size=1 onchange='";
  echo'getColor(this.value, {content_type:"html", target:"color1", preloader:"prl"});
              getmColor(this.value, {content_type:"html", target:"mcolor1", preloader:"prl"});
              getTshirtTeam(this.value, {content_type:"html", target:"tshirt1", preloader:"prl"});setTimeout(function() {
  syncTshirtToInput("tshirt1", "tshirt1_input");
}, 300);';
              
  echo "'>";

	 $recordSet2 = $db->Execute("select * from v9ky_team where turnir='".$turnir_id."' ORDER BY name " );
	 while (!$recordSet2->EOF)
	 {
		 if ($recordSet2->fields[id]==$team1) print "<option value='".$recordSet2->fields[id]."' selected>".$recordSet2->fields[name]."</option> \n";
		 else print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 $recordSet2->MoveNext();
	 }
         if (0==$team1) print "<option value=0 selected>Команда 1</option> \n";
         else print "<option value=0>Команда 1</option> \n";
	 echo"</select>";

	 echo"   Команда2: <select id='town2' name='team2' size=1 onchange='";
         echo'getColor(this.value, {content_type:"html", target:"color2", preloader:"prl"});
              getmColor2(this.value, {content_type:"html", target:"mcolor2", preloader:"prl"});
              getTshirtTeam(this.value, {content_type:"html", target:"tshirt2", preloader:"prl"});setTimeout(function() {
  syncTshirtToInput("tshirt2", "tshirt2_input");
}, 300);';
         echo "'>";
	 $recordSet2 = $db->Execute("select * from v9ky_team where turnir='".$turnir_id."' ORDER BY name ");
	 while (!$recordSet2->EOF)
	 {
		 if ($recordSet2->fields[id]==$team2) print "<option value='".$recordSet2->fields[id]."' selected>".$recordSet2->fields[name]."</option> \n";
		 else print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 $recordSet2->MoveNext();
	 }
         if (0==$team2) print "<option value=0 selected>Команда 2</option> \n";
         else print "<option value=0>Команда 2</option> \n";
	 echo"</select><span  style='visibility: hidden;' id='color2' ><img width=30px src='".$colors[$recordSetmcolor2->fields[tcolor]]."' ></span>";
	 echo"<span id='tshirt2' ><img width=30px src='/img/t-shirt/".$currentShirt2."' ></span><br><br>";
   echo '<input type="hidden" name="tshirt2" id="tshirt2_input">';

         for ($i = 0; $i <= 12; $i++) {
           $ccolors[$i]="";
           if($recordSet1->fields['tcolor1']==$i) $ccolors[$i]=" checked";
         }
         echo'<table  style="visibility: hidden;" border="1"><tr><td id="mcolor1"><input id="img1" type="radio" name="tcolor1" value="1"'.$ccolors[1].'>
              <label for="img1"><img width=30px src="picts/1t.jpg" ></label>
              <input id="img2" type="radio" name="tcolor1" value="2"'.$ccolors[2].'>
              <label for="img2"><img width=30px src="picts/2t.jpg" ></label>
              <input id="img3" type="radio" name="tcolor1" value="3"'.$ccolors[3].'>
              <label for="img3"><img width=30px src="picts/3t.jpg" ></label>
              <input id="img4" type="radio" name="tcolor1" value="4"'.$ccolors[4].'>
              <label for="img4"><img width=30px src="picts/4t.jpg" ></label>
              <input id="img5" type="radio" name="tcolor1" value="5"'.$ccolors[5].'>
              <label for="img5"><img width=30px src="picts/5t.jpg" ></label>
              <input id="img6" type="radio" name="tcolor1" value="6"'.$ccolors[6].'>
              <label for="img6"><img width=30px src="picts/6t.jpg" ></label>
              <input id="img7" type="radio" name="tcolor1" value="7"'.$ccolors[7].'>
              <label for="img7"><img width=30px src="picts/7t.jpg" ></label>
              <input id="img8" type="radio" name="tcolor1" value="8"'.$ccolors[8].'>
              <label for="img8"><img width=30px src="picts/8t.jpg" ></label>
              <input id="img9" type="radio" name="tcolor1" value="9"'.$ccolors[9].'>
              <label for="img9"><img width=30px src="picts/9t.jpg" ></label>
              <input id="img10" type="radio" name="tcolor1" value="10"'.$ccolors[10].'>
              <label for="img10"><img width=30px src="picts/10t.jpg" ></label>
              <input id="img11" type="radio" name="tcolor1" value="11"'.$ccolors[11].'>
              <label for="img11"><img width=30px src="picts/11t.jpg" ></label>
              <input id="img12" type="radio" name="tcolor1" value="12"'.$ccolors[12].'>
              <label for="img12"><img width=30px src="picts/12t.jpg" ></label>
              <input id="img13" type="radio" name="tcolor1" value="0"'.$ccolors[0].'>
              <label for="img13"><img width=30px src="picts/0m.jpg" ></label>
              
              </td>';

         for ($i = 0; $i <= 12; $i++) {
           $ccolors[$i]="";
           if($recordSet1->fields['tcolor2']==$i) $ccolors[$i]=" checked";
         }
         echo'<td   style="visibility: hidden;" id="mcolor2"><input id="img21" type="radio" name="tcolor2" value="1"'.$ccolors[1].'>
              <label for="img21"><img width=30px src="picts/1t.jpg" ></label>
              <input id="img22" type="radio" name="tcolor2" value="2"'.$ccolors[2].'>
              <label for="img22"><img width=30px src="picts/2t.jpg" ></label>
              <input id="img23" type="radio" name="tcolor2" value="3"'.$ccolors[3].'>
              <label for="img23"><img width=30px src="picts/3t.jpg" ></label>
              <input id="img24" type="radio" name="tcolor2" value="4"'.$ccolors[4].'>
              <label for="img24"><img width=30px src="picts/4t.jpg" ></label>
              <input id="img25" type="radio" name="tcolor2" value="5"'.$ccolors[5].'>
              <label for="img25"><img width=30px src="picts/5t.jpg" ></label>
              <input id="img26" type="radio" name="tcolor2" value="6"'.$ccolors[6].'>
              <label for="img26"><img width=30px src="picts/6t.jpg" ></label>
              <input id="img27" type="radio" name="tcolor2" value="7"'.$ccolors[7].'>
              <label for="img27"><img width=30px src="picts/7t.jpg" ></label>
              <input id="img28" type="radio" name="tcolor2" value="8"'.$ccolors[8].'>
              <label for="img28"><img width=30px src="picts/8t.jpg" ></label>
              <input id="img29" type="radio" name="tcolor2" value="9"'.$ccolors[9].'>
              <label for="img29"><img width=30px src="picts/9t.jpg" ></label>
              <input id="img210" type="radio" name="tcolor2" value="10"'.$ccolors[10].'>
              <label for="img210"><img width=30px src="picts/10t.jpg" ></label>
              <input id="img211" type="radio" name="tcolor2" value="11"'.$ccolors[11].'>
              <label for="img211"><img width=30px src="picts/11t.jpg" ></label>
              <input id="img212" type="radio" name="tcolor2" value="12"'.$ccolors[12].'>
              <label for="img212"><img width=30px src="picts/12t.jpg" ></label>
              <input id="img213" type="radio" name="tcolor2" value="0"'.$ccolors[0].'>
              <label for="img213"><img width=30px src="picts/0m.jpg" ></label>
              
              </td></tr><br>';

	  

         echo"Корекція ціни за матч для команди1: <input type='text' name='correction_price_team1' size='4' value='".$correction_price_team1."'> команди2: <input type='text' name='correction_price_team2' size='4' value='".$correction_price_team2."'> <br>";
	 echo"<br>Состояние: <select name='canseled' size=1>";
	 if ($canseled==0) $ifselected="selected"; else $ifselected="";
	 print "<option value='0' ".$ifselected.">Активен</option> \n";
	 if ($canseled==1) $ifselected="selected"; else $ifselected="";
	 print "<option value='1' ".$ifselected.">Завершен</option> \n";
         if ($canseled==2) $ifselected="selected"; else $ifselected="";
	 print "<option value='2' ".$ifselected.">Live</option> \n";
	 echo "</select>";

     echo"Арбитр1: <select name='refery1' size=1>";
     if (0==$refery1) print "<option value='0' selected>-</option> \n";
		 else print "<option value='0'>-</option> \n";
     $recordrefery = $db->Execute("select * from v9ky_refery where active>0");
     while (!$recordrefery->EOF)
       {
	 if ($recordrefery->fields[id]==$refery1) print "<option value='{$recordrefery->fields[id]}' selected>".$recordrefery->fields[name1]."  ".$recordrefery->fields[name2]."</option> \n";
		 else print "<option value='{$recordrefery->fields[id]}'>".$recordrefery->fields[name1]."  ".$recordrefery->fields[name2]."</option> \n";
		 $recordrefery->MoveNext();
       }
     echo"</select>";

     echo"Арбитр2: <select name='refery2' size=1>";
     if (0==$refery2) print "<option value='0' selected>-</option> \n";
		 else print "<option value='0'>-</option> \n";
     $recordrefery = $db->Execute("select * from v9ky_refery where active>0");
     while (!$recordrefery->EOF)
       {
	 if ($recordrefery->fields[id]==$refery2) print "<option value='{$recordrefery->fields[id]}' selected>".$recordrefery->fields[name1]."  ".$recordrefery->fields[name2]."</option> \n";
		 else print "<option value='{$recordrefery->fields[id]}'>".$recordrefery->fields[name1]."  ".$recordrefery->fields[name2]."</option> \n";
		 $recordrefery->MoveNext();
       }
     echo"</select>";


     echo"<br> <br><input type='submit' value='  Изменить  '><input type='radio' name='red' value='1' checked>
	   Внести изменения в матч ".$recordSet1->fields['date']."<input type='radio' name='red' value='0'>
	   Добавить как новую";
	 echo "<input type='hidden' name='id' value='".$id_to_update."'>";

   echo"<br> <br>";
    
    
      include_once('select_tshirt_match.tpl.php');

	 echo "</form> ";

  if (isset($_GET['red'])){ 
    echo "Матч: <H2> ".$recordSet1->fields['date']." </H2> изменения приняты";
  }

  

}else {
   echo"Дата: <input type='datetime-local' name='date' size='60' >  ";
   echo"Поле: <select name='field' size=1> ";
	 $recordSet2 = $db->Execute("select * from v9ky_fields where visible=1 ORDER BY name " );
	 while (!$recordSet2->EOF)
	 {
	   print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
	   $recordSet2->MoveNext();
	 }
	 echo"</select>";
   echo"№ Тура: <input type='text' name='tur' size='3' > ";

   echo"Кубковая ступень: <select name='cup_stage' size=1>
         <option value='0' selected>Не кубок</option> \n
	 <option value='1' >ФІНАЛ КУБКУ</option> \n
         <option value='2' >МАТЧ ЗА 3-е МІСЦЕ</option> \n
         <option value='3' >ПІВФІНАЛ КУБКУ</option> \n
         <option value='4' >1/4 КУБКУ</option> \n
         <option value='5' >1/8 КУБКУ</option> \n
         <option value='6' >1/16 КУБКУ</option> \n
         <option value='7' >1/32 КУБКУ</option> \n
         <option value='8' >1/64 КУБКУ</option> \n
         <option value='10' >Золотий матч</option> \n
	 </select><br><br>";

   echo"Турнир: <select name='turnir' size=1 id='city' onchange='";
         echo'getTown(this.value, {content_type:"json", target:"town", preloader:"prl"});
           getTown(this.value, {content_type:"json", target:"town2", preloader:"prl"})';
         echo "'>";
	 $recordSet3 = $db->Execute("select * from v9ky_turnir where active>0");
	 while (!$recordSet3->EOF)
	 {
		 print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[name]."  ".$recordSet3->fields[season]."</option> \n";
		 $recordSet3->MoveNext();
	 }
	 echo"</select>";
   echo'<br><img id="prl" src="../design/spinner.gif" style="visibility:hidden;">';

   echo"Команда1: <select id='town' name='team1' size=1> ";
         print "<option value=0>Команда 1</option> \n";
	 $recordSet2 = $db->Execute("select * from v9ky_team ORDER BY grupa, turnir, name");
	 while (!$recordSet2->EOF)
	 {
		 print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 $recordSet2->MoveNext();
	 }
	 echo"</select>";

	 echo"Команда2: <select id='town2' name='team2' size=1> ";
         print "<option value=0>Команда 2</option> \n";
	 $recordSet2 = $db->Execute("select * from v9ky_team ORDER BY grupa, turnir, name");
	 while (!$recordSet2->EOF)
	 {
		 print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 $recordSet2->MoveNext();
	 }
	 echo"</select><br><br>";

	 

	 echo"Состояние: <select name='canseled' size=1>";
	 print "<option value='0' >Активен</option> \n";
	 print "<option value='1' >Завершен</option> \n";
         print "<option value='2' >Live</option> \n";
 	 echo  "</select>";

   echo"Арбитр1: <select name='refery1' size=1>";
   print "<option value='0'>-</option> \n";
   $recordrefery = $db->Execute("select * from v9ky_refery where active>0");
     while (!$recordrefery->EOF)
       {
	 print "<option value='{$recordrefery->fields[id]}'>".$recordrefery->fields[name1]."  ".$recordrefery->fields[name2]."</option> \n";
	   $recordrefery->MoveNext();
       }
     echo"</select>";
   echo"Арбитр2: <select name='refery2' size=1>";
   print "<option value='0'>-</option> \n";
   $recordrefery = $db->Execute("select * from v9ky_refery where active>0");
     while (!$recordrefery->EOF)
       {
	 print "<option value='{$recordrefery->fields[id]}'>".$recordrefery->fields[name1]."  ".$recordrefery->fields[name2]."</option> \n";
	   $recordrefery->MoveNext();
       }
     echo"</select>";
   echo"<input type='hidden' name='red' value='0'><br><br>";
   echo"<br> <br><input type='submit' value='  Создать матч  '></form> ";

}

?>
<script>
document.querySelectorAll('.shirt-select').forEach(select => {
    const inputId = select.dataset.inputId;
    const input = document.getElementById(inputId);
    const preview = select.querySelector('.shirt-preview');
    const options = select.querySelectorAll('.shirt-option');

    select.addEventListener('click', () => {
        select.classList.toggle('open');
    });

    options.forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation(); // чтобы не сработал .toggle('open') снова
            const value = option.dataset.value;
            input.value = value;
            preview.src = '/img/t-shirt/' + value;
            select.classList.remove('open');
        });
    });
});

// закрытие при клике вне селекта
document.addEventListener('click', function(event) {
    document.querySelectorAll('.shirt-select.open').forEach(openSelect => {
        if (!openSelect.contains(event.target)) {
            openSelect.classList.remove('open');
        }
    });
});

// Получаем значение src, вытаскиваем имя файла и кладем в hidden input
function syncTshirtToInput(imgId, inputId) {
  const img = document.querySelector(`#${imgId} img`);
  const input = document.getElementById(inputId);
  if (img && input) {
    const src = img.getAttribute("src");
    const filename = src.split("/").pop(); // Получаем только имя файла
    input.value = filename;
  }
}

</script>



</center>
</body>
</html>