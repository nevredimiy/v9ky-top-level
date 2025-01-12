<?php 
    require_once CONTROLLERS . '/head.php';
    require_once CONTROLLERS . '/menu.php';
?>


<section class="team-page">
  <div class="container">
    <div class="team-page__header">
      <div id="teamCard" class="team-page__team-card">
        <p class="team-page__title"><?= isset($teamData['name']) ? $teamData['name'] : 'Команда не знайдена' ?></p>
        <img class="team-page__logo" src="<?= $team_logo_path . $teamData['pict'] ?>" alt="Team logo">

        <div class="team-page__stars">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
          <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="star">
        </div>

        <div class="team-page__skills">
          <div class="team-page__skills-container">
            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/star-icon.png" alt="skills-icon">
              <span><?= $bestGravetc ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/gloves-icon.png" alt="skills-icon">
              <span><?= $bestGolkiper ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/football-icon.png" alt="skills-icon">
              <span><?= $bestBombardi ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/boots-icon.png" alt="skills-icon">
              <span><?= $bestAssist ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/pitt-icon.png" alt="skills-icon">
              <span><?= $bestZhusnuk ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/player-icon.png" alt="skills-icon">
              <span><?= $bestDribling ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/rocket-ball-icon.png" alt="skills-icon">
              <span><?= $bestUdar ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/team-page/assets/images/ball-icon.png" alt="skills-icon">
              <span><?= $bestPas ?></span>
            </div>
          </div>
          <div class="team-page__skills-container">
            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/football-icon.png" alt="Загальна кількість забитих м'ячів" title="Загальна кількість забитих м'ячів">
              <span><?= $totalGoalsByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/boots-icon.png" alt="Загальна кількість гольових пасів" title="Загальна кількість гольових пасів">
              <span><?= $totalAsistByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/yellow-shirt.png" alt="">
              <span><?= $countInTurOfTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/golden-star-icon.png" alt="Загальна кількість гравець матчу" title="Загальна кількість гкравець матчу">
              <span><?= $totalBestPlayerByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/green-field-icon.png" alt="">
              <span><?= $totalMatchesByTeam ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/yellow-card-icon.png" alt="">
              <span><?= $totalYellowByTeam; ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/yellow-red-icon.png" alt="">
              <span><?= $totalYellowRedByTeam; ?></span>
            </div>

            <div class="team-page__skills-item">
              <img style="width: 100%" width="19" height="19" src="/css/components/card-player-full/assets/images/red-card-icon.png" alt="">
              <span><?= $totalRedByTeam ?></span>
            </div>
          </div>
        </div>

        <a data-target="teamCard" class="team-page__share-button save-image">
          <img src="/css/components/team-page/assets/images/share-icon.svg" alt="share">
        </a>
      </div>

      <div class="team-page__photo">
        <img src="/css/components/team-page/assets/images/team-photo.jpg" alt="">
      </div>
    </div>

    <div class="team-page__management">
      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads[5]['player_photo']) ? $player_face_path . $teamHeads[5]['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="manager-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/manager-icon.svg"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= isset($teamHeads[5]) ? $team_logo_path . $teamHeads[5]['team_photo'] : $player_face_path . 'avatar.jpg'  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads[5]['player_lastname'] ) ? $teamHeads[5]['player_lastname'] : 'менеджер' ?></span></p>
          <p><?=  isset($teamHeads[5]) ? $teamHeads[5]['player_firstname'] .' '.  $teamHeads[5]['player_middlename'] : '' ?></p>
        </div>
      </div>

      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads[4]['player_photo']) ? $player_face_path . $teamHeads[4]['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="trainer-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/coach-icon.png"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= isset($teamHeads[4]) ? $team_logo_path . $teamHeads[4]['team_photo'] : $player_face_path . 'avatar.jpg'  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads[4]['player_lastname'] ) ? $teamHeads[4]['player_lastname'] : 'тренер' ?></span></p>
          <p><?=  isset($teamHeads[4]) ? $teamHeads[4]['player_firstname'] .' '.  $teamHeads[4]['player_middlename'] : '' ?></p>
        </div>
      </div>
      <div class="team-page__manager">
        <img
          class="team-page__manager-photo"
          src="<?=  isset($teamHeads[0]['player_photo']) ? $player_face_path . $teamHeads[0]['player_photo'] : $player_face_path . 'avatar.jpg' ?>" 
          alt="capitan-photo"
        >
        <img 
          class="team-page__manager-logo"  
          src="/css/components/team-page/assets/images/cap-icon.svg"
          alt="manager"
        >
        <img
          class="team-page__manager-team-logo"
          src="<?= isset($teamHeads[0]) ? $team_logo_path . $teamHeads[0]['team_photo'] : $player_face_path . 'avatar.jpg'  ?>"
          alt="manager"
        >
        <div class="team-page__manager-label">
          <p><span> <?=  isset($teamHeads[0]['player_lastname'] ) ? $teamHeads[0]['player_lastname'] : 'місце для капітана' ?></span></p>
          <p><?=  isset($teamHeads[0]) ? $teamHeads[0]['player_firstname'] .' '.  $teamHeads[0]['player_middlename'] : '' ?></p>
        </div>
      </div>
      
    </div>

    <table class="team-page__calendar-table">
      <thead>
        <tr>
          <th>ТУР</th>
          <th>ДАТА</th>
          <th>ЧАС</th>
          <th>поле</th>
          <th colspan="5">матч</th>
        </tr>
      </thead>
      <tbody> 
        <?php foreach($matches as $match) :?>
        <tr>
            <td><?= $match['tur'] ?> тур</td>
            <td><?= $match['match_day'] ?></td>
            <td><?= $match['match_time'] ?></td>
            <td><?= $match['field_name']?></td>

            <td class="team__info team1" style="text-align: right">
                <span class="team_name truncate"><?=$match['team1'];?></span>
                <img style="margin-left: 10px" src="<?= $team_logo_path ?><?= $match['team1_photo'] ?>" alt="" width=30>
            </td>
            <?php if(empty($match['goals1'])):?>
              <td>VS</td>
            <?php else :?>
            <td><?= $match['goals1'] ?> :  <?= $match['goals2'] ?></td>
            <?php endif ?>

            <td class="team__info team2" style="text-align: left">
                <img style="margin-right: 10px" src="<?=$team_logo_path?><?= $match['team2_photo'] ?>" alt="" width=30>
                <span class="team_name truncate"><?=$match['team2'];?></span>
            </td>

        </tr>
        <?php endforeach ?>
      </tbody>
    </table>

    <div class="team-page__players">
    <?php $idx = 0 ?>
    <div style="display: none;"><?php  dump($players) ?></div>
    <?php foreach ($players as $player): ?>

      <?php          
          // Получение индивидуальной статистики. Player Card.
          if (isset($allStaticPlayers)) { $indStaticPlayer = getIndStaticPlayer($allStaticPlayers, $player['id']); }            
      ?>
      <!-- Если тренер или менеджер, то не показываем карточку игрока -->
      <?php if ( $player['amplua'] != 4 && $player['amplua'] != 5 && isset( $dataAllPlayers[$player['id']] ) )  :?>      

      <div id="playerCard<?= $idx ?>" class="card-player-full content-image">
          <?php 
            // Место в рейтинге
            $bestGravetc   = getBestPlayer($topGravetc, $player['id'], 'player_id');
            $bestGolkiper  = getBestPlayer($topGolkiper, $player['id'], 'player_id');
            $bestBombardi  = getBestPlayer($topBombardi, $player['id'], 'player_id');
            $bestAsists    = getBestPlayer($topAsists, $player['id'], 'player_id');
            $bestZhusnuk   = getBestPlayer($topZhusnuk, $player['id'], 'player_id');
            $bestDribling  = getBestPlayer($topDribling, $player['id'], 'player_id');
            $bestUdar      = getBestPlayer($topUdar, $player['id'], 'player_id');
            $bestPas       = getBestPlayer($topPas, $player['id'], 'player_id');

            // Определения значка в карточке игрока (верхний правый угол) - находим то, в чем игрок лучший.
            if($player['amplua'] != 1) {
              $arrayCPB = [$bestGravetc, $bestGolkiper, $bestBombardi, $bestAsists, $bestZhusnuk, $bestDribling, $bestUdar, $bestPas];
              $minValueCPB = min($arrayCPB);          
              $indexCPB = array_search($minValueCPB, $arrayCPB); 
            } else {
              $arrayCPB = [$bestGolkiper, $bestBombardi, $bestAsists, $bestZhusnuk, $bestDribling, $bestUdar, $bestPas];          
              $minValueCPB = min($arrayCPB);          
              $indexCPB = array_search($minValueCPB, $arrayCPB); 
              $indexCPB ++; 
            }

            // 0 - star-icon.png, 1 - gloves-icon.png, 2 - football-icon.png, 3 - boots-icon.svg, 4 - pitt-icon.svg, 5 - player-icon.svg
            // 6 - rocket-ball-icon.png, 7 - ball-icon.png
            $arrayCategoryPlayers = ['star-icon.png', 'gloves-icon.png', 'football-icon.png', 'boots-icon.png', 'pitt-icon.png', 'player-icon.png', 'rocket-ball-icon.png', 'ball-icon.png'];
            $categoryBestPlayers = $arrayCategoryPlayers[$indexCPB];
          ?>        
        
        <div class="card-player-full__photo">
          <img src="<?=$player_face_path?><?= $dataAllPlayers[$player['id']]['player_photo'] ?>" alt="photo_player">
          

          <img width="24" height="24" class="card-player-full__best-icon" src="/css/components/card-player-full/assets/images/<?= $categoryBestPlayers ?>" alt="icon">

          <img src="<?=$team_logo_path?><?= $dataAllPlayers[$player['id']]['team_photo'] ?>" alt="Team Logo">
          
          <?php if ($player['v9ky']):?>
            <img src="/css/components/team-page/assets/images/player-v9ku.png" alt="В9КУ">
            <?php elseif ($player['dubler']): ?>
            <img src="/css/components/team-page/assets/images/dubler-icon.png" alt="Дублер">
            <?php elseif ($player['vibuv']): ?>
            <img class="vibuv" src="/css/components/team-page/assets/images/vibuv.png" alt="Вибув">
          <?php endif ?>
            
          
        </div>
        

        <div class="card-player-full__name">
          <p><span><?= $dataAllPlayers[$player['id']]['last_name'] ?> </span></p>
          <p><?= $dataAllPlayers[$player['id']]['first_name'] ?></p>
          
        </div>

          <!--  иконки первого ряда: мяч, звездочка, футболка ... -->
        <ul class="card-player-full__top-statistic">
          
            <li>
              <img src="/css/components/card-player-full/assets/images/football-icon.png" alt="Кількість забитих м'ячів">
              <p><?= $indStaticPlayer['count_goals'] ?></p>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/golden-star-icon.png" alt="Кількість гольових пасів">
              <p><?= $indStaticPlayer['count_best_player_of_match'] ?></p>
            </li>   
            
            <li>
              <img src="/css/components/card-player-full/assets/images/yellow-shirt.png" alt="Кількість раів учасник збірна туру">
              <p><?= getCountInTur($playersOfAllTurs, $player['id']) ?> </p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/boots-icon.png" alt="">
              <p><?= $indStaticPlayer['count_asists'] ?></p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/green-field-icon.png" alt="">
              <p><?= $indStaticPlayer['count_matches'] ?></p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/yellow-card-icon.png" alt="">
              <p><?= $indStaticPlayer['yellow_cards'] ?></p>
            </li>

            <li>
              <img src="/css/components/card-player-full/assets/images/yellow-red-icon.png" alt="">
              <p><?= $indStaticPlayer['yellow_red_cards'] ?></p>
            </li>
          
            <li>
              <img src="/css/components/card-player-full/assets/images/red-card-icon.png" alt="">
              <p><?= $indStaticPlayer['red_cards'] ?></p>
            </li>

        </ul>

        <ul class="card-player-full__middle-statistic">
          <li>
            <p>Точність удару</p>
            <p><?= $indStaticPlayer['accuracy_of_kicking'] ?>%</p>
          </li>
          <li>
          <li>
            <p>Точність пасів</p>
            <p><?= $indStaticPlayer['accuracy_of_passing'] ?>%</p>
          </li>

          <li>
            <p>Вдалі обводки</p>
            <p><?= $indStaticPlayer['accuracy_of_dribbles'] ?>%</p>
          </li>

          <li>
            <p>Загострень за матч</p>
            <p><?= $indStaticPlayer['count_of_aggravations'] ?></p>
          </li>

          <li>
            <p>Відборів за матч</p>
            <p><?= $indStaticPlayer['accuracy_of_tackles'] ?></p>
          </li>
        </ul>

        <h4>Місце в рейтингу ліги</h4>


        <ul class="card-player-full__skills">
          <!-- Если не вратарь, то не показываем -->
          <?php if($dataAllPlayers[$player['id']]['amplua'] != 1): ?>  
            <li>
              <img src="/css/components/card-player-full/assets/images/star-icon.png" alt="skills-icon" title="Топ-Гравець">
              <span><?= $bestGravetc ?></span>
            </li>
          <?php endif ?>
          
          <!-- Если вратарь, то показываем -->
          <?php if($dataAllPlayers[$player['id']]['amplua'] == 1): ?>
            <li>
              <img src="/css/components/card-player-full/assets/images/gloves-icon.png" alt="skills-icon" title="Топ-Голкіпер">
              <span><?= $bestGolkiper ?></span>
            </li>
          <?php endif ?>            
          
          <li>
            <img src="/css/components/card-player-full/assets/images/football-icon.png" alt="skills-icon" title="Топ-Бомбардир">              
            <span><?= $bestBombardi ?></span>
          </li>

          <li>
            <img src="/css/components/card-player-full/assets/images/boots-icon.png" alt="skills-icon" title="Топ-Асист">
            <span><?= $bestAsists ?></span>
          </li>

          <li>
            <img src="/css/components/card-player-full/assets/images/pitt-icon.png" alt="skills-icon" title="Топ-Захист">
            <span><?= $bestZhusnuk ?></span>
          </li>

          <li>
            <img src="/css/components/card-player-full/assets/images/player-icon.png" alt="skills-icon" title="Топ-Дріблінг">
            <span><?= $bestDribling ?></span>
          </li>

          <li>
            <img src="/css/components/card-player-full/assets/images/rocket-ball-icon.png" alt="skills-icon" title="Топ-Удар">
            <span><?= $bestUdar ?></span>
          </li>

          <li>
            <img src="/css/components/card-player-full/assets/images/ball-icon.png" alt="skills-icon" title="Топ-Пас">
            <span><?= $bestPas ?></span>
          </li>
        </ul>

        <div class="card-player-full__footer">
          <img src="/css/components/card-player-full/assets/images/v9ku-logo-on-white-back.png" alt="">
          
          <div>
            
            <p><?= $seasonName ?></p>
            
          </div>
          
          <!-- Если игрок голкипер -->
          <?php if($player['amplua'] == 1): ?>
            <img src="/css/components/team-page/assets/images/goalkeeper-icon.svg" alt="" title="Голкіпер">
            <?php else : ?>
              <img src="/css/components/team-page/assets/images/player-boots-icon.svg" alt="" title="Гравець">
              <?php endif ?>
              
            </div>
            <div class="card-player-full__share">
              <button data-target="playerCard<?= $idx ?>" data-card-player class="card-player-full__button-share save-image">
                <img 
                src="/css/components/card-player-full/assets/images/button-share-icon.svg" 
                alt="button-share-icon"
                >
              </button>
            </div>
            <div class="card-player-full__footer">
        </div>

      </div><!-- card-player-full__photo -->
      <?php endif ?>
      <?php $idx++ ?>
    <?php endforeach?>

    </div><!-- team-page__players -->

    <div class="team-page__reference">
      <ul class="team-page__reference--top">
        <li>
          <img src="/css/components/team-page/assets/images/star-icon.png" alt="">
          <p>ККД гравця</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/gloves-icon.png" alt="">
          <p>ККД голкіпера</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/football-icon.png" alt="">
          <p>Бомбардири</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/boots-icon.png" alt="">
          <p>Асисти</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/pitt-icon.png" alt="">
          <p>Захист</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/player-icon.png" alt="">
          <p>Дріблінг</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/ball-icon.png" alt="">
          <p>Пас</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/rocket-ball-icon.png" alt="">
          <p>Удар</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/manager-icon.png" alt="">
          <p>Президент команди</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/coach-icon.png" alt="">
          <p>Тренер</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/goalkeeper-icon.svg" alt="" title="Голкіпер">
          <p>Голкіпер</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/player-boots-icon.svg" alt="" title="Гравець">
          <p>Гравець в полі</p>
        </li>

        <li>
          <img src="/css/components/team-page/assets/images/cap-icon.svg" alt="">
          <p>Капітан</p>
        </li>

      </ul>

      <ul class="team-page__reference--bottom">
        <li>
          <div>
            <img src="/css/components/team-page/assets/images/football-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість голів за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/boots-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість гольових пасів за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/dubler-icon.png" alt="">
            <span></span>
          </div>
          <p>Дублери</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/player-v9ku.png" alt="">
            <span></span>
          </div>
          <p>Гравець «В9КУ» (орендований гравець)</p>
        </li>

        <li>
          <div>
            <img class="team-page__card-icon" src="/css/components/team-page/assets/images/red-card-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість червоних карток за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/green-field-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість проведених матчів за сезон</p>
        </li>

        <li>
          <div>
            <img class="team-page__card-icon" src="/css/components/team-page/assets/images/yellow-card-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість жовтих карток за сезон</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/golden-star-icon.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість отриманих номінацій «Гравець Матчу»</p>
        </li>

        <li>
          <div>
            <img src="/css/components/team-page/assets/images/yellow-shirt.png" alt="">
            <span>1</span>
          </div>
          <p>Кількість потраплянь в збірну туру за сезон</p>
        </li>
      </ul>
    </div> <!-- team-page__reference -->
  </div><!-- container --> 
</section><!-- team-page --> 

<?php 
    require_once CONTROLLERS . '/footer.php';
?>