<?php

if(!isset($currentTur)){
    if(isset($turnir)) $turnir = getTurnir($tournament); 
    $currentTur = getLasttur($turnir);
}

// true - турнір кубка (плей-офф), false - ліга (групповой турнир)
$isCupCurrentTur = isCupCurrentTur($turnir, $currentTur);

$redCards = getCardsByType($dbF, $turnir, 'red', $currentTur);
$yellowCards = getCardsByType($dbF, $turnir, 'yellow', $currentTur);

$tableCards = getTableCards($redCards, $yellowCards);

$disqualifiedPlayers = getDisqualifiedPlayers($tableCards, $currentTur);

if($isCupCurrentTur){

    $redCardsCup = getCardsByType($dbF, $turnir, 'red', $currentTur, 1);

    $yellowCardsCup = getCardsByType($dbF, $turnir, 'yellow', $currentTur, 1);

    $tableCardsCupTurnir = getTableCards($redCardsCup, $yellowCardsCup);

    $disqualifiedPlayersCup = getDisqualifiedPlayers($tableCardsCupTurnir, $currentTur);
}

$disPlayers = count($disqualifiedPlayersCup)>0 ? $disqualifiedPlayersCup : $disqualifiedPlayers;

require_once VIEWS . '/disqualification.tpl.php';