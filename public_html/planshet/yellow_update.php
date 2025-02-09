<?PHP 
define('READFILE', true);
require_once ("menu.php");
require_once('config.php');
require_once('ajax_forms/PHPLiveX.php');

$matcid = intval($_GET['matcid'])*1;
$recordS = $db->Execute("select turnir from v9ky_match where id='".$matcid."'");


class Validation {
    
    public $text1 = "<tr><td>ID</td><td>Команда</td><td>Гравець</td><td>Хвилина</td><td><img width=20px height=27px src='https://v9ky.in.ua/planshet/picts/yel.jpg'></td></tr>";
	
    
    public function sel_all(){
        global $db;
        global $matcid;
		
        $test1 = $this -> text1;
        $recordS1 = $db->Execute("select * from v9ky_yellow where matc='".$matcid."' ORDER BY time");
        while (!$recordS1->EOF) {
          $recordS2 = $db->Execute("select * from v9ky_team where id='".$recordS1->fields[team]."'"); 
	  $recordS3 = $db->Execute("select * from v9ky_player where id='".$recordS1->fields[player]."'"); 
          $recordname = $db->Execute("select name1, name2 from v9ky_man where id=".$recordS3->fields[man]."");  
          $test1 .= "<tr><td>".$recordS1->fields[0]."</td>
	    <td>".$recordS2->fields[name]."</td><td>".$recordname->fields[name1]." ".$recordname->fields[name2]."</td>
	    <td>".$recordS1->fields[time]."</td>
	    <td><input type='submit' value=' Видалити ' onclick='validdel(".$recordS1->fields[id].");'></td>";
          $test1 .=  "</tr> \n";
          $recordS1->MoveNext();
        }
        
        return $test1;
    }	


    public function create_man($team, $time, $player){
      global $db;
      global $matcid;
      global $recordS;
       
      $team=intval($team)*1;
      $time=intval($time)*1;
      $player=intval($player)*1;	
	
      $record["matc"] = $matcid;	
      $record["team"] = $team;
      $record["time"] = $time;
      $record["player"] = $player;
        
      //запись в базу
      $db->AutoExecute('v9ky_yellow',$record,'INSERT');
      $record_gol_to_matc["upd_teams_match_stat"] = 1;
	  $db->AutoExecute('v9ky_match',$record_gol_to_matc,'UPDATE', 'id = '.$matcid.'');
      $test = $this -> sel_all();

      return $test;
    }

    public function del_man($idnum){
      global $db;
      global $recordS;
	  global $matcid;
      $db->Execute("delete from v9ky_yellow where id='".$idnum."'");
      $record_gol_to_matc["upd_teams_match_stat"] = 1;
	  $db->AutoExecute('v9ky_match',$record_gol_to_matc,'UPDATE', 'id = '.$matcid.'');
      $test = $this -> sel_all();

      return $test;
    }

    public function moment($time){
      global $matcid;
      global $db;

      $record2["minuta"] = 1*intval($time);
      $record2["matc"] = $matcid;
      $db->AutoExecute('v9ky_moments',$record2,'INSERT');
      $test = $time;
      return $test;
    }
    
    public function activer($active){
      global $matcid;
      global $recordS;
      global $db;

      $record1["canseled"] = 1*intval($active);
      $record1["upd_teams_match_stat"] = 1;
      $db->AutoExecute('v9ky_match',$record1,'UPDATE', 'id = '.$matcid.'');
      $test = $active;
      return $test;
    }
}

$ajax = new PHPLiveX();

$myClass = new Validation();
$ajax->AjaxifyObjectMethods(array("myClass" => array("create_man", "del_man", "moment", "activer")));
// If validateEmail was a static function, you wouldn't need to create an object:
// $ajax->AjaxifyClassMethods(array("Validation" => array("validateEmail")));

$ajax->Run(); // Must be called inside the 'html' or 'body' tags
?>

<script type="text/javascript">
function validdel(idnum){
    myClass.del_man(idnum, {

        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;
        }
    });
}

