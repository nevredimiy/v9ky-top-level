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

$dataTurnirs1 = [];
foreach($cities as $city){
    $sql1 = "SELECT * 
            FROM v9ky_turnir t
            WHERE active = 0 AND city=:city_id 
            ORDER BY t.id DESC";
    
    $fields = $dbF->query($sql1, [":city_id" => $city['city_id']])->findAll();
    $dataTurnirs1[$city['city_name']][] = $fields;
    
}

?>

<div class="container">
<?php foreach($dataTurnirs1 as $key => $data) : ?>
        <?php foreach($data as $item): ?>
                <?php foreach($item as $it) : ?>

                        <p> <?= $key ?> / <?= $it['season'] ?> / <?= $it['ru'] ?></p>
                        <br>

                <?php endforeach ?>
                <br>
        
<?php endforeach ?>
<?php endforeach ?>
</div>

<?php


require_once VIEWS . "/archive.tpl.php";
require_once CONTROLLERS . "/footer.php";