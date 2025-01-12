<?
  define('READFILE', true);
  require_once ("menu.php");
  echo "<center><h3>Поля</h3>";
  require_once('config.php');

  //удаление выбранного города
  if (isset($_GET['id']))
  {
  	 $id_to_delete=intval($_GET['id']);

	 echo"Видалено поле №".$id_to_delete;
	 $db->Execute("delete from v9ky_fields where id='".$id_to_delete."'");
   }

   if (isset($_GET['selid']))
  {
  	 $id_to_select=intval($_GET['selid']);
	 echo"Вы выбрали задания к правилу ".$id_to_select."<br><br>";
	 $recordSet = $db->Execute("select * from v9ky_fields where id='".$id_to_select."' ORDER BY name");
   } else {
      $recordSet = $db->Execute("select * from v9ky_fields order by id desc");
   }

  echo "<center><table cellspacing='2' border='1' cellpadding='5'>";
  echo "<tr>";
  echo "<td>ID</td><td>Назва</td><td>Місто</td><td>Адреса</td><td>Фото</td><td>Поле 40х20 (шт.)</td><td>Поле 60х40 (шт.)</td>";
  echo "<td>Парковка (0/1)</td><td>Душ (0/1)</td><td>Гучномовець (0/1)</td><td>Гардероб (0/1)</td><td>Туалет (0/1)</td><td>Видимість</td><td>Пріоритет</td><td></td>";
  echo "<td><a href='field_update.php'>+</a></td>";
  echo "</tr>";
  if ($recordSet>0){
  while (!$recordSet->EOF) {
    $recordGorod = $db->Execute("select name_ua from v9ky_city where id=".$recordSet->fields[city]."");
    
	
	
    print "<tr><td>".$recordSet->fields[id]."</td>
      <td>".$recordSet->fields[name]."</td>
      <td>".$recordGorod->fields[name_ua]."</td>
      <td>".$recordSet->fields['adres']."</td>
      <td>".$recordSet->fields['photo']."</td>
      <td>".$recordSet->fields['fields_40x20']."</td>
      <td>".$recordSet->fields['fields_60x40']."</td>
      <td>".$recordSet->fields['parking']."</td>
      <td>".$recordSet->fields['shower']."</td>
      <td>".$recordSet->fields['loudspeaker']."</td>
      <td>".$recordSet->fields['cloakroom']."</td>
      <td>".$recordSet->fields['toilet']."</td>
      <td>".$recordSet->fields['visible']."</td>
      <td>".$recordSet->fields['priority']."</td>";
    print "<td><a href='field_update.php?id=".$recordSet->fields[id]."'>Edit</a></td>";

    $txt1 = "Видалити поле = ".$recordSet->fields[name]."?";
    if ($recorduser->fields[permition]=="admin"){
      echo "<td><a href='fields.php?id=".$recordSet->fields[id]."' "; 
?>
onclick='return confirm("<? echo $txt1; ?>")' >
<?
    echo "Delete</a></td>";}

	echo"</tr> \n";
    $recordSet->MoveNext();
}}
echo"</table></center>";
?>