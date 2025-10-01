<?php

if (!isset($currentTur)) {
    if (isset($turnir)) $turnir = getTurnir($tournament);
    $currentTur = getLasttur($turnir);
}


$dateLastTur = getDateLastTur($turnir);

if(!$dateLastTur){
    $dateLastTur = date("Y-m-d");
}

// true - турнір кубка (плей-офф), false - ліга (групповой турнир)
$isCupCurrentTur = isCupCurrentTur($turnir, $currentTur);


$matchesLastTur = getMatchesLastTurTurByDate($turnir, $dateLastTur);

$matchesLastTurIds = [];
foreach ($matchesLastTur as $match) {
    $matchesLastTurIds[] = $match['id'];
}

if(!empty($matchesLastTurIds)){
    $redCardsByTypeAndDate = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'red');    
    $yellowCardsByTypeAndDate = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow');   
    $yellowRedCardsByTypeAndDate = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow_red'); 
    $tableCardsByDate = getTableCards($redCardsByTypeAndDate, $yellowCardsByTypeAndDate, $yellowRedCardsByTypeAndDate);  
    $dateUpcomingTur = getDateUpcomingTur($turnir);
    $disqualifiedPlayers = getDisqualifiedPlayersByDate($tableCardsByDate, $turnir, $dateLastTur);
} else {
    $disqualifiedPlayers = [];
}

// $redCards = getCardsByType($dbF, $turnir, 'red', $currentTur);

// $yellowCards = getCardsByType($dbF, $turnir, 'yellow', $currentTur);

// $tableCards = getTableCards($redCards, $yellowCards);

// $disqualifiedPlayers = getDisqualifiedPlayers($tableCards, $currentTur);

if ($isCupCurrentTur) {

    $redCardsCup = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'red', 1);

    $yellowCardsCup =  getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow', 1);

    $yellowRedCardsCup = getCardsByTypeAndDate($dbF, $matchesLastTurIds, 'yellow_red', 1);

    $tableCardsCupTurnir = getTableCards($redCardsCup, $yellowCardsCup, $yellowRedCardsCup);

    $disqualifiedPlayersCup = getDisqualifiedPlayersByDate($tableCardsCupTurnir, $dateLastTur, 2);
}

$disPlayers = count($disqualifiedPlayersCup) > 0 ? $disqualifiedPlayersCup : $disqualifiedPlayers;

require_once VIEWS . '/disqualification.tpl.php';
