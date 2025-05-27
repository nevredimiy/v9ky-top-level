<? 
  include_once "turnir_head.php";

  $cachefile = 'jeka_cashe/violators/cached-'.$tournament.'.html';
$cachetime = 900;
// Обслуживается из файла кеша, если время запроса меньше $cachetime
$golupdate = $db->Execute("select CONVERT_TZ( updatet, @@session.time_zone, '+00:00' ) as updatet1, active from v9ky_turnir where id='".$turnir."'");

//if (file_exists($cachefile) && ((strtotime($golupdate->fields[updatet]) < (filemtime($cachefile)+date("Z", filemtime($cachefile))+1800)))) {
//    include($cachefile);
if ((file_exists($cachefile) && ((strtotime($golupdate->fields[updatet1]) < (filemtime($cachefile) + 4000))))) {
    echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
    include($cachefile);
} else {ob_start(); // Запуск буфера вывода
?>
		<div class="content">
			<div class="content-asistent">
				<div class="tabl_disq">
				<table class="tabl_disq">
				  <tr class="tabl_head">
				    <td>ДИСКВАЛІФІКАЦІЯ</td>
					<td></td>
					<td></td>	    
				  </tr>


<? //$db->debug = true;
  $record_pagestat["ip"] = $_SERVER['REMOTE_ADDR'];
  //$record_pagestat["ip_forwarded"] = $_SERVER['HTTP_X_FORWARDED_FOR'];
  $record_pagestat["agent"] = $_SERVER['HTTP_USER_AGENT'];
  $record_pagestat["page"] = "violators";
  $db->AutoExecute('page_stat',$record_pagestat,'INSERT');
  
  $recordnah = $db->Execute("select * 
    from v9ky_red a where matc = (select id from v9ky_match b where canseled=1 and turnir=".$turnir." and (b.team1=a.team or b.team2=a.team) order by b.date desc limit 1) ");
   while (!$recordnah->EOF) {
     
     $recordteamname = $db->Execute("select name, pict from v9ky_team where id=".$recordnah->fields[team]."");
     $recordman = $db->Execute("select man from v9ky_player where id=".$recordnah->fields[player]."");
     $recordfio = $db->Execute("select concat(name1, ' ', name2) as fio from v9ky_man where id=".$recordman->fields[man]."");
     $recordtur = $db->Execute("select tur from v9ky_match where id=".$recordnah->fields[matc]."");
     $recordface = $db->Execute("select pict from v9ky_man_face where man=".$recordman->fields[man]." ORDER BY data desc LIMIT 1");
     if ($recordface->fields[pict]) $face = $recordface->fields[pict]; else $face = "avatar.jpg";
?>
				  <tr>
					<td><img height="65" src="<?=$player_face_path?><?=$face?>"><p><?=$recordfio->fields[fio]?></p></td>
					<td><img src="<?=$team_logo_path?><?=$recordteamname->fields[pict]?>"><p><?=$recordteamname->fields[name]?></p></td>
					<td><img src="/img/red-card.png"><p><?=$recordtur->fields[tur]?> Тур</p></td>
				  </tr>
<?   $recordnah->MoveNext();}?>	

<? 
$recordnah = $db->Execute("select *
 
  from v9ky_yellow a where 
  ((select count(id) from v9ky_yellow where player=a.player)>2) and
  ((select max(tur) from v9ky_match where id=a.matc and canseled=1 and turnir=".$turnir.")=(select max(tur) from v9ky_match where canseled=1 and turnir=".$turnir.")) and
  player not in (select player from v9ky_red b where (b.player=a.player) and ((select tur from v9ky_match where id=b.matc) >= (select min(tur) from v9ky_match where id in (select matc from v9ky_yellow where player=b.player) and canseled=1 and turnir=".$turnir." and (team1=team or team2=team) order by date desc limit 3))) and 

  matc in (select id from v9ky_match where canseled=1 and turnir=".$turnir.") 

  group by player");
  
while (!$recordnah->EOF) {
  if ($recordnah->fields[player]<>""){

    $rec_yellow = $db->Execute("select (select tur from v9ky_match where id=a.matc) as tur, (select date from v9ky_match where id=a.matc) as date from v9ky_yellow a where player='".$recordnah->fields[player]."' order by date asc");
    $rec_red = $db->Execute("select (select tur from v9ky_match where id=a.matc) as tur, (select date from v9ky_match where id=a.matc) as date from v9ky_red a where player='".$recordnah->fields[player]."' order by date asc");
    $rec_m = $db->Execute("select (@row_number:=@row_number + 1) AS rn, date from v9ky_match where canseled=1 and turnir=".$turnir." and (team1 in(select team from v9ky_player where id='".$recordnah->fields[player]."') or
      team2 in(select team from v9ky_player where id='".$recordnah->fields[player]."')) order by date asc");
    $rec_mkol = $db->Execute("select count(id) AS kol from v9ky_match where canseled=1 and turnir=".$turnir." and (team1 in(select team from v9ky_player where id='".$recordnah->fields[player]."') or
      team2 in(select team from v9ky_player where id='".$recordnah->fields[player]."')) order by date asc");
    $ban = 3;
    $red_array = array();
	$yellow_array = array();
    while (!$rec_yellow->EOF) {
      $yellow_array[] = $rec_yellow->fields[date];
    $rec_yellow->MoveNext();}
    while (!$rec_red->EOF) {
      $red_array[] = $rec_red->fields[date];
    $rec_red->MoveNext();}
    
    while (!$rec_m->EOF) {
      if (in_array($rec_m->fields[date], $yellow_array)) {
        if ($ban==0) {$ban = 2;} else $ban = $ban-1;
      }
      try { if (in_array($rec_m->fields[date], $red_array)) {
        $ban = 0;
      }
      } catch (Exception $e) {echo "";}
    $rec_m->MoveNext();}
    unset($yellow_array);  unset($red_array);  
    if ($ban==0){

      $recordna = $db->Execute("select (select tur from v9ky_match where id=a.matc) as tur, (select date from v9ky_match where id=a.matc) as date from v9ky_yellow a where player='".$recordnah->fields[player]."' order by date desc limit 3");
      $recordteamname = $db->Execute("select name, pict from v9ky_team where id=".$recordnah->fields[team]."");
     $recordman = $db->Execute("select man from v9ky_player where id=".$recordnah->fields[player]."");
     $recordfio = $db->Execute("select concat(name1, ' ', name2) as fio from v9ky_man where id=".$recordman->fields[man]."");
     $recordface = $db->Execute("select pict from v9ky_man_face where man=".$recordman->fields[man]." ORDER BY data desc LIMIT 1");
     
      if ($recordface->fields[pict]) $face = $recordface->fields[pict]; else $face = "avatar.jpg";
     
?>
			   <tr>
					<td><img height="65" src="<?=$player_face_path?><?=$face?>"><p><?=$recordfio->fields[fio]?></p></td>
					<td><img src="<?=$team_logo_path?><?=$recordteamname->fields[pict]?>"><p><?=$recordteamname->fields[name]?></p></td>
					<td>
<?
  while (!$recordna->EOF) {
    $naharray[] = $recordna->fields[tur];
  $recordna->MoveNext();}
  $naharray = array_reverse($naharray);
  foreach ( $naharray as $tursarray ) {

?>
<img src="/img/yellow-card.png"><p><?=$tursarray?> Тур</p>
<?}
  unset($naharray);?>
</td>
				  </tr>

<?   }} $recordnah->MoveNext();}?>	
				</table>

				<table class="tabl_porush">
				  <tr class="tabl_head">
				    <td>ПОРУШЕННЯ</td>
					<td></td>
					<td></td>	    
				  </tr>

<?
$recordcards = $db->Execute("select SUM(a.kol) as kolyel, id, team, pict, man, pl_id
from(select count(*) as kol, v9ky_team.id as id, v9ky_team.name as team, v9ky_team.pict as pict,
v9ky_player.man as man, v9ky_player.id as pl_id
from v9ky_yellow
INNER JOIN v9ky_player ON (v9ky_player.id=v9ky_yellow.player)
INNER JOIN v9ky_team ON (v9ky_team.id=v9ky_player.team)
INNER JOIN v9ky_match ON (v9ky_match.id=v9ky_yellow.matc)
where v9ky_player.active='1' and v9ky_match.canseled='1' and v9ky_match.turnir='".$turnir."' GROUP by v9ky_yellow.player
UNION ALL
select count(*)*5 as kol, v9ky_team.id as id, v9ky_team.name as team, v9ky_team.pict as pict,
v9ky_player.man as man, v9ky_player.id as pl_id
from v9ky_red
INNER JOIN v9ky_player ON (v9ky_player.id=v9ky_red.player)
INNER JOIN v9ky_team ON (v9ky_team.id=v9ky_player.team)
INNER JOIN v9ky_match ON (v9ky_match.id=v9ky_red.matc)
where v9ky_player.active='1' and v9ky_match.canseled='1' and v9ky_match.turnir='".$turnir."' GROUP by v9ky_red.player
ORDER BY 5) AS a GROUP by a.man ORDER BY kolyel DESC
");

$i1 = $recordcards->fields[kolyel]; $i = 1;
while (!$recordcards->EOF) {
	
   $recordface = $db->Execute("select * from v9ky_man_face where man='".$recordcards->fields[man]."' ORDER BY data desc LIMIT 1");
   $recordname = $db->Execute("select * from v9ky_man where id='".$recordcards->fields[man]."' LIMIT 1");
   if ($recordface->fields[pict]) $face = $recordface->fields[pict]; else $face = "avatar.jpg";	
	
   if ($i1 <> $recordcards->fields[kolyel]) $i = $i+1;
   echo "<tr><td><img src='".$player_face_path.$face."' alt='' /><p>".$recordname->fields[name1]."<br>".$recordname->fields[name2]."</p></td>";


   echo "<td><a href='".$url."/team/id/".$recordcards->fields[id]."' >
   <img src='".$team_logo_path.$recordcards->fields[pict]."' alt='' /></a>
   <p><a href='".$url."/team/id/".$recordcards->fields[id]."' >".stripcslashes($recordcards->fields[team])."</a></p>";
   $recordcard = $db->Execute("select v9ky_match.tur as t, '1' as vid, v9ky_match.date as d from v9ky_yellow
      INNER JOIN v9ky_player ON (v9ky_player.id=v9ky_yellow.player)
      INNER JOIN v9ky_match ON (v9ky_match.id=v9ky_yellow.matc)
      where v9ky_player.active='1' and v9ky_match.canseled='1' and v9ky_yellow.player='".$recordcards->fields[pl_id]."'
	  UNION ALL
	  select v9ky_match.tur as t, '2' as vid, v9ky_match.date as d from v9ky_red
      INNER JOIN v9ky_player ON (v9ky_player.id=v9ky_red.player)
      INNER JOIN v9ky_match ON (v9ky_match.id=v9ky_red.matc)
      where v9ky_player.active='1' and v9ky_match.canseled='1' and v9ky_red.player='".$recordcards->fields[pl_id]."'
	  ORDER BY d");
   echo "<td>";
   while (!$recordcard->EOF) {
     if ($recordcard->fields[vid]==1) echo "<img src='http://".$_SERVER['SERVER_NAME']."/img/yellow-card.png' alt='' />".$recordcard->fields[t]." тур";
	 if ($recordcard->fields[vid]==2) echo "<img src='http://".$_SERVER['SERVER_NAME']."/img/red-card.png' alt='' />".$recordcard->fields[t]." тур";
     $recordcard->MoveNext();}
   echo "</td></tr>";
   $i1 = $recordcards->fields[kolyel];
   $recordcards->MoveNext();
}
?>
				  
				</table>
				</div>
				
			</div>
		</div>
	</div>
</article>
<?

$time = microtime(true) - $start;
error_log($_SERVER['REQUEST_URI']." ->Gen in ".$time."sec");

  // Кешируем содержание в файл
$cached = fopen($cachefile, 'w');
fwrite($cached, ob_get_contents());
fclose($cached);
ob_end_flush(); // Отправялем вывод в браузер
}

  include_once "footer.php";
?>