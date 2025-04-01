<?php
// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

include_once HOME . "/dates.php";
include_once CONTROLLERS . "/head.php";

$turnir = '541';
// Последний тур в турнире (в лиге).
$lastTur = getLastTur($turnir);

//Получаем всю статистику игроков
$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем данные всех игроков - ФИО, фото и т.д.
$dataAllPlayers = getDataPlayers($allStaticPlayers); 

// Отсортированный массив по рубрике Топ-Гравець
// $topGravetc = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'topgravetc', $lastTur);

function getTopGravtsi1($allStaticPlayers, $dataAllPlayers, $lastTur){

    // Преобразование массива
  $topPlayers = [];
  $row = [];

  foreach ($allStaticPlayers as $playerId => $matches) {
      $matchCount = count($matches); // Количество матчей
      $countGoals = array_sum(array_column($matches, 'count_goals'));
      $countAsists = array_sum(array_column($matches, 'count_asists'));
      
    //   $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
      $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
      $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
      $countRed_cards = array_sum(array_column($matches, 'red_cards'));  
      $matchIdKeys = array_keys($matches);
      // Ищем количество лучших игроков матча.
      $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
      $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;

      $totalGoals = array_sum(array_column($matches, 'count_goals'));
    //   $totalGolevoypas = array_sum(array_column($matches, 'count_asists'));
      $totalZagostrennia = array_sum(array_column($matches, 'zagostrennia'));
      $totalPasplus = array_sum(array_column($matches, 'pasplus'));
      $totalPasminus= array_sum(array_column($matches, 'pasminus'));
      $totalVtrata = array_sum(array_column($matches, 'vtrata'));
      $totalVstvor = array_sum(array_column($matches, 'vstvor'));
      $totalMimo = array_sum(array_column($matches, 'mimo'));
      $totalObvodkaplus = array_sum(array_column($matches, 'obvodkaplus'));
      $totalObvodkaminus = array_sum(array_column($matches, 'obvodkaminus'));
      $totalOtbor = array_sum(array_column($matches, 'otbor'));
      $totalOtbormin = array_sum(array_column($matches, 'otbormin'));
      $totalBlok = array_sum(array_column($matches, 'blok'));
      $totalSeyv = array_sum(array_column($matches, 'seyv'));
      $totalSeyvmin = array_sum(array_column($matches, 'seyvmin'));


      $totalKeySort = $totalGoals * 15 
          + $countAsists * 10 
          + $totalZagostrennia * 10
          + $totalPasplus * 3 
          - $totalPasminus * 3 
          - $totalVtrata * 3 
          + $totalVstvor * 7 
          - $totalMimo * 4 
          + $totalObvodkaplus * 5 
          - $totalObvodkaminus * 3 
          + $totalOtbor * 8 
          - $totalOtbormin * 5 
          + $totalBlok * 4 
          + $totalSeyv * 15 
          - $totalSeyvmin * 7;
          
          if($playerId == '125259') {
            dump($totalKeySort);
          }
      if(!is_array($totalKeySort)) {
  
          $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч
          
          $row = [
              'player_id' => $playerId,
              'match_count' => $matchCount,
              'total_key' => $totalKeySort,
              'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
              'match_ids' => implode(" ", $matchIdKeys),
      
              'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
              'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
              'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
              'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
              'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
              'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
  
              'count_goals' => $countGoals,
              'count_asists' => $countAsists,
            //   'golevoypas' => $countGolevoypas, 
              'yellow_cards' => $countYellowCards,
              'yellow_red_cards' => $countYellowRedCards,
              'red_cards' => $countRed_cards,
              'count_best_player_of_match' => $countBestPlayerOfMatch,
              'v9ky' => isset($dataAllPlayers[$playerId]['v9ky']) ? (int)$dataAllPlayers[$playerId]['v9ky'] : 0,
          ];
  
      } 

      



      // Добавляем значение для каждого матча
      foreach ($matches as $matchId => $stats) {
          if(isset($stats['tur'])){
              $row["match_{$stats[ 'tur' ]}_key"] = 
              $stats['count_goals'] * 15 
              + $stats['count_asists'] * 10
              + $stats['zagostrennia'] * 10
              + $stats['pasplus'] * 3 
              - $stats['pasminus'] * 3 
              - $stats['vtrata'] * 3
              + $stats['vstvor'] * 7 
              - $stats['mimo'] * 4 
              + $stats['obvodkaplus'] * 5
              - $stats['obvodkaminus'] * 3 
              + $stats['otbor'] * 8 
              - $stats['otbormin'] * 5
              + $stats['blok'] * 4 
              + $stats['seyv'] * 15 
              - $stats['seyvmin'] * 7;
          }
      }



      // Додаємо гравця у відповідний масив
      if ($row['v9ky']) {
        $outOfTopPlayers[] = $row;
    } else {
        $topPlayers[] = $row;
    }
  }

  // Сортируем игроков
  usort($topPlayers, function ($a, $b) use ($lastTur) {
      // 1. Сортировка по (total_key)
      if ($a['total_key'] != $b['total_key']) {
          return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
      }

      // 2. Сортировка по «Матчів» (count_matches)
      if ($a['match_count'] != $b['match_count']) {
          return ($b['match_count'] > $a['match_count']) ? 1 : -1; // По убыванию
      }
      // 3. Сортировка по последнему сыгранному матчу (total_3_match)
      if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {

      if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
          return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
      }

      }

      // Если все значения равны, оставить текущий порядок
      return 0;
  });

  // Присваиваем позиции
  $rank = 1; // Начальный порядковый номер
  foreach ($topPlayers as $index => &$player) {

      // если в последнем туре не играли оба савниваемых игрока
      if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
      
      // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
      if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
          $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
      ) {
          $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
      } else {
          $player['rank'] = $rank; // Новый ранг
      }

      } else {
      // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
      if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count']
      ) {
          $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
      } else {
          $player['rank'] = $rank; // Новый ранг
      }
      }
      $rank++; // Увеличиваем счетчик
  }

  if(isset($outOfTopPlayers)){
    $topPlayers = array_merge($topPlayers, $outOfTopPlayers);
}
  return $topPlayers;
    
}

