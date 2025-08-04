<?php

if (!isset($currentTur)) {
    if (isset($turnir)) $turnir = getTurnir($tournament);
    $currentTur = getLasttur($turnir);
}


$dateLastTur = getDateLastTur($turnir);

// true - турнір кубка (плей-офф), false - ліга (групповой турнир)
$isCupCurrentTur = isCupCurrentTur($turnir, $currentTur);


$matchesLastTur = getMatchesLastTurTurByDate($turnir, $dateLastTur);

$matchesLastTurIds = [];
foreach ($matchesLastTur as $match) {
    $matchesLastTurIds[] = $match['id'];
}

$redCardsByTypeAndDate = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'red');
$yellowCardsByTypeAndDate = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow');
$tableCardsByDate = getTableCards($redCardsByTypeAndDate, $yellowCardsByTypeAndDate);
$dateUpcomingTur = getDateUpcomingTur($turnir);
$disqualifiedPlayers = getDisqualifiedPlayersByDate($tableCardsByDate, $turnir, $dateLastTur);

// $redCards = getCardsByType($dbF, $turnir, 'red', $currentTur);

// $yellowCards = getCardsByType($dbF, $turnir, 'yellow', $currentTur);

// $tableCards = getTableCards($redCards, $yellowCards);

// $disqualifiedPlayers = getDisqualifiedPlayers($tableCards, $currentTur);

if ($isCupCurrentTur) {

    $redCardsCup = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'red', 1);

    $yellowCardsCup =  getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow', 1);

    $tableCardsCupTurnir = getTableCards($redCardsCup, $yellowCardsCup);

    $disqualifiedPlayersCup = getDisqualifiedPlayersByDate($tableCardsCupTurnir, $dateLastTur, 2);
}

$disPlayers = count($disqualifiedPlayersCup) > 0 ? $disqualifiedPlayersCup : $disqualifiedPlayers;

require_once VIEWS . '/disqualification.tpl.php';
