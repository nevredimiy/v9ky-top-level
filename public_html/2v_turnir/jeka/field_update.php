<?PHP
define('READFILE', true);
require_once ("menu.php");
?>

<center>
<h1>Редагування поля</h1>
<form action="" method="POST" ENCTYPE='multipart/form-data'>
<?


if(!defined('UPLOADS')){ 
 define("UPLOADS", dirname(__DIR__) . '/images');
}


// Записываем фото стадиона
// Убеждаемся, что файл был отправлен
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
  // Параметры файла
  $fileTmpPath = $_FILES['photo']['tmp_name'];
  $fileName = $_FILES['photo']['name'];
  $fileSize = $_FILES['photo']['size'];
  $fileType = $_FILES['photo']['type'];

  // Разрешённые типы файлов
  $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!in_array($fileType, $allowedTypes)) {
      die('Недопустимый тип файла. Разрешены только JPEG, PNG, GIF.');
  }

  // Проверка размера файла (максимум 5 МБ)
  if ($fileSize > 5 * 1024 * 1024) {
      die('Файл слишком большой. Максимальный размер: 5 МБ.');
  }

  // Генерация уникального имени файла
  $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
  $newFileName = uniqid('photo_', true) . '.' . $fileExtension;

  // Директория для сохранения
  $uploadDir = '/fields/';
  if (!is_dir(UPLOADS . $uploadDir)) {
      mkdir(UPLOADS . $uploadDir, 0755, true);
  }
  

  // Путь для сохранения файла
  $destPath = UPLOADS . $uploadDir . $newFileName;

    // Путь файла url
  $fileUrl = '2v_turnir' . $uploadDir . $newFileName;

  // Перемещение файла в указанную директорию
  if (move_uploaded_file($fileTmpPath, $destPath)) {
      // переменная для отображения результата загрузки фото
      $response_file_upload = 'Фото стадіона успішно завантажено.';
      // Сохранение информации о файле в переменной для  базы данных
      $photo = $fileUrl;    
  } else {
    $response_file_upload = 'Помилка під час завантаження фото стадіона';
  }
} else {
  $photo = '2v_turnir/images/fields/stadium-photo.jpeg';
}
echo 'post'. "<br>";
var_dump($_POST);
echo "<br>" . 'get' . "<br>";
var_dump($_GET);
echo "<br>";

