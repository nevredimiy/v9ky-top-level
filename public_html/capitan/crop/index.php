<?
define('READFILE', true);
include_once "../../config.php";

// загрузка фото капитанами
function genRandomString() {
     $length = 12;
     $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
     for ($p = 0; $p < $length; $p++) {
         $string .= $characters[mt_rand(0, strlen($characters))];
		 
     }	 	 
    return $string;
  }
  $man = 1*$_POST['man'];
  $team = 1*$_POST['team'];
  $tournir = htmlspecialchars($_POST['tour']);
  $tournir = mysql_real_escape_string($tournir);

  $valid_types = array(".gif",".jpg", ".png", ".jpeg", ".JPG", ".PNG",);
  $max_image_width	= 10000;
  $max_image_height	= 10000;
  $max_image_size	= 10 * 1024 * 1024;     

              if (@$_FILES['rule_picture']['name'])
			  {
                 $errortext=array(0 => 'Файл загружен.', 1 => 'Превышение размеров допускаемых сервером.', 
                 2 => 'Превышение разрешенного формой размера', 3 => 'Загрузка не завершена.', 
                 4 => 'Файл не загружен. Вероятно указан неверный путь.');

                 if (@is_uploaded_file($_FILES['rule_picture']['tmp_name'])) 
				 {	 
					 //уникальное имя файла картинки
				    $ext = substr($_FILES['rule_picture']['name'], strrpos($_FILES['rule_picture']['name'], "."));
                    $ext = strtolower($ext);
                    
                    $a=genRandomString();
					$a = $man."_".$a;
		            
			        $face_path = $_SERVER['DOCUMENT_ROOT']."/preface/";
                    $newfilename = $face_path.$a.''.$ext.'';
                    if (filesize($_FILES['rule_picture']['tmp_name']) > $max_image_size) 
					{
			           exit("Error: File size > $max_image_size байт");
		             } elseif (!in_array($ext, $valid_types)) 
					{
			           exit('Error: Invalid file type.');
		             } else 
					{
 		               $size = GetImageSize($_FILES['rule_picture']['tmp_name']);
 		               if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)
                          && ($size[0] > 1) && ($size[1] > 1)) 
					   {
                          if (! move_uploaded_file($_FILES['rule_picture']['tmp_name'], $newfilename))
						  {
                            exit('<p>Файл загружен, но не перемещен');
                           }else{
                              echo '<center><H2>Фото завантажено. Виділіть необхідну ділянку фото та натисніть нижню кнопку.</h2></center>';
                              //$record["pict"] = $a.$ext;
		              //$record["man"] = $man;
		              //$db->AutoExecute('v9ky_man_face',$record,'INSERT');
                              $record1["date"] = time()+(3*60*60);
                              $record1["face"] = $a.$ext;
		              $record1["man"] = $man;
                              $record1["zapit"] = 1;
                              $record1["team"] = $team;
                              $db->AutoExecute('v9ky_capzapros',$record1,'INSERT');
							  
							}
						}else {echo "Великий розмір зображення > ".$max_image_width."x".$max_image_height;}
					 }
				 }
			  }
// загрузка фото капитанами
?>


﻿<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title></title>
<link rel="stylesheet" type="text/css" href="default.css">
<link rel="stylesheet" type="text/css" href="imgareaselect-default.css"> 
</head>

<body>
<center>
<br>
<div id="coords"> </div>
<a href="http://v9ky.in.ua/<?=$_POST['tour']?>/team_page/id/<?=$team?>">Повернутися назад</a>

<p>Фото потрібно завантажвати у форматі jpg.<br>
Далі обов'язково необхідно виділити за домомогою рамки потрібну ділянку фото та натисниту нижню кнопку.<br>
Яцщо все було зроблено вірно. Протягом 24 годин фото з'явиться на сайті.<br>
Якщо після натиснення кнопки ОБРІЗАТИ ТА ВІДПРАВИТИ ви побачили чорний прямокутник,<br>
значить було допущена помилка (фото не відповідає формату jpg або невикористовувалася рамка виділення перед відправкою) і фото не буде відображено сайті.<br>
Завантажувати фото можна лише з компьютера. Зі смартфонів дана функція зараз не працює.
</p>

<div id="image_preview"><img id="photo" src="http://v9ky.in.ua/preface/<?=$a.$ext?>" /></div>
<div id="message"></div>
    
        
<form action="crop.php" method="post">

    <input type="hidden" name="n" value="<?=$a.$ext?>" />
    <input type="hidden" name="x1" value="" />
    <input type="hidden" name="y1" value="" />
    <input type="hidden" name="x2" value="" />
    <input type="hidden" name="y2" value="" />
    <input type="hidden" name="w" value="" />
    <input type="hidden" name="h" value="" />
    <input type="hidden" name="team" value="<?=$team?>">
    <input type="hidden" name="tournir" value="<?=$tournir?>">
    <input type="submit" value="Обрізати та відправити" style="font-size:20;" />
</form>
<br>
</center>
<script type="text/javascript" src="jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="jquery.imgareaselect.pack.js"></script>
<script type="text/javascript"> 
function preview(img, selection) {
    var scaleX = 100 / (selection.width || 1);
    var scaleY = 100 / (selection.height || 1);
    $('#photo + div > img').css({
        width: Math.round(scaleX * 600) + 'px',
        height: Math.round(scaleY * 400) + 'px',
        marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
        marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
    });
} 

$(document).ready(function () { 
    
    $('#photo').imgAreaSelect({
        aspectRatio: '3:4',
        x1: 0, y1: 0, x2: 150, y2: 200,
        handles: true,
        onSelectChange: preview,
        onSelectEnd: function ( image, selection ) {
            $('input[name=x1]').val(selection.x1);
            $('input[name=y1]').val(selection.y1);
            $('input[name=x2]').val(selection.x2);
            $('input[name=y2]').val(selection.y2);
            $('input[name=w]').val(selection.width);
            $('input[name=h]').val(selection.height);
        }
    });



    // Function to preview image after validation
$(function() {



$("#file").change(function() {
$("#message").empty(); // To remove the previous error message
var file = this.files[0];
var imagefile = file.type;
var match= ["image/jpeg","image/png","image/jpg"];
if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
{
$('#photo').attr('src','photo.jpg');
$("#message").html("<p id='error'>Please Select A valid Image File</p>"+"<h4>Note</h4>"+"<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
return false;
}
else
{
var reader = new FileReader();
reader.onload = function(event) {
    
    var dataUri = event.target.result;
        document.getElementById('photo').src=dataUri;
        document.getElementById('photo').width=600;
 $("#message").html(dataUri);
    
};
reader.readAsDataURL(this.files[0]);
}
});
});


}); 
</script>