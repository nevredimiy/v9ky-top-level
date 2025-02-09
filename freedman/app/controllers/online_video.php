<?php

$sqlOnlineVideos = "SELECT * FROM `v9ky_online_video` WHERE `active` > 0 ORDER BY `prior`";

$onlineVideos = $dbF->query($sqlOnlineVideos)->findAll();

require_once CONTROLLERS . '/head.php';
require_once VIEWS . '/online_video.tpl.php';
require_once CONTROLLERS . 'footer.php';