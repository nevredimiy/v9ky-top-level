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

$dateLastTur = getDateLastTur($turnir);

$matchesLastTur = getMatchesLastTurTurByDate($turnir, $dateLastTur);

$matchesLastTurIds = [];
foreach ($matchesLastTur as $match) {
    $matchesLastTurIds[] = $match['id'];
}

// true - турнір кубка (плей-офф), false - ліга (групповой турнир)
$isCupCurrentTur = isCupCurrentTur($turnir, $currentTur);

$redCardsByTypeAndDate = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'red');
$yellowCardsByTypeAndDate = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow');

include_once CONTROLLERS . "/head.php";
include_once CONTROLLERS . "/leagues.php";

$tableCardsByDate = getTableCards($redCardsByTypeAndDate, $yellowCardsByTypeAndDate);



function getDisqualifiedPlayersByDate1($tableCardsByDate, $turnir, $dateLastTur, $countCards = 3)
{
    $disqualifiedPlayers = [];

    foreach ($tableCardsByDate as $player) {

        // $lastTur = date('Y-m-d', strtotime($dateLastTur));
        $dateLastTur1 = getDateLastTur($turnir, $player['team_id']);

        // dd($dateLastTur1);
        $lastTur1 = date('Y-m-d H:i:s', strtotime($dateLastTur1));

        if (isset($player['red_date']) && count($player['red_date']) > 0) {
            $lastRedTur = end($player['red_date']);
            $lastRedTur = date('Y-m-d H:i:s', strtotime($lastRedTur));            

            // $upcomingTur = date('Y-m-d H:i:s', strtotime($dateUpcomingTur));

            // Перевірка, чи гравець дискваліфікований
            if ($lastRedTur == $lastTur1) {

                $disqualifiedPlayers[$player['player_id']] = [
                    'player_id' => $player['player_id'],
                    'lastname' => $player['lastname'],
                    'firstname' => $player['firstname'],
                    'team_logo' => $player['team_logo'],
                    'team' => $player['team'],
                    'player_photo' => $player['player_photo'],
                    'red' => $player['red'],
                    'yellow' => $player['yellow'],
                    'red_date' => $player['red_date'],
                    'yellow_date' => $player['yellow_date']
                ];
            }
        }

        // Перевірка на кількість жовтих карток

        // Желто-красная карточка - когда две желтіе в одном туре
        $yellowAndRed = 0;
        $doubleYellowInMatch = [];
        foreach ($player['yellow'] as $yellow) {
            if (in_array($yellow, $doubleYellowInMatch)) {
                $yellowAndRed++;
            }
            $doubleYellowInMatch[] = $yellow;
        }

        $yellowTotal = count($player['yellow_date']) + $yellowAndRed;

        if ($yellowTotal > 0 && $yellowTotal % $countCards == 0) {
            $lastYellowTur = end($player['yellow_date']);
            $lastYellowTur = date('Y-m-d H:i:s', strtotime($lastYellowTur));

            if ($lastYellowTur == $lastTur1) {
                if (!isset($disqualifiedPlayers[$player['player_id']])) {
                    $disqualifiedPlayers[$player['player_id']] = [
                        'player_id' => $player['player_id'],
                        'lastname' => $player['lastname'],
                        'firstname' => $player['firstname'],
                        'team_logo' => $player['team_logo'],
                        'team' => $player['team'],
                        'player_photo' => $player['player_photo'],
                        'red' => [],
                        'yellow' => $player['yellow'],
                        'red_date' => [],
                        'yellow_date' => $player['yellow_date']
                    ];
                } elseif (empty($disqualifiedPlayers[$player['player_id']]['yellow'])) {
                    // Только если в массиве ещё не добавлены жёлтые карточки — добавляем
                    $disqualifiedPlayers[$player['player_id']]['yellow'] = $player['yellow'];
                    $disqualifiedPlayers[$player['player_id']]['yellow_date'] = $player['yellow_date'];
                }
            }
        }
    }
    
    return array_values($disqualifiedPlayers);
}

// dd($tableCardsByDate);

$disqualifiedPlayers = getDisqualifiedPlayersByDate1($tableCardsByDate, $turnir,  $dateLastTur);

// dump($disqualifiedPlayers);
if ($isCupCurrentTur) {

    $redCardsCup = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'red', 1);

    $yellowCardsCup = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow', 1);

    $tableCardsCupTurnir = getTableCards($redCardsCup, $yellowCardsCup);

    $disqualifiedPlayersCup = getDisqualifiedPlayersByDate($tableCardsCupTurnir, $dateLastTur, 2);
}

