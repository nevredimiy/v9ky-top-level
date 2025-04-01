<?php

// Запросы построены так, что бы игроки были в списке исавалифицированных только один тур.
// Запрос на игроков у которые красные карточки
$sqlRedCard = "SELECT 
        r.`player`,
        p.`man`,
        m.`name1` as lastname,
        m.`name2` as firstname,
        mf.`pict` as player_photo,
        t.`pict` as team_photo,
        t.`name` as team_name 
    FROM v9ky_red r 
    LEFT JOIN `v9ky_player` p
            ON p.`id` = r.`player` 
    LEFT JOIN `v9ky_man` m
        ON m.`id` = p.`man` 
    LEFT JOIN `v9ky_man_face` mf
        ON mf.`id` = (
            SELECT max(id)
            FROM `v9ky_man_face` mff
            WHERE mff.`man` = p.`man`
        )
    LEFT JOIN `v9ky_team` t
        ON t.`id` = p.`team`  
    WHERE matc = ( SELECT id 
            FROM v9ky_match m 
            WHERE canseled=1 
            AND turnir=:turnir 
            AND ( m.team1 = r.team OR m.team2 = r.team) 
            ORDER BY m.date DESC LIMIT 1)";

// Запрос на игроков которые "вибули"
$disPlayers = $dbF->query($sqlRedCard, ['turnir' => $turnir])->findALl();


// игроки у которых больше трех желтых карточек за турнир
$sqlYellowCard = "SELECT 
        y.`player`,
        p.`man`,
        m.`name1` as lastname,
        m.`name2` as firstname,
        mf.`pict` as player_photo,
        t.`pict` as team_photo,
        t.`name` as team_name
    FROM v9ky_yellow y
    LEFT JOIN `v9ky_player` p
            ON p.`id` = y.`player` 
        LEFT JOIN `v9ky_man` m
            ON m.`id` = p.`man` 
        LEFT JOIN `v9ky_man_face` mf
            ON mf.`id` = (
                SELECT max(id)
                FROM `v9ky_man_face` mff
                WHERE mff.`man` = p.`man`
            )
        LEFT JOIN `v9ky_team` t
            ON t.`id` = p.`team`  

    WHERE  ( ( SELECT count(id) FROM v9ky_yellow WHERE player=y.player ) > 2 ) 
    AND ( 
            (SELECT MAX(tur) FROM v9ky_match WHERE id=y.matc AND canseled = 1 AND turnir=:turnir)
                =
            (SELECT max(tur) FROM v9ky_match WHERE canseled=1 AND turnir=:turnir)
        ) 
    AND player NOT IN (SELECT player FROM v9ky_red b WHERE (b.player=y.player) AND ((SELECT tur FROM v9ky_match WHERE id=b.matc) >= (SELECT min(tur) FROM v9ky_match WHERE id in (SELECT matc FROM v9ky_yellow WHERE player=b.player) AND canseled=1 AND turnir=:turnir AND (team1=team or team2=team) order by date desc limit 3))) 
    AND matc in (SELECT id FROM v9ky_match WHERE canseled=1 AND turnir=:turnir) 
    GROUP BY player";

$disThreeYellowPlayer = $dbF->query($sqlYellowCard, ['turnir' => $turnir])->findALl();
if(count($disThreeYellowPlayer) > 0){
    $disPlayers = array_merge($disPlayers, $disThreeYellowPlayer);
}


require_once VIEWS . '/disqualification.tpl.php';