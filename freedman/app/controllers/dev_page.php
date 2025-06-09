<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// игроки у которых больше двух (три и более) желтых карточек за турнир
// 1 условие. Выбираем только тех игроков, у кого больше двух жёлтых карточек (то есть минимум 3 карточки).
// 2 условие. Берутся карточки только из последнего сыгранного тура турнира (не из любого матча).
// 3 условие. Игрок не должен быть дисквалифицирован (то есть не должен иметь красную карточку) в этом же туре.
// 4 условие. Игрок должен быть активен (то есть иметь статус "1") в таблице игроков.
// 5 условие. Игрок должен быть в команде, которая участвует в турнире (то есть его команда должна быть в таблице команд турнира).

$turnir = getTurnir($tournament); 

$currentTur = getLasttur($turnir);

function isCupCurrentTur($turnir, $currentTur) {
    global $dbF;
    $sql = "SELECT cup_stage FROM v9ky_match WHERE turnir = :turnir AND tur = :tur LIMIT 1";
    $result = $dbF->query($sql, ['turnir' => $turnir, 'tur' => $currentTur])->find();
    return isset($result['cup_stage']) && $result['cup_stage'] > 0;
}

// true - турнір кубка (плей-офф), false - ліга (групповой турнир)
$isCupCurrentTur = isCupCurrentTur($turnir, $currentTur);

include_once CONTROLLERS . "/head.php";
include_once CONTROLLERS . "/leagues.php";

function getCardsByType($dbF, $turnir, $cardType, $currentTur, $cupStage = 0) {
    $tableName = $cardType === 'red' ? 'v9ky_red' : 'v9ky_yellow';
    $cupStageCondition = $cupStage > 0 ? "AND mtc.cup_stage > 0" : "AND mtc.cup_stage = 0";
    
    $sql = "
        SELECT 
            c.`player` as player_id,
            p.`man`,
            m.`name1` as lastname,
            m.`name2` as firstname,
            mf.`pict` as player_photo,
            t.`pict` as team_logo,
            t.`name` as team,
            mtc.`tur`
        FROM $tableName c
        LEFT JOIN `v9ky_player` p ON p.`id` = c.`player`
        LEFT JOIN `v9ky_man` m ON m.`id` = p.`man`
        LEFT JOIN `v9ky_man_face` mf ON mf.`id` = (
            SELECT MAX(id) FROM `v9ky_man_face` mff WHERE mff.`man` = p.`man`
        )
        LEFT JOIN `v9ky_team` t ON t.`id` = p.`team`
        LEFT JOIN `v9ky_match` mtc ON mtc.`id` = c.`matc`
        WHERE mtc.`turnir` = :turnir
        $cupStageCondition
        ORDER BY mtc.`tur`, mtc.`date`
    ";

    $cards = $dbF->query($sql, ['turnir' => $turnir])->findAll();
    $result = [];

    foreach ($cards as $player) {
        $id = $player['player_id'];
        if (!isset($result[$id])) {
            $result[$id] = [
                'player_id' => $id,
                'lastname' => $player['lastname'],
                'firstname' => $player['firstname'],
                'team_logo' => $player['team_logo'],
                'team' => $player['team'],
                'player_photo' => $player['player_photo'],
                'red' => [],
                'yellow' => []
            ];
        }

        if ($player['tur'] <= $currentTur) {
            $result[$id][$cardType][] = $player['tur'];
        }
    }

    return $result;
}
$redCards = getCardsByType($dbF, $turnir, 'red', $currentTur);
$yellowCards = getCardsByType($dbF, $turnir, 'yellow', $currentTur);

function getTableCards($redCards, $yellowCards) {
    $tableCards = [];

    foreach ([$redCards, $yellowCards] as $source) {
        foreach ($source as $id => $data) {
            if (!isset($tableCards[$id])) {
                $tableCards[$id] = [
                    'player_id' => $data['player_id'],
                    'lastname' => $data['lastname'],
                    'firstname' => $data['firstname'],
                    'team_logo' => $data['team_logo'],
                    'team' => $data['team'],
                    'player_photo' => $data['player_photo'],
                    'red' => [],
                    'yellow' => [],
                    'tur' => []
                ];
            }

            $tableCards[$id]['red'] = array_merge($tableCards[$id]['red'], $data['red']);
            $tableCards[$id]['yellow'] = array_merge($tableCards[$id]['yellow'], $data['yellow']);
            $tableCards[$id]['tur'] = array_merge($tableCards[$id]['tur'], $data['red'], $data['yellow']);
        }
    }


    // Сортировка по количеству карточек (общая)
    usort($tableCards, function($a, $b) {
        return count($b['tur']) - count($a['tur']);
    });

    return $tableCards;
}

$tableCards = getTableCards($redCards, $yellowCards);