?>

<div class="statistic">
    <div class="container">
        <?php if (isset($tableCardsCupTurnir) && count($tableCardsCupTurnir) > 0): ?>

            <div class="">
                <h2 class="text-center uppercase">Плей-Офф</h2>
                <?php if (isset($disqualifiedPlayersCup) && count($disqualifiedPlayersCup) > 0) : ?>
                    <table id="top-pas" class="draggable-container width-auto">
                        <caption>
                            Дискваліфікація
                        </caption>

                        <thead>
                            <tr>
                                <th>№</th>
                                <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
                                <th class="th_s" data-label="К">КОМАНДА</th>
                                <th class="th_s" data-label="Д">Дані</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disqualifiedPlayersCup as $key => $player): ?>
                                <tr>
                                    <td><?= $key + 1  ?></td>
                                    <td>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $player_face_path ?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                        <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                    </td>
                                    <td>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $team_logo_path ?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                        <?= $player['team'] ?>
                                    </td>
                                    <td>

                                        <?php foreach ($player['red'] as $tur): ?>
                                            <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                            <?= $tur ?> тур
                                        <?php endforeach ?>
                                        <?php foreach ($player['yellow'] as $tur): ?>
                                            <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                            <?= $tur ?> тур
                                        <?php endforeach ?>

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
                            <th class="th_s" data-label="Д">Дані</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tableCardsCupTurnir as $key => $player): ?>
                            <tr>
                                <td><?= $key + 1  ?></td>
                                <td>

                                    <?php if (isset($player['player_photo']) && !empty($player['player_photo'])): ?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $player_face_path ?><?= $player['player_photo'] ?>" alt="Фото гравця">
                                    <?php else: ?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/no-image.jpeg' ?>" alt="Фото гравця">
                                    <?php endif ?>
                                    <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                </td>
                                <td>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $team_logo_path ?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                    <?= $player['team'] ?>
                                </td>
                                <td>
                                    <?php foreach ($player['red'] as $tur): ?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                        <?= $tur ?> тур
                                    <?php endforeach ?>
                                    <?php foreach ($player['yellow'] as $tur): ?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                        <?= $tur ?> тур
                                    <?php endforeach ?>

                                </td>
                            </tr>

                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

        <?php endif ?>

        <div class="">
            <h2 class="text-center uppercase">Груповий Етап</h2>
            <?php if (isset($disqualifiedPlayers) && count($disqualifiedPlayers) > 0) : ?>
                <table id="top-pas" class="draggable-container width-auto">
                    <caption>
                        Дискваліфікація
                    </caption>

                    <thead>
                        <tr>
                            <th>№</th>
                            <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
                            <th class="th_s" data-label="К">КОМАНДА</th>
                            <th class="th_s" data-label="Д">Дані</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($disqualifiedPlayers as $key => $player): ?>
                            <tr data-player-id="<?= $player['player_id'] ?>" >
                                <td><?= $key + 1  ?></td>
                                <td>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $player_face_path ?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                    <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                                </td>
                                <td>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $team_logo_path ?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                    <?= $player['team'] ?>
                                </td>
                                <td>
                                    <?php foreach ($player['red'] as $tur): ?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                        <?= $tur ?> тур
                                    <?php endforeach ?>
                                    <?php foreach ($player['yellow'] as $tur): ?>
                                        <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                        <?= $tur ?> тур
                                    <?php endforeach ?>

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
                        <th class="th_s" data-label="Д">Дані</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tableCardsByDate as $key => $player): ?>
                        <tr data-player-id="<?= $player['player_id'] ?>" >
                            <td><?= $key + 1  ?></td>
                            <td>
                                <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $player_face_path ?>/<?= $player['player_photo'] ?>" alt="team-logo">
                                <?= $player['lastname'] ?> <?= $player['firstname'] ?>
                            </td>
                            <td>
                                <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= $team_logo_path ?>/<?= $player['team_logo'] ?>" alt="team-logo">
                                <?= $player['team'] ?>
                            </td>
                            <td>
                                <?php foreach ($player['red'] as $tur): ?>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/red-card-icon.png' ?>" alt="">
                                    <?= $tur ?> тур
                                <?php endforeach ?>
                                <?php foreach ($player['yellow'] as $tur): ?>
                                    <img width="20" height="30" style="width: 20px; height: 30px;" src="<?= IMAGES . '/yellow-card-icon.png' ?>" alt="">
                                    <?= $tur ?> тур
                                <?php endforeach ?>

                            </td>
                        </tr>

                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php include_once  CONTROLLERS . "/footer.php" ?>