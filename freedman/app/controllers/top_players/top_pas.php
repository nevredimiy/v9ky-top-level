<?php

include_once HOME . "/dates.php";
include_once CONTROLLERS . "/head.php";


// Последний тур в турнире (в лиге).
$lastTur = getLastTur($turnir);

//Получаем всю статистику игроков
$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем данные всех игроков - ФИО, фото и т.д.
$dataAllPlayers = getDataPlayers($allStaticPlayers); 

// Отсортированный массив по рубрике Топ-Пас
$topPas = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'pas', $lastTur);

// Проверяем, есть ли значение у HTTP_REFERER  
if (isset($_SERVER['HTTP_REFERER'])) {  
    $previousPage = $_SERVER['HTTP_REFERER'];
  } else {
  $previousPage = $site_url;
  }

?>

<div class="statistic">
    <div class="container">
        <table id="top-pas" class="draggable-container">
        <caption>
            ТОП-Пас
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
            <th class="th_s" data-label="Т">Тотал</th>
            <th class="th_s" data-label="М">Матчів</th>
            
            <th class="th_s" data-label="П+">Пас+ (+1)</th>
            <th class="th_s" data-label="П-">Пас- (-1)</th>
            <?php for($i = 1; $i <= 10; $i++): ?>
              <th data-label="<?= $i ?>}"><?= $i ?></th>
            <?php endfor ?>
            </tr>
        </thead>
        <tbody>
            <?php $int = 0; ?>        
            <?php foreach($topPas as $player): ?>
            <tr data-playerid="<?= $player['player_id'] ?>" data-matchid="<?= $player['match_ids'] ?>" data-serial-number="<?= $int ?>" >
                <td><?= isset($player['rank']) ? $player['rank'] : "?" ?></td>
                <td><img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo"></td>
                <td><img src="<?=$team_logo_path?>/<?= $player['team_photo'] ?>" alt="team-logo"></td>
                <td  class="name-cell"><?= $player['last_name'] ?> <?= $player['first_name'] ?></td>
                <td><?= $player['total_key'] ?></td>
                <td><?= $player['match_count'] ?></td>
                
                <td><?= $player['pasplus'] ?></td>
                <td><?= $player['pasminus']*3 ?></td>
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
<?php include_once "freedman/footer.php" ?>