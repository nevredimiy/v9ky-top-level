<?php

include_once HOME . "/dates.php";
include_once CONTROLLERS . "/head.php";


// Последний тур в турнире (в лиге).
$lastTur = getLastTur($turnir);

//Получаем всю статистику игроков
$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем данные всех игроков - ФИО, фото и т.д.
$dataAllPlayers = getDataPlayers($allStaticPlayers); 

// Отсортированный массив по рубрике Топ-Захистник
$topZhusnuk = getTopZhusnuks($allStaticPlayers, $dataAllPlayers, 'zahusnuk', $lastTur);

// Проверяем, есть ли значение у HTTP_REFERER  
if (isset($_SERVER['HTTP_REFERER'])) {  
  $previousPage = $_SERVER['HTTP_REFERER'];
} else {
$previousPage = $site_url;
}

?>

<div class="statistic">
    <div class="container">
    <table id="top-zahustnuk" class="draggable-container">
      <caption>
        ТОП-ЗАХИСНИК
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
          <th class="th_s" data-label="ВБ">Відбір + Блок</th>
          <th class="th_s" data-label="М">Матчів</th>
          <th class="th_s" data-label="В+Б/М">В+Б/М</th>
          <?php for($i = 1; $i <= 10; $i++): ?>
              <th data-label="<?= $i ?>}"><?= $i ?></th>
          <?php endfor ?>
        </tr>
      </thead>
      <tbody>
        <?php $int = 0; ?>
        <?php foreach($topZhusnuk as $player): ?>
          <?php if($player['total_key'] <= 0) continue;   ?>
        <tr data-playerid="<?= $player['player_id'] ?>" data-matchid="<?= $player['match_ids'] ?>" data-serial-number="<?= $int ?>" >
            <td><?= isset($player['rank']) ? $player['rank'] : "?" ?></td>
            <td><img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo"></td>
            <td><img src="<?=$team_logo_path?>/<?= $player['team_photo'] ?>" alt="team-logo"></td>
            <td class="name-cell">
              <?php if(isset($player['v9ky']) && $player['v9ky']) : ?>
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