
<section data-cup-mode="<?= $cupMode ?>" class="table-league">

<!-- Если кубок, то выводим реультат кубка -->
    <?php if(isset($cupData) && $cupData):?>
        <div class="cup__block">
            <div class="cup__block-wrap">
                <?php foreach($cupData as $key => $cup): ?>
                    <h2><?= $key ?></h2>
                    <?php foreach($cup as $match): ?>
                        <div class="">
                            
                            <div class="">
                                <?php if(isset($match['cup1'])): ?>
                                    <img width="20" height="30" src="<?= IMAGES . '/' . $match['cup1'] ?>" alt="">
                                <?php endif ?>
                                <?= $match['team1_name']?>
                            </div>
                            <div class=""><?= $match['goals1']?>:<?= $match['goals2']?></div>
                            <div class="">
                                <?= $match['team2_name']?>
                                <?php if(isset($match['cup2'])): ?>
                                    <img width="20" height="30" src="<?= IMAGES . '/' . $match['cup2'] ?>" alt="">
                                <?php endif ?>    
                            </div>   
                                                            
                        </div>
                    <?php endforeach ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php foreach ($groups ?: [''] as $group): ?>
        <?php 
        // Фильтруем команды, оставляя только те, что принадлежат текущей группе
        $group_teams = array_filter($stats, function($team) use ($group) {
            return $team['group'] == $group;
        });

        // Считаем количество команд в группе
        $team_count = count($group_teams);
        ?>
        
        <h2 class="table-league__title title title--inverse">
            <span>Турнірна таблиця</span>
            <span><?= $result[0]['turnir_name'] ?></span>
            <?= $group ? "<span>Група $group</span>" : '' ?>
        </h2>

        <div class="swiper swiper-table">
            <div class="swiper-wrapper">
                <div class="swiper-slide swiper-slide--table swiper-slide-active">
                    <table class="table-league__table">
                        <tbody>
                            <tr>
                                <th><span>М</span></th>
                                <th><span class="cell--team-logo"></span></th>
                                <th><span class="cell--team">Команда</span></th>
                                <?php for ($i = 1; $i <= $team_count; $i++): ?>
                                    <th><span class="cell--score"><?= $i ?></span></th>
                                <?php endfor; ?>
                                <th><span class="cell cell--games">І</span></th>
                                <th><span class="cell cell--win">В</span></th>
                                <th><span class="cell cell--draw">Н</span></th>
                                <th><span class="cell cell--defeat">П</span></th>
                                <th class="td-scored"><span class="cell cell--scored">Г</span></th>
                                <th><span class="cell cell--total">О</span></th>
                            </tr>

                            <?php $position = 1; ?>
                            <?php foreach ($group_teams as $team_id => $stat): ?>
                                <tr>
                                    <td><span class="cell"><?= $position ?></span></td>
                                    <td><img width="18" height="18" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $stat['logo'] ?>"></td>
                                    <td><a href="<?= $site_url . '/' . $tournament .'/team_page/id/' . $team_id ?>"><span class="cell--team"><?= $stat['name']?></span></a></td>

                                    <?php foreach ($group_teams as $key => $value): ?>
                                        <?php if ($key === $team_id): ?>  
                                            <td><span class="cell--score cell--own"></span></td> 
                                        <?php else: ?>                                    
                                            <td><span class="cell--score"><?= isset($stat['matches_home'][$key]) ? $stat['matches_home'][$key] : '-' ?></span></td>
                                        <?php endif ?>
                                    <?php endforeach ?>

                                    <td><span class="cell cell--games"><?= $stat['games']?></span></td>
                                    <td><span class="cell cell--win"><?= $stat['wins']?></span></td>
                                    <td><span class="cell cell--draw"><?= $stat['draws']?></span></td>
                                    <td><span class="cell cell--defeat"><?= $stat['losses']?></span></td>
                                    <td class="td-scored"><span class="cell cell--scored"><?= $stat['goals_scored']?> - <?= $stat['goals_conceded']?> </span></td>
                                    <td><span class="cell cell--total"><?= $stat['points']?></span></td>
                                </tr>
                                <?php $position++; ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="swiper-scrollbar-table"></div>
        </div>
    <?php endforeach; ?>
</section>

