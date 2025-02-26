<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title></title>

</head>

<body>
<center>
<?php

// The x and y coordinates on the original image where we
// will begin cropping the image, taken from the form
$x1    = $_POST['x1'];
$y1    = $_POST['y1'];
$x2    = $_POST['x2'];
$y2    = $_POST['y2'];
$w    = $_POST['w'];
$h    = $_POST['h'];   
$n    = $_POST['n'];  
$team    = 1*$_POST['team']; 

$face_path = $_SERVER['DOCUMENT_ROOT']."/preface/".$n; 
// Original image
$filename = $face_path;
//die(print_r($_POST));
$new_filename = $face_path;

// Get dimensions of the original image
list($current_width, $current_height) = getimagesize($filename);


// This will be the final size of the image
$crop_width = 150;
$crop_height = 200;

// Create our small image
$new = imagecreatetruecolor($crop_width, $crop_height);
// Create original image
$current_image = imagecreatefromjpeg($filename);
// resamling (actual cropping)
imagecopyresampled($new, $current_image, 0, 0, $x1, $y1, $crop_width, $crop_height, $w, $h);
// creating our new image
imagejpeg($new, $new_filename, 80);
?>
<img id="photo" src="http://v9ky.in.ua/preface/<?=$n?>" /><br><br>
Фото відправлено на модерацію. Пізніше з'явиться на сайті.

<a href="http://v9ky.in.ua/<?=$_POST['tournir']?>/team_page/id/<?=$team?>">Повернутися назад</a>
</center>
</body>
</html>