function validate1(){
    val1 = document.getElementById("team1").value;
    val2 = document.getElementById("time1").value;
    val3 = document.getElementById("player1").value;
	
    myClass.create_man(val1, val2, val3, {

        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function validate2(){
    val1 = document.getElementById("team2").value;
    val2 = document.getElementById("time2").value;
    val3 = document.getElementById("player2").value;
	
    myClass.create_man(val1, val2, val3, {

        "onFinish": function(response){
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function activem(){
    val1 = document.getElementById("active").value;
	
    myClass.activer(val1, {

        "onFinish": function(response){
            var msg = document.getElementById("acmsg");
            msg.innerHTML = response;

        }
    });
}

function moment(){
    val1 = document.getElementById("moment").value;
	
    myClass.moment(val1, {

        "onFinish": function(response){
            var msg = document.getElementById("mommsg");
            msg.innerHTML = response;

        }
    });
}

</script>


<?

$recordS = $db->Execute("select * from v9ky_match where id='".$matcid."'");
$recordSet2 = $db->Execute("select name from v9ky_team where id='".$recordS->fields[3]."'"); 
$recordSet3 = $db->Execute("select name from v9ky_team where id='".$recordS->fields[4]."'");
?>  
<center>

<script type="text/javascript">

   function goinfo() {
     window.location = "https://v9ky.in.ua/planshet/match_info.php?matcid=<?=$matcid?>";
   }
   function gogol() {
     window.location = "https://v9ky.in.ua/planshet/gol_update.php?matcid=<?=$matcid?>";
   }
   function goyellow() {
     window.location = "https://v9ky.in.ua/planshet/yellow_update.php?matcid=<?=$matcid?>";
   }
   function goyellowred() {
     window.location = "https://v9ky.in.ua/planshet/yellow_red_update.php?matcid=<?=$matcid?>";
   }
   function gored() {
     window.location = "https://v9ky.in.ua/planshet/red_update.php?matcid=<?=$matcid?>";
   }
   function goasist() {
     window.location = "https://v9ky.in.ua/planshet/asist_update.php?matcid=<?=$matcid?>";
   }
   function gopenalty() {
     window.location = "https://v9ky.in.ua/planshet/penalty_update.php?matcid=<?=$matcid?>";
   }

  </script>
<center><table border='1' cellpadding='5'><tr><td>

<select id="moment" onchange="moment();">
<option<?
echo ">Цікавий момент</option> \n";

$x=0;
while ($x++<90) echo "<option value='".$x."'>".$x." хв</option> \n";
echo"</select>";

echo "<span id='mommsg'></span></td><td>";
?>

<select id="active" onchange="activem();">
<option value='0'<?

if ($recordS->fields[canseled] ==0) echo "selected";
echo ">Активний</option> \n";
	 
print "<option value='1'";
if ($recordS->fields[canseled] ==1) echo "selected";
echo ">Завершений</option> \n";

print "<option value='2'";
if ($recordS->fields[canseled] ==2) echo "selected";
echo ">LIVE</option> \n";
	 echo"</select><span id='acmsg'></span>";

echo"</td><td>".date('H:i', strtotime($recordS->fields[date]))."</td><td>".$recordSet2->fields[name]."</td><td>".$recordS->fields[gols1].":".$recordS->fields[gols2]."</td>
<td>".$recordSet3->fields[name]."</td>
<td><button type='submit' onclick='goinfo();'><img width=40px height=40px src='https://v9ky.in.ua/img/anons.png' 
          style='vertical-align: middle'> ІНФО</button></td>
<td><button type='submit' onclick='gogol();'><img width=40px height=40px src='https://v9ky.in.ua/planshet/picts/ball.jpg' 
          style='vertical-align: middle'> ГОЛИ</button></td>
<td><input type='hidden' name='matcid' value='".$matcid."'><button type='submit' onclick='goyellowred();'><img width=40px height=40px src='https://v9ky.in.ua/planshet/picts/yellow-red-icon.png' 
          style='vertical-align: middle'> ЖОВ-ЧЕР</button>
</td>
<td><button type='submit' onclick='gored();'><img width=30px height=40px src='https://v9ky.in.ua/planshet/picts/red.jpg' 
          style='vertical-align: middle'> ЧЕРВОНІ</button>
</td>
<td><input type='hidden' name='matcid' value='".$matcid."'><button type='submit' onclick='goasist();'><img width=30px height=40px src='https://v9ky.in.ua/planshet/picts/asist.jpg' 
          style='vertical-align: middle'> АСИСТИ</button>
</td>
<td><input type='hidden' name='matcid' value='".$matcid."'><button type='submit' onclick='gopenalty();'><img width=40px height=40px src='https://v9ky.in.ua/planshet/picts/football-penalty-6m.png' 
          style='vertical-align: middle'> ПЕНАЛЬТІ</button>
</td>
</tr></table>"; ?>

<?  
  echo "<table><tr><td>Команда</td><td>Гравець</td><td>Хвилина</td></tr>
     <tr><td>";
	 echo"<select id='team1' name='team1' size=1> ";
	 $recordSet2 = $db->Execute("select * from v9ky_team where id=".$recordS->fields[team1]."");
	 print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 
	 echo"</select> </td><td><select id='player1' name='player1' size=1>";
	 $recordSet5 = $db->Execute("select v9ky_sostav.nomer as nomer, v9ky_player.id as id,v9ky_player.name1 as name1,v9ky_player.name2 as name2 from v9ky_sostav, v9ky_player where v9ky_player.team=".$recordS->fields[team1]." AND v9ky_sostav.player=v9ky_player.id AND v9ky_sostav.matc='".$matcid."' ORDER BY v9ky_player.team=".$recordS->fields[team1].",v9ky_sostav.nomer, v9ky_player.name1");
	 while (!$recordSet5->EOF) 
	 {
             $recordname = $db->Execute("select name1, name2 from v9ky_man where id=(select man from v9ky_player where id=".$recordSet5->fields[id].")");
		 if ($recordSet5->fields[id]==$recordSet1->fields['player']) print "<option value='".$recordSet5->fields[id]."' selected>
		 ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 else print "<option value='".$recordSet5->fields[id]."'>".$recordSet5->fields[nomer]." ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 $recordSet5->MoveNext();
	 }
	
	 echo"</select>";
	 
	 echo" </td>
	 <td><input type='number' min='0' id='time1' name='time1' size='3' ></td><td>";
  echo "<input type='submit' value='  Створити ЖК ' onclick='validate1();'></td></tr></table>";

  
  echo "<table><tr><td>";
	 echo"<select id='team2' name='team2' size=1> ";
	 $recordSet2 = $db->Execute("select * from v9ky_team where id=".$recordS->fields[team2]."");
	 print "<option value='".$recordSet2->fields[id]."'>".$recordSet2->fields[name]."</option> \n";
		 
	 echo"</select> </td><td><select id='player2' name='player2' size=1>";
	 $recordSet5 = $db->Execute("select v9ky_sostav.nomer as nomer, v9ky_player.id as id,v9ky_player.name1 as name1,v9ky_player.name2 as name2 from v9ky_sostav, v9ky_player where v9ky_player.team=".$recordS->fields[team2]." AND v9ky_sostav.player=v9ky_player.id AND v9ky_sostav.matc='".$matcid."' ORDER BY v9ky_player.team=".$recordS->fields[team2].",v9ky_sostav.nomer, v9ky_player.name1");
	 while (!$recordSet5->EOF) 
	 {
             $recordname = $db->Execute("select name1, name2 from v9ky_man where id=(select man from v9ky_player where id=".$recordSet5->fields[id].")");
		 if ($recordSet5->fields[id]==$recordSet1->fields['player']) print "<option value='".$recordSet5->fields[id]."' selected>
		 ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 else print "<option value='".$recordSet5->fields[id]."'>".$recordSet5->fields[nomer]." ".$recordname->fields[name1]." ".$recordname->fields[name2]."</option> \n";
		 $recordSet5->MoveNext();
	 }
	
	 echo"</select>";
	 
	 echo" </td>
	 <td><input type='number' min='0' id='time2' name='time2' size='3' ></td><td>";
  echo "<input type='submit' value='  Створити ЖК ' onclick='validate2();'></td></tr></table>";

  

  
  echo "<center><table id='msg' cellspacing='2' border='1' cellpadding='5'>";
  echo $myClass -> sel_all();
  echo"</table></center>";
  
?> 
</center>
</body>
</html>