if ((!empty($_GET))){

    
    if (isset($_GET['id'])) {
      $city_id = intval($_GET['id']);
    } elseif (isset($_POST['id'])) {
      $city_id = intval($_POST['id']);
    } else {
      $city_id = 0;
    

    if (isset($_POST['city'])) $city=intval($_POST['city']);
    if (isset($_POST['name'])) {$name=filter_string($_POST['name']);
      } ELSE {$name="";}
    if (isset($_POST['adres'])) {$adres=filter_string($_POST['adres']);
      } ELSE {$adres="";}
    
    if (isset($_POST['fields_40x20'])) {$fields_40x20=intval($_POST['fields_40x20']);} else $fields_40x20=0;
    if (isset($_POST['fields_60x40'])) {$fields_60x40=intval($_POST['fields_60x40']);} else $fields_60x40=0;
    if (isset($_POST['parking'])) {$parking=intval($_POST['parking']);} else $parking=0;
    if (isset($_POST['shower'])) {$shower=intval($_POST['shower']);} else $shower=0;
    if (isset($_POST['loudspeaker'])) {$loudspeaker=intval($_POST['loudspeaker']);} else $loudspeaker=0;
    if (isset($_POST['cloakroom'])) {$cloakroom=intval($_POST['cloakroom']);} else $cloakroom=0;
    if (isset($_POST['toilet'])) {$toilet=intval($_POST['toilet']);} else $toilet=0;
    if (isset($_POST['visible'])) {$visible=intval($_POST['visible']);} else $visible=0;
    if (isset($_POST['priority'])) {$priority=intval($_POST['priority']);} else $priority=0;

    $record["name"] = $name;
    $record["city"] = $city;
    $record["adres"] = $adres;

    $record["photo"] = $photo;
    $record["fields_40x20"] = $fields_40x20;
    $record["fields_60x40"] = $fields_60x40;
    $record["parking"] = $parking;
    $record["shower"] = $shower;
    $record["loudspeaker"] = $loudspeaker;
    $record["cloakroom"] = $cloakroom;
    $record["toilet"] = $toilet;
    
    $record["visible"] = $visible;
    $record["priority"] = $priority;
      
	//запись в базу
	if (isset($_POST['red'])) {$redatirovat_or_else=intval($_POST['red']);
        } ELSE {$redatirovat_or_else=0;}
	if ($redatirovat_or_else==1)
	  {
    	 $db->AutoExecute('v9ky_fields',$record,'UPDATE', 'id = '.$city_id.'');
	  }else {$db->AutoExecute('v9ky_fields',$record,'INSERT');}
  }
  $name="";
  $site="";
  $adres="";
  
  $visible=1;
  $priority=0;

  if ((isset($_GET['id']))&&(intval($_GET['id'])*1>0)&&(($redatirovat_or_else==1)||(!isset($_GET['red']))))
  {
  	$id_to_update=intval($_GET['id'])*1;
	$recordSet1 = $db->Execute("select * from v9ky_fields where id='".$id_to_update."'");
  }else {
    $recordSet1 = $db->Execute("select * from v9ky_fields where id=(SELECT LAST_INSERT_ID())");
    $id_to_update=$recordSet1->fields['id'];
  }

	 $recordSet1 = $db->Execute("select * from v9ky_fields where id='".$id_to_update."'");
	 
	 echo'Название: <input type="text" name="name" size="40" value="'.stripcslashes($recordSet1->fields[name]).'"> <br><br>';
         echo"Город: <select name='city' size=1> ";
	 $recordSet3 = $db->Execute("select * from v9ky_city ORDER BY name_ua");
	 while (!$recordSet3->EOF)
	 {
		 if ($recordSet3->fields[id]==$recordSet1->fields[city]) print "<option value='".$recordSet3->fields[id]."' selected>".$recordSet3->fields[name_ua]."</option> \n";
		 else print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[name_ua]."</option> \n";
		 $recordSet3->MoveNext();
	 }
	 echo"</select><br><br>";
         echo'Адреса: <input type="text" name="adres" size="40" value="'.stripcslashes($recordSet1->fields[adres]).'"><br><br>';
         echo '<img width="48" height="32" src="https://v9ky.in.ua/'. stripcslashes($recordSet1->fields['photo']) .'" alt="">';
         echo 'Фото: <input type="file" name="photo" id="photo" accept="image/*" ><br><br>';
         echo'Поля 40 на 20: <input type="number" name="fields_40x20" size="1" min="0" max="100" value="'.stripcslashes($recordSet1->fields['fields_40x20']).'"><br><br>';
         echo'Поля 60 на 40: <input type="number" name="fields_60x40" size="1" min="0" max="100" value="'.stripcslashes($recordSet1->fields['fields_60x40']).'"><br><br>';
         echo'Наявність парковки. 1 - є парковка; 0 - немає: <input type="number" name="parking" min="0" max="100" value="'.stripcslashes($recordSet1->fields['parking']).'"><br><br>';
         echo'Наявність душу. 1 - є душ; 0 - немає: <input type="number" name="shower" min="0" max="1" value="'.stripcslashes($recordSet1->fields['shower']).'"><br><br>';
         echo'Наявність гучномовця. 1 - є гучномовець; 0 - немає: <input type="number" name="loudspeaker" min="0" max="1" value="'.stripcslashes($recordSet1->fields['loudspeaker']).'"><br><br>';
         echo'Наявність гардеробу. 1 - є гардероб; 0 - немає: <input type="number" name="cloakroom" min="0" max="1" value="'.stripcslashes($recordSet1->fields['cloakroom']).'"><br><br>';
         echo'Наявність туалету. 1 - є туалет; 0 - немає: <input type="number" name="toilet" min="0" max="1" value="'.stripcslashes($recordSet1->fields['toilet']).'"><br><br>';
         echo'Видимость в редакторе матча = 1; невидим = 0: <input type="text" name="visible" min="0" max="1" value="'.stripcslashes($recordSet1->fields[visible]).'"><br><br>';
         echo'Очередность в календаре: <input type="text" name="priority" min="0" max="1" value="'.stripcslashes($recordSet1->fields[priority]).'"><br><br>';
	 echo "<br><br>";
     
	 echo"<br> <br><input type='submit' value='  Изменить  '><input type='radio' name='red' value='1' checked>
	 Внести изменения в поле ".stripcslashes($recordSet1->fields['name'])."<input type='radio' name='red' value='0' >
	 Добавить как новый<input type='hidden' name='id' value='".$recordSet1->fields[id]."'></form> ";
	 if (isset($_POST['red'])){ echo "Поле: <H2> ".$recordSet1->fields['name']." </H2> изменения приняты. " . $response_file_upload . " Имя файла - " . $photo;}
}else {
  print"Название: <input type='text' name='name' size='100' ><br><br>";
  echo"Город: <select name='city' size=1> ";
	 $recordSet3 = $db->Execute("select * from v9ky_city ORDER BY name_ua");
	 while (!$recordSet3->EOF)
	 {
            print "<option value='".$recordSet3->fields[id]."'>".$recordSet3->fields[name_ua]."</option> \n";
	    $recordSet3->MoveNext();
	 }
	 echo"</select><br><br>";
         echo'Адреса: <input type="text" name="adres" size="40"><br><br>';
         echo 'Фото: <input type="file" name="photo" id="photo" accept="image/*" ><br><br>';
         echo'Поля 40 на 20: <input type="number" name="fields_40x20" min="0" max="100"><br><br>';
         echo'Поля 60 на 40: <input type="number" name="fields_60x40" min="0" max="100"><br><br>';
         echo'Наявність парковки. 1 - є парковка; 0 - немає: <input type="number" name="parking" min="0" max="1"><br><br>';
         echo'Наявність душу. 1 - є душ; 0 - немає: <input type="number" name="shower" min="0" max="1"><br><br>';
         echo'Наявність гучномовця. 1 - є гучномовець; 0 - немає: <input type="number" name="loudspeaker" min="0" max="1"><br><br>';
         echo'Наявність гардеробу. 1 - є гардероб; 0 - немає: <input type="number" name="cloakroom" min="0" max="1"><br><br>';
         echo'Наявність туалету. 1 - є туалет; 0 - немає: <input type="number" name="toilet" min="0" max="1"><br><br>';
        
         echo'Видимость в редакторе матча = 1; невидим = 0: <input type="text" name="visible" size="1"><br><br>';
         echo'Очередность в календаре: <input type="text" name="priority" size="1"><br><br>';
  echo "<input type='hidden' name='red' value='0'><br><br>
    <input type='submit' value='  Создать  '>
    </form>  ";
}
?>


</center>
</body>
</html>