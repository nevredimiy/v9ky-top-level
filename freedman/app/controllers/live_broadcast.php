<?php

$sqlLiveBroadcast = "SELECT 
    m.`id`, 
    c.`name_ua` AS city_name,
    m.`canseled`, 
    m.`date`, 
    m.`tur`, 
    m.`field`, 
    f.name AS field_name,
    m.`turnir`, 
    m.`gols1` AS goals1, 
    m.`gols2` AS goals2, 
    m.`team1`,
    t1.name AS team1_name,
    t1.pict AS team1_logo,
    m.`team2`,
    t2.name AS team2_name,
    t2.pict AS team2_logo
FROM `v9ky_match` m
LEFT JOIN `v9ky_team` t1 
	ON t1.id = m.team1
LEFT JOIN `v9ky_team` t2 
	ON t2.id = m.team2
LEFT JOIN `v9ky_fields` f
	ON f.id = m.field
LEFT JOIN `v9ky_city` c
	ON c.id = f.city
WHERE `canseled` = '2' 
ORDER BY `date`";


$sqlIncommingMatches = "SELECT 
    m.`id`, 
    c.`name_ua` AS city_name,
    m.`canseled`, 
    m.`date`, 
    m.`tur`, 
    m.`field`, 
    f.name AS field_name,
    m.`turnir`, 
    m.`gols1` AS goals1, 
    m.`gols2` AS goals2, 
    m.`team1`,
    t1.name AS team1_name,
    t1.pict AS team1_logo,
    m.`team2`,
    t2.name AS team2_name,
    t2.pict AS team2_logo
FROM `v9ky_match` m
LEFT JOIN `v9ky_team` t1 
	ON t1.id = m.team1
LEFT JOIN `v9ky_team` t2 
	ON t2.id = m.team2
LEFT JOIN `v9ky_fields` f
	ON f.id = m.field
LEFT JOIN `v9ky_city` c
	ON c.id = f.city
WHERE `canseled` = '0' 
AND (CURDATE() + INTERVAL 5 DAY) >= m.`date`
ORDER BY m.`date`";

$liveMatches = $dbF->query($sqlLiveBroadcast)->findAll();

$incommingMatches = $dbF->query($sqlIncommingMatches)->findAll();


require_once CONTROLLERS . '/head.php';
require_once VIEWS . '/live_broadcast.tpl.php';
require_once CONTROLLERS . 'footer.php';



