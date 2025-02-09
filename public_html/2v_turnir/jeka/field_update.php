<?PHP
define('READFILE', true);
require_once ("menu.php");



if(!defined('UPLOADS')){ 
 define("UPLOADS", dirname(__DIR__) . '/images');
}

$field_id = 0;
$errors = [];

// Текст кнопки submit
( isset($_GET['id']) || isset($_POST['id']) ) ? $btn_text = 'Змінити дані стадіону' : $btn_text = 'Додати стадіон';


if(isset($_GET['id'])){
  
  $field_id = intval($_GET['id']);
// Получаем из БД данные поля по идентификатору
  $field_data = $db->Execute("select * from v9ky_fields where id='".$field_id."'");

  $name = stripcslashes($field_data->fields['name']);
  $city = stripcslashes($field_data->fields['city']);
  $adres = stripcslashes($field_data->fields['adres']);
  $photo = stripcslashes($field_data->fields['photo']);
  $fields_40x20 = stripcslashes($field_data->fields['fields_40x20']);
  $fields_60x40 = stripcslashes($field_data->fields['fields_60x40']);
  $parking = stripcslashes($field_data->fields['parking']);
  $shower = stripcslashes($field_data->fields['shower']);
  $loudspeaker = stripcslashes($field_data->fields['loudspeaker']);
  $cloakroom = stripcslashes($field_data->fields['cloakroom']);
  $toilet = stripcslashes($field_data->fields['toilet']);
  $latitude = stripcslashes($field_data->fields['latitude']);
  $longitude = stripcslashes($field_data->fields['longitude']);
  $visible = stripcslashes($field_data->fields['visible']);
  $priority = stripcslashes($field_data->fields['priority']);
 
}

