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





// true - турнір кубка (плей-офф), false - ліга (групповой турнир)
$isCupCurrentTur = isCupCurrentTur($turnir, $currentTur);

include_once CONTROLLERS . "/head.php";
include_once CONTROLLERS . "/leagues.php";

$redCards = getCardsByType($dbF, $turnir, 'red', $currentTur);
$yellowCards = getCardsByType($dbF, $turnir, 'yellow', $currentTur);

$tableCards = getTableCards($redCards, $yellowCards);

$disqualifiedPlayers = getDisqualifiedPlayers($tableCards, $currentTur);
// dump($disqualifiedPlayers);

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
                                <tr>
                                    <td><?= $key +1  ?></td>
                                    <td>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                        <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                    </td>
                                    <td>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                        <?= $player['team'] ?>
                                    </td>
                                    <td>

                                        <?php foreach($player['red'] as $tur):?>
                                            <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                            <?=$tur?> тур
                                        <?php endforeach?>
                                        <?php foreach($player['yellow'] as $tur):?>
                                            <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                            <?=$tur?> тур
                                        <?php endforeach?>
                                        
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
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                    <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                </td>
                                <td>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                    <?= $player['team'] ?>
                                </td>
                                <td >
                                    <?php foreach($player['red'] as $tur):?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                        <?=$tur?> тур
                                    <?php endforeach?>
                                    <?php foreach($player['yellow'] as $tur):?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
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
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                    <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                </td>
                                <td>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                    <?= $player['team'] ?>
                                </td>
                                <td>
                                    <?php foreach($player['red'] as $tur):?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                        <?=$tur?> тур
                                    <?php endforeach?>
                                    <?php foreach($player['yellow'] as $tur):?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                        <?=$tur?> тур
                                    <?php endforeach?>                                       
                                   
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
                                <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                            </td>
                            <td>
                                <img width="20" height="30" style="width: 20px; height: 30px;" src="<?=$team_logo_path?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                <?= $player['team'] ?>
                            </td>
                            <td >
                                <?php foreach($player['red'] as $tur):?>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                    <?=$tur?> тур
                                <?php endforeach?>
                                <?php foreach($player['yellow'] as $tur):?>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
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

