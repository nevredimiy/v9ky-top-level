<? 
  include_once "turnir_head.php";
?>
		<div class="content">
			
<?
$cachefile = 'jeka_cashe/table/cached-'.$tournament.'.html';
$cachetime = 900;
// Обслуживается из файла кеша, если время запроса меньше $cachetime
//$golupdate = $db->Execute("select CONVERT_TZ( updatet, @@session.time_zone, '+00:00' ) as updatet1 ,active from v9ky_turnir where id='".$turnir."'");
//echo "<!-- Cached table copy, generated ".date('H:i', filemtime($cachefile))." -->\n";

//if ((file_exists($cachefile) && ((strtotime($golupdate->fields[updatet1]) < (filemtime($cachefile) + 4000))))) {
$golupdate = $db->Execute("select upd_table as updatet1 from v9ky_turnir where id=".$turnir."");

if (file_exists($cachefile) && ($golupdate->fields[updatet1] == 0)) {
	
    include($cachefile);
} else {
	$recordt["upd_table"] = 0;
    $db->AutoExecute('v9ky_turnir',$recordt,'UPDATE', 'id = '.$turnir.'');
	ob_start(); // Запуск буфера вывода
  $record_pagestat["ip"] = $_SERVER['REMOTE_ADDR'];
  //$record_pagestat["ip_forwarded"] = $_SERVER['HTTP_X_FORWARDED_FOR'];
  $record_pagestat["agent"] = $_SERVER['HTTP_USER_AGENT'];
  $record_pagestat["page"] = "table";
  $record_pagestat["test"] = $tournament;
  $db->AutoExecute('page_stat',$record_pagestat,'INSERT');
  
$cupgroup = $db->Execute("select cup_stage from v9ky_match where turnir='".$turnir."' and cup_stage>0 group by cup_stage");
if (!$cupgroup){
	$recorde["page"] = "table";
	$recorde["text"] = $db->ErrorMsg();
    $db->AutoExecute('v9ky_errors',$recorde,'INSERT');}
else
while (!$cupgroup->EOF) {
$cups1 = $db->Execute("select cup_stage, id, canseled from v9ky_match where turnir='".$turnir."' and cup_stage='".$cupgroup->fields[cup_stage]."'");
echo '<div class="panel panel-default">
  <div class="panel-heading"><strong>';
switch ($cupgroup->fields[cup_stage]){
  case 1: echo "Фінал";break;
  case 2: echo "Матч за 3-е місце";break;
  case 3: echo "Півфінал";break;
  case 4: echo "1/4 КУБКУ";break;
  case 5: echo "1/8 КУБКУ";break;
  case 6: echo "1/16 КУБКУ";break;
  case 7: echo "1/32 КУБКУ";break;
  case 8: echo "1/64 КУБКУ";break;
  case 10: echo "Золотий матч";break;
}
echo '</strong></div>
  <div class="panel-body">';


while (!$cups1->EOF) {
if ($cups1->fields[cup_stage]>0){

  $cups1match = $db->Execute("select * from v9ky_match where id='".$cups1->fields[id]."'");

  $recwins1 = $db->Execute("select count(*) as kol from v9ky_gol
	      where team='".$cups1match->fields[team1]."' and matc='".$cups1->fields[id]."' ");
  $recwins2 = $db->Execute("select count(*) as kol from v9ky_gol
	      where team='".$cups1match->fields[team2]."' and matc='".$cups1->fields[id]."' ");

  if ($cups1match->fields[team1]==0) $cups1team1 = "Команда 1";
  else  {$cups1team = $db->Execute("select * from v9ky_team where id='".$cups1match->fields[team1]."'"); $cups1team1 = $cups1team->fields[name];}
  if ($cups1match->fields[team2]==0) $cups1team2 = "Команда 2";
  else  {$cups1team = $db->Execute("select * from v9ky_team where id='".$cups1match->fields[team2]."'"); $cups1team2 = $cups1team->fields[name];}

  if ($cupgroup->fields[cup_stage]==1) {
    $img1 = "<img src='/img/goldcup.png' alt='Золотой кубок по мини футболу' height=36 />";
    $img2 = "<img src='/img/silvercup.png' alt='Серебряный кубок по мини футболу' height=30 />";}
  if ($cupgroup->fields[cup_stage]==10) {
    $img1 = "<img src='/img/goldcup.png' alt='Золотой кубок по мини футболу' height=36 />";
    $img2 = "<img src='/img/silvercup.png' alt='Серебряный кубок по мини футболу' height=30 />";}
  if ($cupgroup->fields[cup_stage]==2) $img3 = "<img src='/img/bronzecup.png' alt='Золотой кубок по мини футболу' height=25 />";

  echo "<center>";
  switch ($cupgroup->fields[cup_stage]){
    case 1: if ($recwins1->fields[kol]>$recwins2->fields[kol]) echo $img1; 
            if ($recwins1->fields[kol]<$recwins2->fields[kol]) echo $img2; 
      break;
    case 10: if ($recwins1->fields[kol]>$recwins2->fields[kol]) echo $img1; 
            if ($recwins1->fields[kol]<$recwins2->fields[kol]) echo $img2; 
      break;
    case 2: if ($recwins1->fields[kol]>$recwins2->fields[kol]) echo $img3; break;
  }
  echo "&nbsp;".$cups1team1."&nbsp;";
  if ($cups1->fields[canseled]==0) {echo "-";} else {echo ($recwins1->fields[kol] *1);}
  echo ":";
  if ($cups1->fields[canseled]==0) {echo "-";} else {echo ($recwins2->fields[kol] *1);}
  echo "&nbsp;".$cups1team2."&nbsp;";
  switch ($cupgroup->fields[cup_stage]){
    case 1: if ($recwins1->fields[kol]<$recwins2->fields[kol]) echo $img1; 
            if ($recwins1->fields[kol]>$recwins2->fields[kol]) echo $img2; 
      break;
    case 10: if ($recwins1->fields[kol]<$recwins2->fields[kol]) echo $img1; 
            if ($recwins1->fields[kol]>$recwins2->fields[kol]) echo $img2; 
      break;
    case 2: if ($recwins1->fields[kol]<$recwins2->fields[kol]) echo $img3; break;
  }
  echo "</center>";
}
$cups1->MoveNext();
}
echo "</div></div>";
$cupgroup->MoveNext();
}


$rec_grups = $db->Execute("select grupa from v9ky_team where turnir='".$turnir."' group by grupa");

?>

<? while (!$rec_grups->EOF) {
   $rec_ru = $db->Execute("select ru from v9ky_turnir where id='".$turnir."'");
	if ($rec_grups->fields[grupa]!=='') {$grupa = 'Група '.$rec_grups->fields[grupa].' ';}
	else $grupa = '';
	?>


<div class="content-turnament">
				<p class="title">ТУРНІРНА ТАБЛИЦЯ <?=$grupa;?></p>
				<div class="tabl_turnir">
				<table class="tabl_turnament">
				  <tr class="tabl_turn_head">


	
					<td>М</td>
					<td>Команда</th>
					<td>I</td>
					<td>В</td>
					<td>Н</td>
					<td>П</td>
					<td>Г</td>
					<td>О</td>
                              </tr>
                            
<?

$recordSet = $db->Execute("select * from v9ky_team where turnir=".$turnir." and grupa='".$rec_grups->fields[grupa]."'");

$i=0;
$db->Execute("TRUNCATE TABLE v9ky_table"); //очистка турнирной таблицы
while (!$recordSet->EOF) {
	$i=$i+1;
	$recordgames = $db->Execute("select count(*) as kol from v9ky_match where (canseled='1')and (cup_stage=0)
	  and (team1='".$recordSet->fields[id]."' or team2='".$recordSet->fields[id]."')");
	$recordgols = $db->Execute("select count(*) as kol from v9ky_gol a, v9ky_match b where a.team='".$recordSet->fields[id]."' and a.matc=b.id
	and (b.canseled='1') and (b.cup_stage=0)");
	$recordgols1 = $db->Execute("select count(*) as kol from v9ky_gol a, v9ky_match b
	  where not a.team='".$recordSet->fields[id]."' and (b.team1='".$recordSet->fields[id]."' or b.team2='".$recordSet->fields[id]."') and
	  a.matc=b.id and (b.canseled='1') and (b.cup_stage=0)");
	$recordwins = $db->Execute("select id, team1, team2 from v9ky_match
	  where (team1='".$recordSet->fields[id]."' or team2='".$recordSet->fields[id]."') and (canseled='1') and (cup_stage=0) ");
	$wins=0; $nichya=0; $loss=0; $ochki=0;
	if ($recordwins){
	  while (!$recordwins->EOF) {
	   $recordwins1 = $db->Execute("select count(*) as kol from v9ky_gol
	      where team='".$recordSet->fields[id]."' and matc='".$recordwins->fields[id]."' ");
	   $recordwins2 = $db->Execute("select count(*) as kol from v9ky_gol
	      where not team='".$recordSet->fields[id]."' and matc='".$recordwins->fields[id]."' ");
	   $recordwins->MoveNext();
	   if ($recordwins1->fields[kol]>$recordwins2->fields[kol]) {$wins=$wins+1; $ochki=$ochki+3;}
	   if ($recordwins1->fields[kol]==$recordwins2->fields[kol]) {$nichya=$nichya+1; $ochki=$ochki+1;}
	   if ($recordwins1->fields[kol]<$recordwins2->fields[kol]) $loss=$loss+1;
	  }}
	/*    echo"<tr><td class='bakc_grei_grdient'>".$i."</td>";
	echo'<td><img src="'.$team_logo_path.$recordSet->fields[pict].'" alt="" height=30 />';
	echo"".$recordSet->fields[name]."</td>";
        echo"<td>".$recordgames->fields[kol]."</td>";
	echo"<td>".$wins."</td>";
	echo"<td>".$nichya."</td>";
	echo"<td>".$loss."</td>";
	$kol=$recordgols1->fields[kol];
	echo"<td>".$recordgols->fields[kol]."-".$kol."</td>";
	echo"<td>".$ochki."</td></tr>";*/

	$record["logo"] = $team_logo_path.$recordSet->fields[pict];
    $record["team_id"] = $recordSet->fields[id];
	$record["team_name"] = $recordSet->fields[name];
	$record["games"] = $recordgames->fields[kol];
	$record["wins"] = $wins;
	$record["nichiya"] = $nichya;
	$record["loses"] = $loss;
	$record["gols"] = $recordgols->fields[kol];
	$record["gols1"] = $recordgols1->fields[kol];
	$record["diference"] = $recordgols->fields[kol] - $recordgols1->fields[kol];
	$record["score"] = $ochki;
	$record["turnir"] = $turnir;
	$record["grupa"] = $rec_grups->fields[grupa];

	$db->AutoExecute('v9ky_table',$record,'INSERT');

	if (($i>1)&&($ochki_old == $ochki)&&($ochki>0)){

           




	   $match_lichki = $db->Execute("select * from v9ky_match where team1='".$team_id_old."' and team2='".$record['team_id']."'  and (cup_stage=0)");



	   $omatch = new match(); //класс матча

	   if (!$match_lichki){


		$match_lichki = $db->Execute("select * from v9ky_match where team2='".$team_id_old."' and team1='".$record['team_id']."'  and (cup_stage=0)");

		   if ($match_lichki){

			   $goly1 = $omatch->Gol1($db, $match_lichki->fields[id]);
			   $goly2 = $omatch->Gol2($db, $match_lichki->fields[id]);
                           
			   if ($goly1>$goly2) {$record1["lichki"] = 1; $db->AutoExecute('v9ky_table',$record1,'UPDATE', 'team_id = '.$record['team_id'].'');}
			   else if ($goly1<$goly2) {$record1["lichki"] = 1; $db->AutoExecute('v9ky_table',$record1,'UPDATE', 'team_id = '.$team_id_old.'');}
		   }
	   }else{

		   $goly1 = $omatch->Gol1($db, $match_lichki->fields[id]);
	        $goly2 = $omatch->Gol2($db, $match_lichki->fields[id]);

		    if ($goly1>$goly2) {$record1["lichki"] = 1; $db->AutoExecute('v9ky_table',$record1,'UPDATE', 'team_id = '.$team_id_old.'');
		   				  }else if ($goly1<$goly2) {$record1["lichki"] = 1; $db->AutoExecute('v9ky_table',$record1,'UPDATE', 'team_id = '.$record['team_id'].'');}

	   }




		$record1["lichki"]=0;
	}

	$team_id_old = $record["team_id"];
	$ochki_old = $ochki;

	$recordSet->MoveNext();
}


$recordfindlichki = $db->Execute("select score, count(*) as kol from v9ky_table where turnir='".$turnir."' and grupa='".$rec_grups->fields[grupa]."' and score>0 GROUP BY score");


while (!$recordfindlichki->EOF) {

	if ($recordfindlichki->fields[kol]>1){
       $recordlichki = $db->Execute("select * from v9ky_table where score='".$recordfindlichki->fields[score]."' ");
	   $db->Execute("TRUNCATE TABLE v9ky_table_lichki"); //очистка турнирной таблицы где лички
       while (!$recordlichki->EOF) {






		   $recordgames = $db->Execute("select count(*) as kol from v9ky_match where (canseled='1') and (cup_stage=0)
		     and ((team1='".$recordlichki->fields[team_id]."') and (team2 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."'))) or
		    (team2='".$recordlichki->fields[team_id]."' and (team1 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."'))) ");
	       $recordgols = $db->Execute("select count(*) as kol from v9ky_gol a, v9ky_match b where a.team='".$recordlichki->fields[team_id]."' and a.matc=b.id and
		     (b.team1 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."') and b.team2 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."'))
		     and (b.canseled='1') and (b.cup_stage=0)");
		   $recordgols1 = $db->Execute("select count(*) as kol from v9ky_gol a, v9ky_match b
	         where not a.team='".$recordlichki->fields[team_id]."' and (b.team1='".$recordlichki->fields[team_id]."' or b.team2='".$recordlichki->fields[team_id]."') and
			 (b.team1 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."')
			 and b.team2 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."')) and
	         a.matc=b.id and (b.canseled='1') and (b.cup_stage=0)");
           $recordwins = $db->Execute("select id, team1, team2 from v9ky_match
	         where (team1='".$recordlichki->fields[team_id]."' or team2='".$recordlichki->fields[team_id]."')and
			 (team1 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."')
			 and team2 in (select team_id from v9ky_table where score='".$recordfindlichki->fields[score]."'))
			 and (canseled='1') and (cup_stage=0)");
           $wins=0; $nichya=0; $loss=0; $ochki=0;
	       if ($recordwins){
        	  while (!$recordwins->EOF) {
	            $recordwins1 = $db->Execute("select count(*) as kol from v9ky_gol
	               where team='".$recordlichki->fields[team_id]."' and matc='".$recordwins->fields[id]."' ");
	            $recordwins2 = $db->Execute("select count(*) as kol from v9ky_gol
	               where not team='".$recordlichki->fields[team_id]."' and matc='".$recordwins->fields[id]."' ");
	            $recordwins->MoveNext();
	            if ($recordwins1->fields[kol]>$recordwins2->fields[kol]) {$wins=$wins+1; $ochki=$ochki+3;}
	            if ($recordwins1->fields[kol]==$recordwins2->fields[kol]) {$nichya=$nichya+1; $ochki=$ochki+1;}
	            if ($recordwins1->fields[kol]<$recordwins2->fields[kol]) $loss=$loss+1;
	       }}


            $lichki["team_id"] = $recordlichki->fields[team_id];
            $recordreds = $db->Execute("select count(*) as kol from v9ky_red where team = ".$recordlichki->fields[team_id]." ");
            $recordyellows = $db->Execute("select count(*) as kol from v9ky_yellow where team = ".$recordlichki->fields[team_id]." ");
            $lichki["games"] = $recordgames->fields[kol];
	        $lichki["wins"] = $wins;
	        $lichki["nichiya"] = $nichya;
	        $lichki["loses"] = $loss;
	        $lichki["gols"] = $recordgols->fields[kol];
	        $lichki["gols1"] = $recordgols1->fields[kol];
	        $lichki["diference"] = $recordgols->fields[kol] - $recordgols1->fields[kol];
	        $lichki["score"] = $ochki;
                $lichki["reds"] = $recordreds->fields[kol];
                $lichki["yellows"] = $recordyellows->fields[kol];


	        $db->AutoExecute('v9ky_table_lichki',$lichki,'INSERT');
		    $recordSetlichki = $db->Execute("select u.team_id as team_id from v9ky_table_lichki u INNER JOIN v9ky_table d ON u.team_id = d.team_id ORDER BY u.score ASC, u.diference ASC, u.gols ASC, d.diference asc, d.gols aSC, u.reds desc, u.yellows desc, d.team_name");
		    $i=0;
            while (!$recordSetlichki->EOF) {
              $i=$i+1;
              $record2["lichki"] = $i;

	      $db->AutoExecute('v9ky_table',$record2,'UPDATE', 'team_id = '.$recordSetlichki->fields[team_id].'');
              $recordSetlichki->MoveNext();
	    }


          $recordlichki->MoveNext();
       }

	}

   $recordfindlichki->MoveNext();
}


$recordSet = $db->Execute("select * from v9ky_table where turnir='".$turnir."' and grupa='".$rec_grups->fields[grupa]."' ORDER BY score DESC, lichki desc, diference DESC, gols DESC, team_name");
$db->Execute("delete from v9ky_head_tables_table where turnir='".$turnir."'");
$i=0;
while (!$recordSet->EOF) {

    $i=$i+1;
    
      $head_tables["stage"] = $i;
      $head_tables["turnir"] = $turnir;
      $head_tables["score"] = $recordSet->fields[score];
      $head_tables["team"] = $recordSet->fields[team_id];
      $head_tables["games"] = $recordSet->fields[games];
      $head_tables["wins"] = $recordSet->fields[wins];
      $head_tables["nichiya"] = $recordSet->fields[nichiya];
      $head_tables["loses"] = $recordSet->fields[loses];
      $head_tables["gols"] = $recordSet->fields[gols];
      $head_tables["gols1"] = $recordSet->fields[gols1];
      
      $db->AutoExecute('v9ky_head_tables_table',$head_tables,'INSERT');
    

    echo"<tr><td class='bakc_grei_grdient'>".$i."</td>";
	echo'<td><a href="'.$url.'/team/id/'.$recordSet->fields[team_id].'" ><img src="'.$recordSet->fields[logo].'" alt="" height=30 /></a>';
	if (($i==101)&&($rec_grups->fields[grupa]=="")) echo'<td><a href="'.$url.'/team/id/'.$recordSet->fields[team_id].'" >
	<img src="/img/goldcup.png" alt="Золотий кубок з міні футболу" height=30 /><img src="/img/1.png" height=30 /></a>'.$recordSet->fields[team_name].'';
	else if (($i==102)&&($rec_grups->fields[grupa]=="")) echo'<td><center><a href="'.$url.'/team/id/'.$recordSet->fields[team_id].'">
	<img src="/img/silvercup.png" alt="Срібний кубок з міні футболу" height=25 /><img src="/img/2.png" height=30 /></a>'.$recordSet->fields[team_name].'';
    else if (($i==103)&&($rec_grups->fields[grupa]=="")) echo'<td><center><a href="'.$url.'/team/id/'.$recordSet->fields[team_id].'" >
	<img src="/img/bronzecup.png" alt="Бронзовий кубок з міні футболу" height=20 /><img src="/img/3.png" height=30 /></a>'.$recordSet->fields[team_name].'';
		else echo''.$recordSet->fields[team_name].'</td>';

    echo"<td>".$recordSet->fields[games]."</td>";
	echo"<td>".$recordSet->fields[wins]."</td>";
	echo"<td>".$recordSet->fields[nichiya]."</td>";
	echo"<td>".$recordSet->fields[loses]."</td>";

	echo"<td>".$recordSet->fields[gols]."-".$recordSet->fields[gols1]."</td>";
	echo"<td>".$recordSet->fields[score]."</td></tr>";
   $recordSet->MoveNext();
}


?>
                        
                    </table></div>
		

<?
   $rec_grups->MoveNext();
}

$time = microtime(true) - $start;
error_log($_SERVER['REQUEST_URI']." ->Gen in ".$time."sec");

// Кешируем содержание в файл
$cached = fopen($cachefile, 'w');
fwrite($cached, ob_get_contents());
fclose($cached);
ob_end_flush(); // Отправялем вывод в браузер
}

?>









		</div>
	</div>
</article>
<?
  include_once "footer.php";
?>