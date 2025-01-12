<?
define('READFILE', true);
require_once ("menu.php");
require_once('config.php');
require_once('ajax_forms/PHPLiveX.php');


$id = intval($_GET['matcid'])*1;
$recordS = $db->Execute("select name from v9ky_team where id='".$id."'");
$kol_igrokov = $db->Execute("select count(*) as a from v9ky_player where team='".$id."' and active='1'");
$kol = $kol_igrokov->fields['a'];
$name = $recordS->fields['name'];


class Validation {

	public $text1 = "<tr><td>ID</td><td></td><td></td><td>Фамилия</td><td>Имя</td><td>Отчество</td><td>Дата рождения</td><td>Амплуа</td><td>V9KY</td><td>Дублер</td><td>Вибув</td><td>Активен</td><td></td><td></td></tr>
	                 <tr><td></td><td></td><td><input type='text' size='3' id='find0'  onkeyup='findman(0);'><select id='man0'><option>Choose A man</option></select><input type='button' value='Создать' onclick='validate();'></td>";

	public function sel_all(){
		global $db;
		global $id;
		$recordS1 = $db->Execute("select * from v9ky_player where team='".$id."' ORDER BY active desc");//, name1, name2, name3");

		while (!$recordS1->EOF) {
			$recordman = $db->Execute("select * from v9ky_man where id='".$recordS1->fields[man]."' ");
                        $fotos = $db->Execute("select pict from v9ky_man_face where man = '".$recordman->fields[id]."' order by data desc");
			$test1 .= '<tr><td>'.$recordS1->fields[0].'</td><td><img src="http://v9ky.in.ua/face/'.$fotos->fields[pict].'" height=50 /><td><input type="text" size="3" id="find'.$recordS1->fields[0].'"  onkeyup="findman('.$recordS1->fields[0].');" >
			   <select id="man'.$recordS1->fields[0].'" >';
			$test1 .= '<option value="'.$recordS1->fields[man].'">'.$recordman->fields[name1].' '.$recordman->fields[name2].'</option></select> </td>';

			$test1 .= "<td><input type='text' id='name1".$recordS1->fields[0]."' name='name1' size='15' value='".($recordman->fields[name1])."'></td>
	             <td><input type='text' id='name2".$recordS1->fields[0]."' name='name2' size='15' value='".$recordman->fields[name2]."'></td>
	             <td><input type='text' id='name3".$recordS1->fields[0]."' name='name3' size='15' value='".$recordman->fields[name3]."'></td>";

	             $age = date_create($recordman->fields['age']);
			if ($recordS1->fields[active]==1){
				$str = "<td  bgcolor='green'><input type='submit' value='Актив' onclick='activate(0, ".$recordS1->fields[0].");'>";
			}else{
				$str = "<td><input type='submit' value='Неактив' onclick='activate(1, ".$recordS1->fields[0].");'>";
			}

                     if ($recordS1->fields[vibuv]==1){
				$strv = " bgcolor='red'";
			}else{
				$strv = "";
			}

	        $test1 .= "<td><input type='date' id='age".$recordS1->fields[0]."' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
                           <td><select id='amplua".$recordS1->fields[0]."' name='amplua' size=1>
                                 <option value='0'";
                if ($recordS1->fields[amplua]==0) $test1 .= "selected";
                $test1 .= ">нет</option> \n 
                                 <option value='1'";
                if ($recordS1->fields[amplua]==1) $test1 .= "selected";
                $test1 .= ">Вратарь</option> \n 
                                <option value='4'";
                if ($recordS1->fields[amplua]=='4') $test1 .= "selected";
                $test1 .= ">Тренер</option> \n 
                                    <option value='5'";
                if ($recordS1->fields[amplua]=='5') $test1 .= "selected";
                $test1 .= ">Менеджер</option> \n 
                               </select></td>
                <td><select id='v9ky".$recordS1->fields[0]."' name='v9ky' size=1>
                                 <option value='0'";
                if ($recordS1->fields[v9ky]==0) $test1 .= "selected";
                $test1 .= ">нi</option> \n 
                                 <option value='1'";
                if ($recordS1->fields[v9ky]==1) $test1 .= "selected";
                $test1 .= ">V9KY</option> \n 
                               </select></td>
           
                <td><select id='dubler".$recordS1->fields[0]."' name='dubler' size=1>
                                 <option value='0'";
                if ($recordS1->fields[dubler]==0) $test1 .= "selected";
                $test1 .= ">нi</option> \n 
                                 <option value='1'";
                if ($recordS1->fields[dubler]==1) $test1 .= "selected";
                $test1 .= ">ДублерA</option> \n 
                <option value='2'";
                if ($recordS1->fields[dubler]==2) $test1 .= "selected";
                $test1 .= ">ДублерB</option> \n
                               </select></td>

                <td".$strv."><select id='vibuv".$recordS1->fields[0]."' name='vibuv' size=1>
                                 <option value='0'";
                if ($recordS1->fields[vibuv]==0) $test1 .= "selected";
                $test1 .= ">нi</option> \n 
                                 <option value='1'";
                if ($recordS1->fields[vibuv]==1) $test1 .= "selected";
                $test1 .= ">Вибув</option> \n 
                               </select></td>

			     ".$str."</td>
	             <td><input type='submit' value=' Изменить ' onclick='validupdate(".$recordS1->fields[0].");'></td>
			<td><input type='submit' value=' Удалить ' onclick='validdel(".$recordS1->fields[0].");'></td>";
            $test1 .= "</tr> \n";
            $recordS1->MoveNext();
        }
		return $test1;

	}