$topGravetc = getTopGravtsi1($allStaticPlayers, $dataAllPlayers, $lastTur);

// Проверяем, есть ли значение у HTTP_REFERER  
if (isset($_SERVER['HTTP_REFERER'])) {  
    $previousPage = $_SERVER['HTTP_REFERER'];
  } else {
  $previousPage = $site_url;
  }

?>

<div class="statistic">
    <div class="container">
        <table id="top-gravetc" class="draggable-container">
        <caption>
            ТОП-Гравець
            <a class="statistic__link-to-home" href="<?= $previousPage?>">
                <img src="/css/components/statistic/assets/images/button-exit.svg" alt="exit">
            </a>
        </caption>
        <thead>
            <tr>
            <th>№</th>
            <th class="th_s" data-label="Ф">ФОТО</th>
            <th class="th_s" data-label="Л">ЛОГО</th>
            <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
            <th class="th_s" data-label="КПД">КПД</th>
            <th class="th_s" data-label="М">Матчів</th>
            <th class="th_s" data-label="КПД/М">КПД/М</th>
            <?php for($i = 1; $i <= 10; $i++): ?>
              <th data-label="<?= $i ?>}"><?= $i ?></th>
            <?php endfor ?>
            </tr>
        </thead>
        <tbody>
            <?php $int = 0; ?>
            <?php foreach($topGravetc as $key => $player): ?>
            <tr data-playerid="<?= $player['player_id'] ?>" data-matchid="<?= $player['match_ids'] ?>" data-serial-number="<?= $int ?>" >
                <td><?= isset($player['rank']) ? $player['rank'] : "?" ?></td>
                <td><img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo"></td>
                <td><img src="<?=$team_logo_path?>/<?= $player['team_photo'] ?>" alt="team-logo"></td>
                <td class="name-cell" data-v9ky="<?=  $player['v9ky'] ?>">
                    <?php if($player['v9ky']) : ?>
                        <img src="<?= IMAGES . '/player-v9ku.png' ?>" alt="">
                    <?php endif ?>
                    <?= $player['last_name'] ?> <?= $player['first_name'] ?>
                </td>
                <td><?= $player['total_key'] ?></td>
                <td><?= $player['match_count'] ?></td>
                <td><?= $player['key_per_match'] ?></td>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                <?php $stub = $i > $lastTur ? '?' : '-' ?>
                <td class="turs" <?= $i > $lastTur ? 'style="opacity:0.5"' : '' ?> ><?= isset($player["match_{$i}_key"]) ? $player["match_{$i}_key"]  : $stub  ?></td>
                <?php endfor ?>
            </tr>
            <?php $int ++?>
            <?php endforeach ?>
        </tbody>
        </table>
    </div>
</div>
<?php include_once  CONTROLLERS . "/footer.php" ?>