function getDisqualifiedPlayers($tableCards, $currentTur) {
    $disqualifiedPlayers = [];

    foreach ($tableCards as $playerId => $player) {
        // Проверяем на красные карточки
        if (count($player['red']) > 0) {
            $lastRedTur = end($player['red']);

            if ($lastRedTur == $currentTur || $lastRedTur == $currentTur - 1) {
                $disqualifiedPlayers[] = [
                    'player_id' => $player['player_id'],
                    'lastname' => $player['lastname'],
                    'firstname' => $player['firstname'],
                    'team_logo' => $player['team_logo'],
                    'team' => $player['team'],
                    'player_photo' => $player['player_photo'],
                    // 'tur' => implode(', ', $player['red'])
                ];
            }
            // unset($tableCards[$playerId]); // Удаляем игрока из общего списка
        }

        // Проверяем на желтые карточки
        if (count($player['yellow']) % 3 == 0 && count($player['yellow']) > 0) {
            $yellowCardLastTur = end($player['yellow']);
            
            if ($yellowCardLastTur == $currentTur) {
                
                $disqualifiedPlayers[] = [
                    'player_id' => $player['player_id'],
                    'lastname' => $player['lastname'],
                    'firstname' => $player['firstname'],
                    'team_logo' => $player['team_logo'],
                    'team' => $player['team'],
                    'player_photo' => $player['player_photo'],
                    // 'tur' => implode(', ', $player['yellow'])
                ];
            }
        }        
    }
    // Удаляем дубликаты игроков из списка дисквалифицированных
    $disqualifiedPlayers = array_unique($disqualifiedPlayers, SORT_REGULAR);

    return $disqualifiedPlayers;
}

$disqualifiedPlayers = getDisqualifiedPlayers($tableCards, $currentTur);

if($isCupCurrentTur){

    $redCardsCup = getCardsByType($dbF, $turnir, 'red', $currentTur, 1);

    $yellowCardsCup = getCardsByType($dbF, $turnir, 'yellow', $currentTur, 1);

    $tableCardsCupTurnir = getTableCards($redCardsCup, $yellowCardsCup);

    $disqualifiedPlayersCup = getDisqualifiedPlayers($tableCardsCupTurnir, $currentTur);
}



?>



<div class="statistic">
    <div class="container">
        <?php if(isset($tableCardsCupTurnir) && count($tableCardsCupTurnir) > 0):?>
            
            <div class="">
                <h2 class="text-center uppercase">Плей-Офф</h2>
                <?php if(isset($disqualifiedPlayersCup) && count($disqualifiedPlayersCup) > 0) : ?>
                    <table id="top-pas" class="draggable-container width-auto">
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
                            <?php foreach($disqualifiedPlayersCup as $key => $player): ?>
                                <tr data-playerid="<?= $player['player_id'] ?>">
                                    <td><?= $key +1  ?></td>
                                    <td>
                                        <img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                        <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                    </td>
                                    <td>
                                        <img src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                        <?= $player['team'] ?>
                                    </td>
                                    <td>
                                        
                                    </td>
                                </tr>                
                            <?php endforeach ?>
                    </table>
                <?php endif ?>        
                <table id="top-pas" class="draggable-container width-auto">
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
                        <?php foreach($tableCardsCupTurnir as $key => $player): ?>
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
                                <td >
                                    <?php foreach($player['red'] as $tur):?>
                                        <img width="20" height="30" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                        <?=$tur?> тур
                                    <?php endforeach?>
                                    <?php foreach($player['yellow'] as $tur):?>
                                        <img width="20" height="30" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                        <?=$tur?> тур
                                    <?php endforeach?>
            
                                </td>
                            </tr>
                        
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

        <?php endif?>
        <div class="">
            <h2 class="text-center uppercase">Груповий Етап</h2>
            <?php if(isset($disqualifiedPlayers) && count($disqualifiedPlayers) > 0) : ?>
                <table id="top-pas" class="draggable-container width-auto">
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
                        <?php foreach($disqualifiedPlayers as $key => $player): ?>
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
                                <td>
                                   
                                </td>
                            </tr>                
                        <?php endforeach ?>
                </table>
            <?php endif ?>        
            <table id="top-pas" class="draggable-container width-auto">
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
                    <?php foreach($tableCards as $key => $player): ?>
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
                            <td >
                                <?php foreach($player['red'] as $tur):?>
                                    <img width="20" height="30" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                    <?=$tur?> тур
                                <?php endforeach?>
                                <?php foreach($player['yellow'] as $tur):?>
                                    <img width="20" height="30" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                    <?=$tur?> тур
                                <?php endforeach?>
        
                            </td>
                        </tr>
                    
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php include_once  CONTROLLERS . "/footer.php" ?>