	public function del_man($delid){
		global $db;
		global $name;
		$delid = 1*$delid;
		$record = $db->Execute("select * from v9ky_player where id = '".$delid."' ");
		$man = $db->Execute("select * from v9ky_man where id = '".$record->fields['man']."' ");
        $recordteam = $db->Execute("select turnir from v9ky_team where id = '".$record->fields[team]."' ");
		$db->Execute("delete from v9ky_player where id='".$delid."'");
                $db->Execute("delete from v9ky_red where player='".$delid."'");
                $db->Execute("delete from v9ky_yellow where player='".$delid."'");
        if($record->fields['name1']){
            $logs["log"] = "Гравця ".$record->fields[name1]." ".$record->fields[name2]." видалено зі складу команди ".$name;      
        } else {
            $logs["log"] = "Гравця ".$man->fields[name1]." ".$man->fields[name2]." видалено зі складу команди ".$name;            
        }
        // $logs["log"] = "Гравця ".$record->fields[name1]." ".$record->fields[name2]." видалено зі складу команди ".$name;       
                $logs["turnir"] =$recordteam->fields[turnir];
                $logs["del_id"] =$delid;
                $logs["man_id"] =$man->fields['id'];
                $logs["team_id"] =$record->fields['team'];
		$db->AutoExecute('v9ky_transfer_log',$logs,'INSERT');    ///////////////////////transfer log

		$test = $this -> text1;


        $test .= "<td><input type='text' id='name1' name='name1' size='15' value=''></td>
        	<td><input type='text' id='name2' name='name2' size='15' value=''></td>
        	<td><input type='text' id='name3' name='name3' size='15' value=''></td>";

        $age =date_create("1900-01-01");
        $test .= "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
            <td><select id='amplua0' name='amplua' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>Вратарь</option> \n 
                                 <option value='4'>Тренер</option> \n 
                                 <option value='5'>Менеджер</option> \n 
                               </select></td>
            <td><select id='v9ky0' name='v9ky' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>V9KY</option> \n 
                               </select></td>
            <td>1</td>
	        <td></td>
            <td>".$errmsg."</td></tr> \n";

		$test .= $this->sel_all();

        return $test;
	}

	public function find($str){
		global $db;
		//$str = mysql_real_escape_string($str);
		$test .="<td>";
		if ($str<>""){
        $recordS1 = $db->Execute("select * from v9ky_man where name1 like '".$str."%' ");
		if (isset($recordS1)){
		while (!$recordS1->EOF) {
			$test .= '<option value="'.$recordS1->fields[id].'">'.$recordS1->fields[name1].' '.$recordS1->fields[name2].'</option>';
		   $recordS1->MoveNext();
		}

		}
		}else $test .= $str;


		$test .="</td>";


		return $test;
	}

