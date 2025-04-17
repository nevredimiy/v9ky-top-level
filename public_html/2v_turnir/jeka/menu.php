<?
if (!defined('READFILE'))
  {exit('Wrong way to file');};
ini_set('error_log', '/error_jeka.txt');
require_once('config.php');
$db->debug = false;

// Если пользователь не авторизовался - авторизуемся
//  if(!isset($_SERVER['PHP_AUTH_USER']) || (!empty($_GET['logout']) && $_SERVER['PHP_AUTH_USER'] == $_GET['logout']))
//  {
//    Header("WWW-Authenticate: Basic realm=\"Control Page\"");
//    Header("HTTP/1.0 401 Unauthorized");
//    exit();
//  }
//  else
//  {
    // Утюжим переменные $_SERVER['PHP_AUTH_USER'] и $_SERVER['PHP_AUTH_PW'],
    // чтобы мышь не проскочила
    if (!get_magic_quotes_gpc())
    {
      $_SERVER['PHP_AUTH_USER'] = mysql_real_escape_string($_SERVER['PHP_AUTH_USER']);
      $_SERVER['PHP_AUTH_PW']   = mysql_real_escape_string($_SERVER['PHP_AUTH_PW']);
    }

    $query = "SELECT * FROM system_users
              WHERE name = '".$_SERVER['PHP_AUTH_USER']."'";
	$recorduser = $db->Execute($query);

    // Если ошибка в SQL-запросе - выдаём окно
    if($recorduser->fields[pass]==0)
    {
      Header("WWW-Authenticate: Basic realm=\"Control Page\"");
      Header("HTTP/1.0 401 Unauthorized");
      exit();
    }

    // Если все проверки пройдены, сравниваем хэши паролей

    if((md5($_SERVER['PHP_AUTH_PW']."mFaFkFaFk")) !== $recorduser->fields[pass])
    {
      Header("WWW-Authenticate: Basic realm=\"Control Page\"");
      Header("HTTP/1.0 401 Unauthorized");
      exit();
    }
//  }


Print"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html lang='uk' xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />

<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' type='text/javascript'></script>
	<script src='uploadify/jquery.uploadify.min.js' type='text/javascript'></script>
	<link rel='stylesheet' type='text/css' href='uploadify/uploadify.css'>
        

<title>Админка</title>";

?>
<style type="text/css">
input, select {
    font-family:Arial, Helvetica, sans-serif;/* need to set for IE6/7 or it won't inhereit properly*/
}
input,span,select{
    display:inine-block;
    vertical-align:middle;
    font-size:13px;/* for demo purposes only*/
}
input,select{
    /*width:80px;*/
}
input{
    padding:0 10px;
    height:30px;/* uses normal box model */
}
select{
    padding:5px 2px 5px 10px;
    height:32px;/* uses broken box model*/
    /*width:160px;*/
    line-height:26px;/* safari doesn't use height but fiddle with line-height until it looks right*/
}
p.vertical{
  -webkit-transform: rotate(-90deg); 
  -moz-transform: rotate(-90deg);
  -ms-transform: rotate(-90deg);
  -o-transform: rotate(-90deg);
  transform: rotate(-90deg);
} 

</style>

</head><body>
<center>
<br>
<?
if ($recorduser->fields[permition]=="admin") echo"  <a href='man.php'>Люди</a>  ";
if ($recorduser->fields[permition]=="admin") echo"
<a href='team_show.php'>Команды</a>  <a href='city_show.php'>Города</a>  <a href='turnir_show.php'>Турниры</a>  ";

$query = "SELECT count(ID) as kol FROM v9ky_capzayavki WHERE stan = 0";
$recordnumd = $db->Execute($query);
$numd = $recordnumd->fields[kol];

$query = "SELECT count(ID) as kol FROM v9ky_capzapros";
$recordnumc = $db->Execute($query);
$numc = $recordnumc->fields[kol];

echo"<a href='match_show.php'>Матчи</a>";
if ($recorduser->fields[permition]=="admin") echo"<a href='capitan_show.php'><font color='red'>".$numc."</font>Запити фото</a>";
if ($recorduser->fields[permition]=="admin") echo"<a href='capzaya_show.php'><font color='red'>".$numd."</font>Дозаявки</a>";
if ($recorduser->fields[permition]=="admin") echo"<a href='sponsors_show.php'>Спонсоры</a>  ";
if ($recorduser->fields[permition]=="admin") echo"<a href='baners_show.php'>Банеры</a>  ";
if ($recorduser->fields[permition]=="admin") echo"<a href='loyalnost_show.php'>Програма лояльности</a>  ";
if ($recorduser->fields[permition]=="admin") echo"<a href='promotion_show.php'>Promotion</a>  ";
if ($recorduser->fields[permition]=="admin") echo"<a href='onlines.php'>Онлайн видео</a>  ";
if (($recorduser->fields[permition]=="admin")||($recorduser->fields[permition]=="zhyrnalist")) echo"<a href='news_show.php'>Новости</a>";
if ($recorduser->fields[permition]=="admin") echo"<a href='svodka_show.php'>Сводка</a>";
if ($recorduser->fields[permition]=="admin") echo"<a href='transfers.php'>Трансфери</a>";
if ($recorduser->fields[permition]=="admin") echo"  <a href='personal.php'>Персонал</a>  ";
if ($recorduser->fields[permition]=="admin") echo"<a href='refery.php'>Судьи</a>  ";
if ($recorduser->fields[permition]=="admin") echo"<a href='fields.php'>Поля</a>  ";
if (($recorduser->fields[name]=="jeka")||($recorduser->fields[name]=="maksm")) echo"<a href='glavbuh_show.php'>Бухгалтерія</a>  ";

echo"<br>
</center>";
?>