<?php

// // Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


$turnir = getTurnir($tournament); 


include_once CONTROLLERS . "/head.php";
include_once CONTROLLERS . "/leagues.php";



$sqlRedCard = "SELECT 
        r.`player` as pl_id,
        p.`man`,
        m.`name1` as lastname,
        m.`name2` as firstname,
        mf.`pict` as player_photo,
        t.`pict` as team_logo,
        t.`name` as team,
        mtc.tur
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
    LEFT JOIN `v9ky_match` mtc
        ON mtc.id = r.matc
    WHERE matc = ( SELECT id 
            FROM v9ky_match mt
            WHERE canseled=1 
            AND turnir=:turnir
            AND ( mt.team1 = r.team OR mt.team2 = r.team) 
            ORDER BY mt.date DESC LIMIT 1)";

// Запрос на игроков которые "вибули"
$disPlayers = $dbF->query($sqlRedCard, ['turnir' => $turnir])->findALl();


// игроки у которых больше трех желтых карточек за турнир
$sqlYellowCard = "SELECT 
        y.`player` as pl_id,
        p.`man`,
        m.`name1` as lastname,
        m.`name2` as firstname,
        mf.`pict` as player_photo,
        t.`pict` as team_logo,
        t.`name` as team,
        mtc.`tur`
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
    
    LEFT JOIN `v9ky_match` mtc
        ON mtc.`id` = y.`matc` 

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



$sqlTable = "SELECT 
    SUM(a.kol) AS kolyel, 
    a.id, 
    a.team, 
    a.pict as team_logo, 
    a.man, 
    a.pl_id,
    m.`name1` as lastname,
    m.`name2` as firstname,
    mf.`pict` as player_photo
    FROM (
        SELECT 
            COUNT(*) AS kol, 
            v9ky_team.id AS id, 
            v9ky_team.name AS team, 
            v9ky_team.pict AS pict,
            v9ky_player.man AS man, 
            v9ky_player.id AS pl_id
        FROM v9ky_yellow
        INNER JOIN v9ky_player ON v9ky_player.id = v9ky_yellow.player
        INNER JOIN v9ky_team ON v9ky_team.id = v9ky_player.team
        INNER JOIN v9ky_match ON v9ky_match.id = v9ky_yellow.matc
        WHERE 
            v9ky_player.active = '1' 
            AND v9ky_match.canseled = '1' 
            AND v9ky_match.turnir = :turnir
        GROUP BY v9ky_yellow.player
        
        UNION ALL
        
        SELECT 
            COUNT(*) * 5 AS kol, 
            v9ky_team.id AS id, 
            v9ky_team.name AS team, 
            v9ky_team.pict AS pict,
            v9ky_player.man AS man, 
            v9ky_player.id AS pl_id
        FROM v9ky_red
        INNER JOIN v9ky_player ON v9ky_player.id = v9ky_red.player
        INNER JOIN v9ky_team ON v9ky_team.id = v9ky_player.team
        INNER JOIN v9ky_match ON v9ky_match.id = v9ky_red.matc
        WHERE 
            v9ky_player.active = '1' 
            AND v9ky_match.canseled = '1' 
            AND v9ky_match.turnir = :turnir
        GROUP BY v9ky_red.player
        ORDER BY 5
    ) AS a 

    LEFT JOIN `v9ky_man` m
            ON m.`id` = a.`man` 
    LEFT JOIN `v9ky_man_face` mf
            ON mf.`id` = (
                SELECT max(id)
                FROM `v9ky_man_face` mff
                WHERE mff.`man` = a.`man`
            )
    GROUP BY a.man 
    ORDER BY kolyel DESC;
";

$violationTable = $dbF->query($sqlTable, ["turnir" => $turnir])->findAll();

$recordcard = "SELECT 
        v9ky_match.tur AS t, 
        '1' AS vid, 
        v9ky_match.date AS d 
    FROM v9ky_yellow
    INNER JOIN v9ky_player ON v9ky_player.id = v9ky_yellow.player
    INNER JOIN v9ky_match ON v9ky_match.id = v9ky_yellow.matc
    WHERE 
        v9ky_player.active = '1' 
        AND v9ky_match.canseled = '1' 
        AND v9ky_yellow.player = :player_id

    UNION ALL

    SELECT 
        v9ky_match.tur AS t, 
        '2' AS vid, 
        v9ky_match.date AS d 
    FROM v9ky_red
    INNER JOIN v9ky_player ON v9ky_player.id = v9ky_red.player
    INNER JOIN v9ky_match ON v9ky_match.id = v9ky_red.matc
    WHERE 
        v9ky_player.active = '1' 
        AND v9ky_match.canseled = '1' 
        AND v9ky_red.player = :player_id

    ORDER BY d
";

$dataCards = [];

foreach($violationTable as $item) {
    $dataCards[$item['pl_id']] = $dbF->query($recordcard, ["player_id" => $item['pl_id']])->findAll();
}




?>


<div class="statistic">
    <div class="container">
        <?php if(count($disPlayers) > 0) : ?>
            <table id="top-pas" class="draggable-container">
                <caption>
                    Дискваліфікація
                </caption>

                <thead>
                    <tr>
                        <th>№</th>
                        <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
                        <th class="th_s" data-label="К">КОМАНДА</th>
                        <th class="th_s" data-label="Д" >Дані</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($disPlayers as $key => $player): ?>
                        <tr>
                            <td><?= $key +1  ?></td>
                            <td>
                                <img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                            </td>
                            <td>
                                <img src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                <?= $player['team'] ?>
                            </td>
                            <td class="name-cell">
                                <img src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                <?= $player['tur'] ?> тур
                            </td>
                        </tr>                
                    <?php endforeach ?>
                    <?php if(count($disThreeYellowPlayer) > 0):?>
                        <?php foreach($disPlayers as $player): ?>
                            <tr>
                                <td><?= $key +1  ?></td>
                                <td>
                                    <img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                    <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                </td>
                                <td>
                                    <img src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                    <?= $player['team'] ?>
                                </td>
                                <td class="name-cell">
                                    <img src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                    <?= $player['tur'] ?> тур
                                </td>
                            </tr>                
                        <?php endforeach ?>
                    <?php endif?>
                </tbody>
            </table>
        <?php endif ?>
        
        <table id="top-pas" class="draggable-container">
            <caption>
                Порушення
            </caption>

            <thead>
                <tr>
                    <th>№</th>
                    <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
                    <th class="th_s" data-label="К">КОМАНДА</th>
                    <th class="th_s" data-label="Д" >Дані</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($violationTable as $key => $player): ?>
                    <tr>
                        <td><?= $key +1  ?></td>
                        <td>
                            <img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                            <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                        </td>
                        <td>
                            <img src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                            <?= $player['team'] ?>
                        </td>
                        <td class="name-cell">
                             <?php foreach( $dataCards[$player['pl_id']] as $card):?>
                                <?php if($card['vid'] == 1): ?>
                                    <img src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                    <?= $card['t'] ?> тур
                                <?php else: ?>
                                    <img src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                    <?= $card['t'] ?> тур
                                <?php endif ?>
                            <?php endforeach?>

                        </td>
                    </tr>
                
                <?php endforeach ?>
            </tbody>
        </table>       
    </div>
</div>



<?php include_once  CONTROLLERS . "/footer.php" ?>

