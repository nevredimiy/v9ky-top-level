<?php
define('TGKEY', '1754326106:AAF8LFoliaFRY9COd2J5bm0qaxNGumzoUAk');
include_once(HOME .'telegramj.php');

$body = file_get_contents('php://input');
$arr = json_decode($body, true); 

$tg = new tg(TGKEY);

$tg_id = 1051437638;
$rez_kb = array();

$message_text = $arr['message']['text'];
$tg->sendChatAction($tg_id);
$sms_rev='Code 56736';

$tg->send($tg_id, $sms_rev, $rez_kb);
exit('ok'); // говорим телеге, что все окей
?>