<?PHP 
define('READFILE', true);
require_once ("menu.php");
require_once('config.php');
?>  
<center>
<h1>Редактирование видео</h1>
<form action="post_game_update.php" method="GET" onSubmit="return sendForm(this);" ENCTYPE='multipart/form-data'> 
<?
    if (isset($_GET['matcid'])) $matcid=intval($_GET['matcid'])*1; else exit();
    if (isset($_GET['tur'])) $tur=intval($_GET['tur'])*1; else exit();
    
    $recordSet = $db->Execute("select id from v9ky_post_game where turnir='".$matcid."' and tur='".$tur."' "); 

    if (isset($_GET['red'])){ 	
        if (isset($_GET['video'])) {$video=addslashes($_GET['video']); } ELSE {$video="";}
        if (isset($_GET['video2'])) {$video2=addslashes($_GET['video2']); } ELSE {$video2="";}	
        if (isset($_GET['video3'])) {$video3=addslashes($_GET['video3']); } ELSE {$video3="";}	
        if (isset($_GET['pl1'])) $pl1=intval($_GET['pl1'])*1; else $pl1=0;
        if (isset($_GET['pl2'])) $pl2=intval($_GET['pl2'])*1; else $pl2=0;
        if (isset($_GET['pl3'])) $pl3=intval($_GET['pl3'])*1; else $pl3=0;
        if (isset($_GET['pl4'])) $pl4=intval($_GET['pl4'])*1; else $pl4=0;
        if (isset($_GET['pl5'])) $pl5=intval($_GET['pl5'])*1; else $pl5=0;
	  	    
        $record["url"] = $video;
        $record["url2"] = $video2;
        $record["url3"] = $video3;
        $record["tur"] = $tur;
        $record["pl1"] = $pl1;
        $record["pl2"] = $pl2;
        $record["pl3"] = $pl3;
        $record["pl4"] = $pl4;
        $record["pl5"] = $pl5;
        $record["turnir"] = $matcid;
    	
        if ($recordSet->fields[id]>0) {$db->AutoExecute('v9ky_post_game',$record,'UPDATE', 'id = '.$recordSet->fields[id].'');}
        else {$db->AutoExecute('v9ky_post_game',$record,'INSERT');}
    }

    $recordSet = $db->Execute("select * from v9ky_post_game a where turnir='".$matcid."' and tur='".$tur."' ");
   
    echo "<center><table cellspacing='2' border='1' cellpadding='5'><tr><td>Турнир</td><td>Тур</td>
      <td>После игры</td><td>Гол тура</td><td>Сейв тура</td></tr>";
    
    
    print "<tr>
          <td>".$matcid."</td>
	  <td>".$tur."</td>
	  <td><iframe width='300' height='250' src='".$recordSet->fields[url]."' frameborder='0' allowfullscreen></iframe></td>
          <td><iframe width='300' height='250' src='".$recordSet->fields[url2]."' frameborder='0' allowfullscreen></iframe></td>
          <td><iframe width='300' height='250' src='".$recordSet->fields[url3]."' frameborder='0' allowfullscreen></iframe></td>";
    print "</tr></table></center>";
  
  
	 $video = $recordSet->fields['url'];
         $video2 = $recordSet->fields['url2'];
         $video3 = $recordSet->fields['url3'];
	 $pl1 = $recordSet->fields['pl1'];	 
	 $pl2 = $recordSet->fields['pl2'];
         $pl3 = $recordSet->fields['pl3'];	 
	 $pl4 = $recordSet->fields['pl4'];
         $pl5 = $recordSet->fields['pl5'];
	 
     echo"Адрес видео Огляд матчів: <input type='text' name='video' size='100' value='".stripslashes($video)."'><br><br>";
     echo"Адрес видео гол тура: <input type='text' name='video2' size='100' value='".stripslashes($video2)."'><br><br>";
     echo"Адрес видео сейв тура: <input type='text' name='video3' size='100' value='".stripslashes($video3)."'><br><br>Сборная тура<br>";
	

     $recordpl = $db->Execute("select id, (select name from v9ky_team where id=a.team) as team, (select name1 from v9ky_man where id=a.man) as name1, (select name2 from v9ky_man where id=a.man) as name2 from v9ky_player a where team in (select id from v9ky_team where turnir='".$matcid."') ORDER BY team, (select name1 from v9ky_man where id=a.man)");
     echo "<select name='pl1' >";
     echo "<option "; if (0==$pl1){ echo "selected";} echo " value = '0'>-</option>";
        while (!$recordpl->EOF)
	 {
           echo "<option "; if (($recordpl->fields[id])==$pl1){ echo "selected";} echo " value = '".$recordpl->fields[id]."'>".$recordpl->fields[team]." ".$recordpl->fields[name1]." ".$recordpl->fields[name2]."</option>";
           $recordpl->MoveNext();
	 }
     echo "</select>";
     $recordpl->MoveFirst();

     echo "<select name='pl2' >";
     echo "<option "; if (0==$pl2){ echo "selected";} echo " value = '0'>-</option>";
        while (!$recordpl->EOF)
	 {
           echo "<option "; if (($recordpl->fields[id])==$pl2){ echo "selected";} echo " value = '".$recordpl->fields[id]."'>".$recordpl->fields[team]." ".$recordpl->fields[name1]." ".$recordpl->fields[name2]."</option>";
           $recordpl->MoveNext();
	 }
     echo "</select><br><br>";
     $recordpl->MoveFirst();

     echo "<select name='pl3' >";
     echo "<option "; if (0==$pl3){ echo "selected";} echo " value = '0'>-</option>";
        while (!$recordpl->EOF)
	 {
           echo "<option "; if (($recordpl->fields[id])==$pl3){ echo "selected";} echo " value = '".$recordpl->fields[id]."'>".$recordpl->fields[team]." ".$recordpl->fields[name1]." ".$recordpl->fields[name2]."</option>";
           $recordpl->MoveNext();
	 }
     echo "</select>";
     $recordpl->MoveFirst();

     echo "<select name='pl4' >";
     echo "<option "; if (0==$pl4){ echo "selected";} echo " value = '0'>-</option>";
        while (!$recordpl->EOF)
	 {
           echo "<option "; if (($recordpl->fields[id])==$pl4){ echo "selected";} echo " value = '".$recordpl->fields[id]."'>".$recordpl->fields[team]." ".$recordpl->fields[name1]." ".$recordpl->fields[name2]."</option>";
           $recordpl->MoveNext();
	 }
     echo "</select><br><br>";
     $recordpl->MoveFirst();
 
     echo "<select name='pl5' >";
     echo "<option "; if (0==$pl5){ echo "selected";} echo " value = '0'>-</option>";
        while (!$recordpl->EOF)
	 {
           echo "<option "; if (($recordpl->fields[id])==$pl5){ echo "selected";} echo " value = '".$recordpl->fields[id]."'>".$recordpl->fields[team]." ".$recordpl->fields[name1]." ".$recordpl->fields[name2]."</option>";
           $recordpl->MoveNext();
	 }
     echo "</select>";
     $recordpl->MoveFirst();


	 
     echo"<br> <br><input type='submit' value='  Изменить  '>";
     echo "<input type='hidden' name='tur' value='".$tur."'>";
     echo "<input type='hidden' name='matcid' value='".$matcid."'>";
	 echo "<input type='hidden' name='red' value='1'>";
	 
	 echo "</form> ";     

?>

</center>
</body>
</html>