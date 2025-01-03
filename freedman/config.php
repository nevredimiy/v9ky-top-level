<?php

define("ROOT", dirname(__DIR__));
define("HOME", ROOT . '/public_html');
define("FREEDMAN", ROOT . '/freedman');
define("CORE", FREEDMAN . '/core');
define("APP", FREEDMAN . '/app');
define("CONTROLLERS", APP . '/controllers');
define("VIEWS", APP . '/views');
define("PATH", 'https://v9ky.in.ua');
define("PHOTO", HOME . '/2v_turnir/photo');
define("PHOTO_URL", PATH . '/2v_turnir/photo');
define("IMAGES", PATH . '/freedman/assets/images');
define("CSS", PATH . '/freedman/assets/css');
define("JS", PATH . '/freedman/assets/js');
define("ADMIN", FREEDMAN . '/admin');
define("ADMINCONTROLLERS", ADMIN . '/app/controllers');
define("ADMINVIEWS", ADMIN . '/app/views');
define("ACTIONS", APP . '/actions');


$site_url = "https://v9ky.in.ua";
$team_logo_path = "https://v9ky.in.ua/2v_turnir/team_logo/";
$player_face_path = "https://v9ky.in.ua/face/";


$mysqli = new PDO('mysql:host=localhost;dbname=corsa134_v9kyv2', 'corsa134_v9kyv2', '2pnr5p92', [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
  ]);
 