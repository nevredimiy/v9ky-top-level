<?php
// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

include_once HOME . "/dates.php";
include_once CONTROLLERS . "/head.php";


// Последний тур в турнире (в лиге).
$lastTur = getLastTur($turnir);

//Получаем всю статистику игроков
$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем данные всех игроков - ФИО, фото и т.д.
$dataAllPlayers = getDataPlayers($allStaticPlayers); 

// Отсортированный массив по рубрике Топ-Бомбардир
$topBombardi = getTopBombardir($allStaticPlayers, $dataAllPlayers, 'count_goals', $lastTur);

// Проверяем, есть ли значение у HTTP_REFERER  
if (isset($_SERVER['HTTP_REFERER'])) {  
  $previousPage = $_SERVER['HTTP_REFERER'];
} else {
$previousPage = $site_url;
}


?>

<div class="statistic">
    <div class="container">
    <table id="top-bombardir" class="draggable-container">
      <caption>
        ТОП-Бомбардир
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
          <th class="th_s" data-label="Г">Голів</th>
          <th class="th_s" data-label="М">Матчів</th>
          <th class="th_s" data-label="Г/М">Г/М</th>
          <?php for($i = 1; $i <= 10; $i++): ?>
              <th data-label="<?= $i ?>}"><?= $i ?></th>
          <?php endfor ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($topBombardi as $player): ?>
          <?php if($player['total_key'] <= 0) continue; ?>
        <tr data-playerid="<?= $player['player_id'] ?>" data-matchid="<?= $player['match_ids'] ?>" >
          <td><?= isset($player['rank']) ? $player['rank'] : "?" ?></td>
          <td><img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo"></td>
          <td><img src="<?=$team_logo_path?>/<?= $player['team_photo'] ?>" alt="team-logo"></td>
          <td class="name-cell">
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
        <?php endforeach ?>
      </tbody>
    </table>
    </div>
</div>

<?php include_once  CONTROLLERS . "/footer.php" ?>