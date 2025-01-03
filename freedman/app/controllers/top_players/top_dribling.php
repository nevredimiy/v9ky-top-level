<?php

include_once HOME . "/dates.php";
include_once CONTROLLERS . "/head.php";
include_once HOME . "/slider_spons.php";
include_once CONTROLLERS . "/menu.php";

// Получаем количество сыграных туров в турнире. Это нужно для отображения в таблице знака вопроса для несыграных матчей. 
$queryLastTur = $db->Execute(
  "SELECT tur as last_tur FROM `v9ky_match` WHERE `canseled`=1 and `turnir` = $turnir order by tur desc limit 1"
);

// Последний тур в турнире (в лиге).
$lastTur = intval($queryLastTur->fields[0]);

// Получаем данные из БД. Статискика всех игроков учавствуюих в текущей лиге. Статистика вся, кроме забитых голов
$queryStaticPlayers = $db->Execute( 
  "SELECT 
    m.tur, 
    s.player, 
    s.matc, 
    s.seyv, 
    s.seyvmin, 
    s.vstvor, 
    s.mimo, 
    s.pasplus, 
    s.pasminus, 
    s.otbor, 
    s.otbormin, 
    s.obvodkaplus, 
    s.obvodkaminus, 
    s.golevoypas, 
    s.zagostrennia, 
    s.vkid, 
    s.vkidmin, 
    s.blok, 
    s.vtrata,
    (SELECT COUNT(*) AS count_goals FROM v9ky_gol g WHERE g.player= s.player and g.matc = s.matc) AS count_goals,
    (SELECT COUNT(*) AS count_asists FROM v9ky_asist a WHERE a.player= s.player and a.matc = s.matc) AS count_asists,
    (SELECT COUNT(*) AS yellow_cards FROM v9ky_yellow y WHERE y.player= s.player and y.matc = s.matc) AS yellow_cards,
    (SELECT COUNT(*) AS yellow_red_cards FROM v9ky_yellow_red yr WHERE yr.player= s.player and yr.matc = s.matc) AS yellow_red_cards,
    (SELECT COUNT(*) AS red_cards FROM v9ky_red r WHERE r.player= s.player and r.matc = s.matc) AS red_cards
FROM `v9ky_sostav` s
LEFT JOIN `v9ky_match` m ON m.id = s.matc
WHERE s.`player` IN (
    SELECT `id` 
    FROM `v9ky_player` 
    WHERE `team` IN (
        SELECT `id` 
        FROM `v9ky_team` 
        WHERE `turnir` = $turnir
    )
)"
);

// Массив для cтастистики игроков учавствуюих в текущей лиге
$allStaticPlayers = array(); 

// Заполняем массив статистикой игроков, кроме статистики забитых голов
while(!$queryStaticPlayers->EOF){

  foreach ( $queryStaticPlayers->fields as $key => $value ) {
    if (is_string($key)){
      $allStaticPlayers[$queryStaticPlayers->fields['player']][$queryStaticPlayers->fields['matc']][$key] = $value;
    }
  }

  $queryStaticPlayers->MoveNext();
}

// Массив только идентификаторов игроков
$allPlayersId = array_keys($allStaticPlayers);

// Делаем строку для апроса в БД.
$strAllPlayersId = implode(", ", $allPlayersId);

// Получаем данные по id - ФИО, фото,  и т.д.
$queryAllPlayersData = $db->Execute(
  "SELECT 
      p.id AS player_id,
      p.team AS team_id,
      p.man AS man_id,
      p.amplua AS amplua,
      p.v9ky AS v9ky,
      p.dubler AS dubler,
      p.vibuv AS vibuv,
      m.name1 AS last_name,
      m.name2 AS first_name,
      mf.pict AS player_photo,
      t.pict AS team_photo,
      t.name AS team_name
  FROM 
      v9ky_player p
  LEFT JOIN 
      v9ky_man m ON p.man = m.id
  LEFT JOIN 
      v9ky_man_face mf ON m.id = mf.man
  LEFT JOIN 
      v9ky_team t ON p.team = t.id
  WHERE 
      p.id IN ($strAllPlayersId)  
");  

// Данные всех игроков типа Имя, Фамилия, Фото и т.д
$dataAllPlayers = [];  

// Меняем структуру массива - для удобства работы с ним
while(!$queryAllPlayersData->EOF){
  foreach ($queryAllPlayersData as $key => $value) {
    $playerId = $value['player_id'];
    if(!isset($dataAllPlayers[$playerId])) {      
        $dataAllPlayers[$playerId] = $value;             
    }
  }
  $queryAllPlayersData->MoveNext();
}

// Отсортированный массив по рубрике Топ-Дриблинг
$topDribling = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'dribling', $lastTur);


?>

<div class="statistic">
    <div class="container">
    <table id="top-dribling" class="draggable-container">
      <caption>
        ТОП-Дріблінг
        <button>
          <img src="/css/components/statistic/assets/images/button-exit.svg" alt="exit">
        </button>
      </caption>

      <thead>
        <tr>
          <th>№</th>
          <th class="th_s" data-label="Ф">ФОТО</th>
          <th class="th_s" data-label="Л">ЛОГО</th>
          <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
          <th class="th_s" data-label="Т">Тотал</th>
          <th class="th_s" data-label="М">Матчів</th>
          <th class="th_s" data-label="О+">Обвод +</th>
          <th class="th_s" data-label="О-">Обвод -</th>
          <?php for($i = 1; $i <= 10; $i++): ?>
              <th data-label="<?= $i ?>}"><?= $i ?></th>
          <?php endfor ?>
        </tr>
      </thead>
      <tbody>
        <?php $int = 0; ?>        
        <?php foreach($topDribling as $player): ?>
        <tr data-playerid="<?= $player['player_id'] ?>" data-matchid="<?= $player['match_ids'] ?>" data-serial-number="<?= $int ?>" >
            <td><?= isset($player['rank']) ? $player['rank'] : "?" ?></td>
            <td><img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo"></td>
            <td><img src="<?=$team_logo_path?>/<?= $player['team_photo'] ?>" alt="team-logo"></td>
            <td class="name-cell"><?= $player['last_name'] ?> <?= $player['first_name'] ?></td>
            <td><?= $player['total_key'] ?></td>
            <td><?= $player['match_count'] ?></td>
            <td><?= $player['obvodkaplus'] ?></td>
            <td><?= $player['obvodkaminus'] ?></td>
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