    public function activate($act, $idnum){
		global $db;
		global $id;
        global $name;

        $act = 1*$act;
		$idnum = 1*$idnum;

		$record["active"] = $act;

		$db->AutoExecute('v9ky_player',$record,'UPDATE', 'id = '.$idnum.'');
		$record = $db->Execute("select * from v9ky_player where id = '".$idnum."' ");
		// $man = $db->Execute("select * from v9ky_man where id = '".$record->fields['man']."' ");
                $recordteam = $db->Execute("select turnir from v9ky_team where id = '".$record->fields[team]."' ");
		if ($act == 1){
			$logs["log"] = "Гравця ".$record->fields[name1]." ".$record->fields[name2]." додано до складу команди ".$name;
		}else{
			$logs["log"] = "Гравця ".$record->fields[name1]." ".$record->fields[name2]." видалено зі складу команди ".$name;
		}
        $logs["turnir"] =$recordteam->fields[turnir];
        $logs["man_id"] =$record->fields['man'];
        $logs["team_id"] =$record->fields['team'];
		$db->AutoExecute('v9ky_transfer_log',$logs,'INSERT');    ///////////////////////transfer log

		$test = $this -> text1;

        $test .= "<td><input type='text' id='name1' name='name1' size='15' value=''></td>
        	<td><input type='text' id='name2' name='name2' size='15' value=''></td>
        	<td><input type='text' id='name3' name='name3' size='15' value=''></td>";

        $age =date_create("1900-01-01");
        $test .= "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
            <td><select id='amplua0' name='amplua' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>Вратарь</option> \n 
                                 <option value='4'>Тренер</option> \n 
                                 <option value='5'>Менеджер</option> \n 
                               </select></td>
            <td><select id='v9ky0' name='v9ky' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>V9KY</option> \n 
                               </select></td>
            <td><select id='dubler0' name='dubler' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>dublerA</option> \n 
                                 <option value='2'>dublerB</option> \n 
                </select>
            </td>
            <td>1</td>
	        <td><input type='button' value='Создать' onclick='validate();'></td>
            <td></td></tr> \n";

		$test .= $this->sel_all();

        return $test;
    }

    public function create_man($idnum, $man, $amplua, $v9ky, $dubler, $vibuv){
		global $db;
		global $id;
		global $name;
		$errmsg="";

		$man=filter_string($man);


		$idnum = 1*$idnum;

		$recordS = $db->Execute("select COUNT(*) as kol from v9ky_player where man='".$man."' and team='".$id."' and not id='".$idnum."' ");
		if ($recordS->fields['kol']==0) {

		   $recordman = $db->Execute("select * from v9ky_man where id = '".$man."' ");
		   $recordface = $db->Execute("select * from v9ky_man_face where man = '".$man."' ORDER BY data limit 1 ");

		   $record["name1"] = $recordman->fields[name1];
		   $record["name2"] = $recordman->fields[name2];
           $record["name3"] = $recordman->fields[name3];
		   $record["age"] = $recordman->fields[age];
		   if ($recordface->fields[pict]<>"") $record["face"] = $recordface->fields[pict]; else $record["face"] = "avatar.jpg";
		   $record["team"] = $id;
		   $record["man"] = $man;
                   $record["amplua"] = $amplua;
                   $record["v9ky"] = $v9ky;
                   $record["dubler"] = $dubler;
                   $record["vibuv"] = $vibuv;

			if ($idnum<>0){
				$db->AutoExecute('v9ky_player',$record,'UPDATE', 'id = '.$idnum.'');
			}else{
				$db->AutoExecute('v9ky_player',$record,'INSERT');
			    $logs["log"] = "Гравця ".$record["name1"]." ".$record["name2"]." додано до складу команди ".$name;
                $recordteam = $db->Execute("select turnir from v9ky_team where id = '".$id."' ");
                $logs["turnir"] =$recordteam->fields[turnir];
                $logs["man_id"] = $record["man"];
                $logs["team_id"] =$id;
			    $db->AutoExecute('v9ky_transfer_log',$logs,'INSERT');    ///////////////////////transfer log
			}

		   $man = "";
		}else $errmsg = "<font color='red'>Такой игрок уже<br>есть в команде!</font>";


		$test = $this -> text1;

        $test .= "<td><input type='text' id='name1' name='name1' size='15' value='".$name1."'></td>
        	<td><input type='text' id='name2' name='name2' size='15' value='".$name2."'></td>
        	<td><input type='text' id='name3' name='name3' size='15' value='".$name3."'></td>";

        $age =date_create("1900-01-01");
        $test .= "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
            <td><select id='amplua0' name='amplua' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>Вратарь</option> \n 
                                 <option value='4'>Тренер</option> \n 
                                 <option value='5'>Менеджер</option> \n 
                               </select></td>
            <td><select id='v9ky0' name='v9ky' size=1>
                                 <option value='0' selected>нi</option> \n 
                                 <option value='1'>V9KY</option> \n 
                               </select></td>
            <td><select id='dubler0' name='dubler' size=1>
                                 <option value='0' selected>нi</option> \n 
                                 <option value='1'>ДублерA</option> \n 
                                 <option value='2'>ДублерB</option> \n 
                </select>
            </td>
            <td><select id='vibuv0' name='vibuv' size=1>
                                 <option value='0' selected>нi</option> \n 
                                 <option value='1'>Вибув</option> \n 
                </select>
            </td>
            <td>1</td>
	        <td><input type='button' value='Создать' onclick='validate();'></td>
            <td>".$errmsg."</td></tr> \n";

		$test .= $this->sel_all();

        return $test;
    }
}

