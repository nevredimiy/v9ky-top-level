<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


$turnir = getTurnir($tournament);  
$seasonName = getSeasonName($turnir);

// Получаем идентификатор команды из адресной строки
if (isset($params['id'])) {
    $teamId = $params['id'];
  }

$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем количество сыграных туров в турнире. Это нужно для отображения в таблице знака вопроса для несыграных матчей. 
$queryLastTur = $db->Execute(
  "SELECT tur as last_tur FROM `v9ky_match` WHERE `canseled`=1 and `turnir` = $turnir order by tur desc limit 1"
);

// Последний тур в турнире (в лиге).
$lastTur = intval($queryLastTur->fields[0]);

$dataAllPlayers = getDataPlayers($allStaticPlayers);

// Отсортированный массив по рубрике Топ-Гравець
$topGravetc = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'topgravetc', $lastTur);

// dump_arr($topGravetc);

// Отсортированный массив по рубрике Топ-Голкипер
$topGolkiper = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golkiper', $lastTur);


// Отсортированный массив по рубрике Топ-Бомбардир
$topBombardi = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_goals', $lastTur);

// Отсортированный массив по рубрике Топ-Асистент
$topAsists = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_asists', $lastTur);

// Отсортированный массив по рубрике Топ-Захистник
$topZhusnuk = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'zahusnuk', $lastTur);

// Отсортированный массив по рубрике Топ-Дриблинг
$topDribling = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'dribling', $lastTur);

// Отсортированный массив по рубрике Топ-Удар
$topUdar = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'udar', $lastTur);

// Отсортированный массив по рубрике Топ-Пас
$topPas = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'pas', $lastTur);

// Берем первый элемент массива - топ-игрок
$topTopGravetc = reset($topGravetc);

// Берем первый элемент массива - топ-игрок
$topTopGolkiper = reset($topGolkiper);

// Берем первый элемент массива - топ-игрок
$topTopBombardi = reset($topBombardi);

// Берем первый элемент массива - топ-игрок
$topTopAsists = reset($topAsists);

// Берем первый элемент массива - топ-игрок
$topTopZhusnuk = reset($topZhusnuk);

// Берем первый элемент массива - топ-игрок
$topTopDribling = reset($topDribling);

// Берем первый элемент массива - топ-игрок
$topTopUdar = reset($topUdar);

// Берем первый элемент массива - топ-игрок
$topTopPas = reset($topPas);



// Получаем идентификатор команды из адресной строки
if (isset($params['id'])) {
  $teamId = $params['id'];
} 

// Сумма статистики команды
$totalGoalsByTeam = getTotalStaticByTeam($topBombardi, $teamId);
$totalAsistByTeam = getTotalStaticByTeam($topAsists, $teamId);
// $totalMatchesByTeam = getTotalStaticByTeam($topAsists, $teamId, 'match_count');
$totalMatchesByTeam = getCountMatchesOfTurnir($allStaticPlayers, $teamId);
$totalYellowByTeam = getTotalStaticByTeam($topAsists, $teamId, 'yellow_cards');
$totalYellowRedByTeam = getTotalStaticByTeam($topAsists, $teamId, 'yellow_red_cards');
$totalRedByTeam = getTotalStaticByTeam($topAsists, $teamId, 'red_cards');
$totalBestPlayerByTeam = getTotalStaticByTeam($topAsists, $teamId, 'count_best_player_of_match');

// Лучшие показатели в команде 
$bestGravetc = getBestPlayer($topGravetc, $teamId);
$bestGolkiper = getBestPlayer($topGolkiper, $teamId);
$bestBombardi = getBestPlayer($topBombardi, $teamId);
$bestAssist = getBestPlayer($topAsists, $teamId);
$bestZhusnuk = getBestPlayer($topZhusnuk, $teamId);
$bestDribling = getBestPlayer($topDribling, $teamId);
$bestUdar = getBestPlayer($topUdar, $teamId);
$bestPas = getBestPlayer($topPas, $teamId);

$requestUri = $_SERVER['REQUEST_URI'];

// Разделение пути по "/"
$partsUri = explode('/', $requestUri);

$playersOfAllTurs = getPlayersOfAllTurs($allStaticPlayers, $lastTur);

$playerIds = getPlayerIds($dataAllPlayers, $teamId);
$countInTurOfTeam = getCountInTurOfTeam($playersOfAllTurs, $playerIds);


$teamData = getTeamData($teamId);
$players = getPlayersOfTeam($teamId);
$teamHeads = getTeamHeads($teamId);
$matches = getMatches($turnir, $teamId);

// Добавляем два элемента в массив с форматированными датами: match_day и match_time
$matches = getArrayWithFormattedDate($matches);

// dump_arr_first($allStaticPlayers);
// dump_arr_first($matches);


require_once VIEWS . '/team_page.tpl.php';


