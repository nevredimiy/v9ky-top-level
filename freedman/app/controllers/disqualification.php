<?php

// Запрос на игроков которые "вибули"
$queryGetVibuv = "SELECT
	p.id,
    t.name AS team_name,
    t.pict AS team_photo,
    m.name1 AS lastname, 
    m.name2 AS firstname,
    (SELECT mf.pict 
     FROM v9ky_man_face mf 
     WHERE mf.man = p.man 
     ORDER BY mf.id DESC LIMIT 1) AS player_photo,
    (SELECT COUNT(*) FROM v9ky_red r WHERE r.player = p.id) AS red_card_count,
    (SELECT COUNT(*) FROM v9ky_yellow_red yr WHERE yr.player = p.id) AS yellow_red_card_count,
    (SELECT COUNT(*) FROM v9ky_yellow y WHERE y.player = p.id) AS yellow_card_count
FROM `v9ky_player` p 
LEFT JOIN v9ky_team t ON t.id = p.team
LEFT JOIN v9ky_man m ON m.id = p.man

WHERE p.`team` IN (SELECT id FROM v9ky_team WHERE turnir = :turnir) AND `vibuv` > 0 AND active > 0";

// Делаем запрос в БД на игроков которые "вибули"
$stmtDisPlayer = $mysqli->prepare($queryGetVibuv);
$stmtDisPlayer->bindParam(':turnir', $turnir, PDO::PARAM_INT);
$stmtDisPlayer->execute();
$disPlayers = $stmtDisPlayer->fetchAll(PDO::FETCH_ASSOC);

require_once VIEWS . '/disqualification.tpl.php';