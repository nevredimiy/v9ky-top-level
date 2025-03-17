<?php 

require_once CONTROLLERS . "/head.php";

$sql = "SELECT 
        `city` AS city_id, 
        (SELECT `name_ua` FROM `v9ky_city` WHERE `id` = t.`city`) AS `city_name` 
        FROM `v9ky_turnir` t
        WHERE `active` = 0 
        GROUP BY `city` 
        ORDER BY (t.`city` = 1) DESC, `city_name`";

$cities = $dbF->query($sql)->findAll();
// dump_arr($cities);

$listTurnirs = [];
foreach($cities as $city){
    $sql1 = "SELECT * 
            FROM v9ky_turnir t
            WHERE active = 0 AND city=:city_id 
            ORDER BY t.id DESC";
    
    $fields = $dbF->query($sql1, [":city_id" => $city['city_id']])->findAll();
    $listTurnirs[$city['city_name']][] = $fields;
    
}




require_once VIEWS . "/archive.tpl.php";
require_once CONTROLLERS . "/footer.php";