// Проверка, что форма была отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Получение данных из формы
  $name = isset($_POST['name']) && !empty($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
  $city = isset($_POST['city']) ? intval($_POST['city']) : 2; // 2 - Киев
  $adres = isset($_POST['adres']) && !empty($_POST['adres']) ? htmlspecialchars($_POST['adres']) : ''; 
  $fields_40x20 = isset($_POST['fields_40x20']) && !empty($_POST['fields_40x20']) ? intval($_POST['fields_40x20']) : 0;
  $fields_60x40 = isset($_POST['fields_60x40']) && !empty($_POST['fields_60x40']) ? intval($_POST['fields_60x40']) : 0;
  $parking = isset($_POST['parking']) && !empty($_POST['parking']) ? intval($_POST['parking']) : 0;
  $shower = isset($_POST['shower']) && intval($_POST['shower']) > 0 ? 1 : 0;
  $loudspeaker = isset($_POST['loudspeaker']) && intval($_POST['loudspeaker']) > 0 ? 1 : 0;
  $cloakroom = isset($_POST['cloakroom']) && !empty($_POST['cloakroom']) ? intval($_POST['cloakroom']) : 0;
  $toilet = isset($_POST['toilet']) && intval($_POST['toilet']) > 0 ? 1 : 0;
  $visible = isset($_POST['visible']) && intval($_POST['visible']) > 0 ? 1 : 0;
  $priority = isset($_POST['priority']) && !empty($_POST['priority']) ? intval($_POST['priority']) : 0;
  $latitude = trim($_POST['latitude']);
  $longitude = trim($_POST['longitude']);

  // Проверка, что данные являются числами
  if (!is_numeric($latitude)) {
       $errors['latitude'] = 'Неверный формат координат. Введите числа.';
  }

  if (!is_numeric($longitude)) {
    $errors['longitude'] = 'Неверный формат координат. Введите числа.';
}

   // Приведение данных к допустимому диапазону
  if ($latitude < -90 || $latitude > 90) {
       isset($errors['latitude']) ? $errors['latitude'] .= ' Широта должна быть в диапазоне от -90 до 90.' : $errors['latitude'] = 'Широта должна быть в диапазоне от -90 до 90.';
      }
      
  if ($longitude < -180 || $longitude > 180) {
        isset($errors['longitude']) ? $errors['longitude'] .= ' Долгота должна быть в диапазоне от -180 до 180.' : $errors['longitude'] = 'Долгота должна быть в диапазоне от -180 до 180.';
   }


  // Обработка загрузки изображения
  // Убеждаемся, что файл был отправлен
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    // Параметры файла
    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileName = $_FILES['photo']['name'];
    $fileSize = $_FILES['photo']['size'];
    $fileType = $_FILES['photo']['type'];

    // Разрешённые типы файлов
    $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fileType, $allowedTypes)) {
        $errors['photo'] = 'Недопустимый тип файла. Разрешены только JPG, JPEG, PNG, GIF.';
    }

    // Проверка размера файла (максимум 5 МБ)
    if ($fileSize > 5 * 1024 * 1024) {
        isset($errors['photo']) ? $errors['photo'] .= ' Файл слишком большой. Максимальный размер: 5 МБ.' : $errors['photo'] = 'Файл слишком большой. Максимальный размер: 5 МБ.';
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
    $fileUrl = '2v_turnir/images' . $uploadDir . $newFileName;

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
    $photo = isset($photo) && !empty($photo) ? $photo : '';
  }

  // Подготовка массива к записи в БД
  $record["name"] = $name;
  $record["city"] = $city;
  $record["adres"] = $adres;

  if($photo != '') {
    $record["photo"] = $photo;
  }

  $record["fields_40x20"] = $fields_40x20;
  $record["fields_60x40"] = $fields_60x40;
  $record["parking"] = $parking;
  $record["shower"] = $shower;
  $record["loudspeaker"] = $loudspeaker;
  $record["cloakroom"] = $cloakroom;
  $record["toilet"] = $toilet;
  $record["latitude"] = $latitude;
  $record["longitude"] = $longitude;

  $record["visible"] = $visible;
  $record["priority"] = $priority;

  //Если нет ошибок делаем запись в БД
  if(empty($errors)) {
    // Делаем изминения в БД.
    if($_POST['field_id'] > 0){

      // Обновляем данные
      $field_id = intval($_POST['field_id']);
      $db->AutoExecute('v9ky_fields',$record,'UPDATE', 'id = '.$field_id.'');
      $message = "Дані стадіона оновлено!";
    } else {
      
      // Добавляем новую запись
      $db->AutoExecute('v9ky_fields',$record,'INSERT');
      $message = "Запис успішно додано!";
    }
  }
  

} 
?>
<div class="bootstrap">

