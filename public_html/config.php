<?
//проверка на вшивость
if (!defined('READFILE')) {exit('Wrong way to file');};

$items_num=5;
$date=date("d.m.Y"); // ?????.?????.???
$time=date("H:i:s"); // ????:??????:???????
$pict_path="../team_logo/"; //хранилище картинок
$gerb_path="../city_gerb/"; //хранилище гербов городов
$baner_path="https://v9ky.in.ua/2v_turnir/baners/"; //хранилище банеров спонсоров
$banerz_path="https://v9ky.in.ua/baner/"; //хранилище банеров спонсоров
$site_url = "https://v9ky.in.ua";
$team_logo_path = "https://v9ky.in.ua/2v_turnir/team_logo/";
$player_face_path = "https://v9ky.in.ua/face/";
$photo_papka = "2v_turnir/";
$reglament_path="https://v9ky.in.ua/reglamenty/";

Error_Reporting(E_ALL & ~E_NOTICE);

//error_reporting(E_ALL); # report all errors
//ini_set("display_errors", "0"); # but do not echo the errors
define('ADODB_ERROR_LOG_TYPE',3);
//define('ADODB_ERROR_LOG_DEST','/sql_errors.txt');
//include('adodb5/adodb-errorhandler.inc.php');
//include('adodb5/adodb.inc.php');
//include('adodb5/tohtml.inc.php');

 
  $dblocation = "localhost";
  // ??? ???? ??????, ?? ???????? ??? ????????? ??????
  $dbname = "corsa134_v9kyv2";
  $dbuser = "corsa134_v9kyv2";
  $dbpasswd = "2pnr5p92";



   include('adodb5/adodb.inc.php');
   //$ADODB_CACHE_DIR = 'ADODB_cache';
   $db = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
   $db->debug = false;
   $db->Connect($dblocation, $dbname, $dbpasswd, $dbuser);
   if (!$db) die("Connection failed");  
   $db->SetCharSet('utf8');
   $db->LogSQL(true); // turn on logging
   //????????? ?????????? ??????

    $mysqli = new PDO('mysql:host=localhost;dbname=corsa134_v9kyv2', 'corsa134_v9kyv2', '2pnr5p92', [
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
    ]);
    if ($mysqli->connect_error) {
      die('Ошибка подключения: ' . $mysqli->connect_error);
    }

?>