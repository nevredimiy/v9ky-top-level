<?
  //показать все команды в таблицу
  define('READFILE', true);
  require_once ("menu.php");
  echo "<center><h3>Турниры <a href='ratings_baly.php'>Вага ліг в рейтингу</a></h3>";
  require_once('config.php');

  //удаление выбранной команды
  if (isset($_GET['id']))
  {
  	 $id_to_delete=intval($_GET['id']);
	 //удаляем картинку

	 echo"Удален турнир №".$id_to_delete;
	 $db->Execute("delete from v9ky_turnir where id='".$id_to_delete."'");
   }

  //удаление выбранной команды
  if (isset($_GET['up'])) 
  {
  	 $turnir=intval($_GET['up']);
         $record1["updatet"] = gmdate('Y-m-d H:i:s');
         $db->AutoExecute('v9ky_turnir',$record1,'UPDATE', 'id = '.$turnir.'');
   } 

  $recordSet = $db->Execute("select * from v9ky_turnir where 1=1 ORDER BY active desc, name desc");   

  $total_rows=$recordSet1->fields[0];
  $num_pages=ceil($total_rows/$per_page);

  echo "<center><table cellspacing='2' border='1' cellpadding='5'><tr><td>ID</td><td>Название RU</td><td>Название<br>символьное</td><td><a href='seasons.php'>Сезон</a></td><td>Місто</td><td>Поле</td>
  <td>Вага в<br>рейтингу</td><td>Актив<br>ность</td><td>Вилетять<br>команд<br>з кінця<br>турнірки</td>
    <td>Очередь<br>в меню</td><td>Ціна<br>заявки</td><td>Ціна матчу<br>для команди</td><td>Обновлен<br>время по гринвичу</td><td>Тип</td><td><a href='turnir_update.php'>+</a></td><td> </td></tr>";
  while (!$recordSet->EOF) {
	$recordPole = $db->Execute("select name from v9ky_fields where id=".$recordSet->fields[field]."");
    $recordGorod = $db->Execute("select name_ua from v9ky_city where id=".$recordSet->fields[city]."");
    $recordVaga = $db->Execute("select name from v9ky_ratings_baly where id=".$recordSet->fields[ves]."");
    if ($recordSet->fields[seasons]>0) $color = " bgcolor='green' "; else $color="";
    print "<tr><td>".$recordSet->fields[0]."</td><td>".$recordSet->fields[ru]."</td><td>".$recordSet->fields[1]."</td><td".$color.">".$recordSet->fields[2]."</td>
	<td>".$recordGorod->fields[name_ua]."</td><td>".$recordPole->fields[name]."</td><td>".$recordVaga->fields[name]."</td><td>".$recordSet->fields[active]."</td><td>".$recordSet->fields[niz_turnirki]."</td><td>".$recordSet->fields[priority]."</td><td>".$recordSet->fields[zayavka_price]."</td><td>".$recordSet->fields[team_match_price]."</td><td><a href='turnir_show.php?up=".$recordSet->fields[0]."'>".$recordSet->fields[updatet]."</a></td><td>";
    
    switch ($recordSet->fields[cup]) {
    case 0:
        echo "чемп1";
        break;
    case 1:
        echo "кубок";
        break;
    case 2:
      echo "чемп2";
      break;
    }
    print "</td><td><a href='turnir_update.php?id=".$recordSet->fields[0]."'>Edit</a></td><td>";
    //<a href='turnir_show.php?id=".$recordSet->fields[0]."'>
    echo"Delete</a></td>
	</tr> \n";
    $recordSet->MoveNext();
}
echo"</table></center>";
if ($id_to_select>0) {$id_to_selectt="&selid=".$id_to_select;}else{$id_to_selectt="";}
for($i=1;$i<=$num_pages;$i++) {
  if ($i == $page) {
    echo $i." ";
  } else {
    echo "<a href='".$_SERVER['PHP_SELF']."?page=".$i.$id_to_selectt."'>".$i."</a> ";
  }
}
?>