<h1 class="title">Редагування поля</h1>
    <form class="mx-auto w-m-content d-grid md-col-2" action="" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="name">Назва <span style="color: red">*</span>:</label>
        <input class="form-control" type="text" name="name" maxlength="30" value="<?= $name ?>" required>
      </div>
      <div class="mb-3">
        <label for="city">Місто:</label>
          <select name='city' size=1> 
            <?php 
                $cities = $db->Execute("select * from v9ky_city ORDER BY name_ua");
                while (!$cities->EOF)
                {
                  if ($cities->fields['id']==$city) print "<option value='".$cities->fields['id']."' selected>".$cities->fields['name_ua']."</option> \n";
                  else print "<option value='".$cities->fields['id']."'>".$cities->fields['name_ua']."</option> \n";
                  $cities->MoveNext();
                }
            ?>
        </select>
      </div>       

      <div class="mb-3">
        <label for="adres">Адреса:</label>
        <input class="form-control" type="text" name="adres" maxlength="30" value="<?= $adres ?>" >
      </div>        

      <div class="mb-3">
        <?php if(isset($field_data->fields['photo'])): ?>
          <img width="64" height="48" src="https://v9ky.in.ua/<?=$photo?>" alt="<?= $name ?>">
        <?php endif ?>          
        <label for="photo">Фото:</label>
        <input  type="file" name="photo" accept="image/jpeg" value="<?= $photo ?>" >
        <?php if (isset($errors['photo'])) : ?>
            <p style="color:red"><?= $errors['photo'] ?></p>
        <?php endif ?>  
      </div>

      <div class="mb-3">
        <label for="fields_40x20">Поле 40x20:</label>
        <input class="form-control" type="number" name="fields_40x20" min="0" value="<?= $fields_40x20 ?>" >
        <p class="description">Кількість полів 40x20</p>
      </div>

        <div class="mb-3">
          <label for="fields_60x40">Поле 60x40:</label>
          <input class="form-control" type="number" name="fields_60x40" min="0" value="<?= $fields_60x40 ?>" >
          <p class="description">Кількість полів 60x40</p>
        </div>

        <div class="mb-3">
          <label for="parking">Парковка:</label>
          <input class="form-control" type="number" name="parking" min="0" value="<?= $parking ?>" >
          <p class="description">Кількість парковочних місць</p>
        </div>

        <div class="mb-3">
          <label for="shower">Душ:</label>
          <input class="form-control" type="number" name="shower" min="0" max="1" value="<?= $shower ?>" >
          <p class="description">0 - немає, 1 - є</p>
        </div>

        <div class="mb-3">
          <label for="loudspeaker">Гучномовець:</label>
          <input class="form-control" type="number" name="loudspeaker" min="0" max="1" value="<?= $loudspeaker ?>">
          <p class="description">0 - немає, 1 - є</p>
        </div>

        <div class="mb-3">
          <label for="cloakroom">Гардеробна:</label>
          <input class="form-control" type="number" name="cloakroom" min="0" value="<?= $cloakroom ?>">
          <p class="description">Кількість гардеробних</p>
        </div>

        <div class="mb-3">
          <label for="toilet">Туалет:</label>
          <input class="form-control" type="number" name="toilet" min="0" max="1" value="<?= $toilet ?>">
          <p class="description">0 - немає, 1 - є</p>
        </div>

        <div class="mb-3">
          <label for="latitude">Широта:</label>
          <input class="form-control" id="latitude" type="text" name="latitude" value="<?= $latitude ?>">
          <p class="description">Приклад: 90.00000</p>
          <?php if (isset($errors['latitude'])) : ?>
            <p style="color:red"><?= $errors['latitude'] ?></p>
            <?php endif ?>
        </div>

        <div class="mb-3">
          <label for="longitude">Довгота:</label>
          <input class="form-control" id="longitude" type="text" name="longitude" value="<?= $longitude ?>">
          <p class="description">Приклад: 90.00000</p>
          <?php if (isset($errors['longitude'])) : ?>
            <p style="color:red"><?= $errors['longitude'] ?></p>
          <?php endif ?>          
        </div>

        <div class="mb-3">
          <label for="visible">Видимість <span style="color: red">*</span>:</label>
          <input class="form-control" type="number" name="visible" min="0" max="1" value="<?= $visible ?>" required>
          <p class="description">0 - невидимий, 1 - видимий</p>
        </div>

        <div class="mb-3">
          <label for="priority">Пріоритет:</label>
          <input class="form-control" type="number" name="priority" min="0" max="255" value="<?= $priority ?>" >
          <p class="description">0 - самий високий пріоритет</p>
        </div>

        <input type="hidden" name="field_id" value="<?= $field_id ?>" >

        <div class="">
          <?php if($message != '') :?>
            <p style="color: green; font-weight: 600"><?=$message?></p>
          <?php endif ?>
          <div class="d-flex items-center">
            <button class="btn btn-main" type="submit"><?=$btn_text?></button>
          </div>
          
          <?php if($btn_text == 'Змінити дані стадіону') : ?>
          <div class="d-flex items-center">
            <a class="btn" href="field_update1.php">Перейти до створення стадіон</a>
          </div>
          <?php endif ?>

          <div class="d-flex items-center">
            <a class="btn" href="fields.php">Повернутися до списку стадіонів</a>
          </div>
        </div>

    </form>

  </div>
</body>
</html>