$ajax = new PHPLiveX();

$myClass = new Validation();
$ajax->AjaxifyObjectMethods(array("myClass" => array("create_man", "del_man", "find", "activate")));
// If validateEmail was a static function, you wouldn't need to create an object:
// $ajax->AjaxifyClassMethods(array("Validation" => array("validateEmail")));

$ajax->Run(); // Must be called inside the 'html' or 'body' tags

?>

<script type="text/javascript">
function validupdate(idnum) {
    val1 = document.getElementById("man" + idnum).value;
    val2 = document.getElementById("amplua" + idnum).value;
    val3 = document.getElementById("v9ky" + idnum).value;
    val4 = document.getElementById("dubler" + idnum).value;
    val5 = document.getElementById("vibuv" + idnum).value;

    myClass.create_man(idnum, val1, val2, val3, val4, val5, {

        "onFinish": function(response) {
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function activate(act, idnum) {

    myClass.activate(act, idnum, {

        "onFinish": function(response) {
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function validdel(idnum) {

    myClass.del_man(idnum, {

        "onFinish": function(response) {
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}

function findman(findid) {
    val1 = document.getElementById("find" + findid).value;
    myClass.find(val1, {

        "onFinish": function(response) {
            var msg = document.getElementById("man" + findid);
            msg.innerHTML = response;

        }
    });
}

function validate() {
    val1 = document.getElementById("man0").value;
    val2 = document.getElementById("amplua0").value;
    val3 = document.getElementById("v9ky0").value;
    val4 = document.getElementById("dubler0").value;
    val5 = document.getElementById("vibuv0").value;

    myClass.create_man(0, val1, val2, val3, val4, val5, {

        "onFinish": function(response) {
            var msg = document.getElementById("msg");
            msg.innerHTML = response;

        }
    });
}
</script>


<center>
    <h3>Команда <?=$recordS->fields[name];?></h3>
    Игроков, бывших в составе хоть одного матча данного турнира не удалять и не замещать - а просто делать неактивными
    (на них висит статистика)!!!
    <?

  echo "<table id='msg' cellspacing='0' bordercolor='silver' border='1' cellpadding='3'>
<tr><td>ID</td><td></td><td></td><td>Фамилия</td><td>Имя</td><td>Отчество</td><td>Дата рождения</td><td>Амплуа</td><td>V9KY</td><td>Дублер</td><td>Вибув</td><td>Активен</td><td>".$kol."</td><td></td></tr>";

  print "<tr><td></td><td></td><td><input type='text' size='3' id='find0' onkeyup='findman(0);'><select id='man0'><option>Choose A man</option></select><input type='button' value='Создать' onclick='validate();'></td>
	<td><input type='text' id='name1' name='name1' size='15' value=''></td>
	<td><input type='text' id='name2' name='name2' size='15' value=''></td>
	<td><input type='text' id='name3' name='name3' size='15' value=''></td>";

  $age =date_create("1900-01-01");
  echo "<td><input type='date' id='age' name='age' size='60' value='".date_format($age, 'Y-m-d')."'></td>
      <td><select id='amplua0' name='amplua' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>Вратарь</option> \n 
                                 <option value='4'>Тренер</option> \n 
                                 <option value='5'>Менеджер</option> \n 
                               </select></td>
      <td><select id='v9ky0' name='v9ky' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>V9KY</option> \n 
                               </select></td>
      <td><select id='dubler0' name='dubler' size=1>
                                 <option value='0' selected>нет</option> \n 
                                 <option value='1'>ДублерA</option> \n 
                                 <option value='2'>ДублерB</option> \n
                               </select></td>
      <td><select id='vibuv0' name='vibuv' size=1>
                                 <option value='0' selected>нi</option> \n 
                                 <option value='1'>Вибув</option> \n 
                               </select></td>
      <td>1</td>
	  <td></td>
      <td></td></tr> \n ";


  echo $myClass -> sel_all();


  echo"</table>";
?>
    <br><br>
</center>

</body>

</html>