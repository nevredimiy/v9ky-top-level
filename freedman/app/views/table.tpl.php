
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

    <?php foreach ($groupedTeams as $group => $teams): ?>
        
        <h2 class="table-league__title title title--inverse">
            <span>Турнірна таблиця</span>
            <span><?= $teams[0]['turnir_name'] ?></span>
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
                                <?php for ($i = 1; $i <= count($teams); $i++): ?>
                                    <th><span class="cell--score"><?= $i ?></span></th>
                                <?php endfor; ?>
                                <th><span class="cell cell--games">І</span></th>
                                <th><span class="cell cell--win">В</span></th>
                                <th><span class="cell cell--draw">Н</span></th>
                                <th><span class="cell cell--defeat">П</span></th>
                                <th class="td-scored"><span class="cell cell--scored">Г</span></th>
                                <th><span class="cell cell--total">О</span></th>
                            </tr>

                            <?php  foreach ($teams as $i => $team) : ?>
                                <?php $id = $team['id']; ?>
                                <?php $team_id = $team['id']; ?>
                                <tr>
                                    <td><span class="cell"><?= $i + 1 ?></span></td>
                                    <td><img width="18" height="18" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $team['logo'] ?>"></td>
                                    <td><a href="<?= $site_url . '/' . $tournament .'/team_page/id/' . $team_id ?>"><span class="cell--team"><?= $stats[$id]['name']?></span></a></td>

                                    <?php foreach ($teams as $t): ?>                                                            
                                      <td>
                                        <span class="cell--score<?= $t['id'] == $team_id ? ' cell--own' : '' ?>">
                                          <?= empty( $matchResults[$id][$teamIndex[$t['id']]] ) ? '-' : $matchResults[$id][$teamIndex[$t['id']]] ?>
                                        </span>
                                      </td>                                        
                                    <?php endforeach ?>

                                    <td><span class="cell cell--games"><?= $stats[$id]['games']?></span></td>
                                    <td><span class="cell cell--win"><?= $stats[$id]['wins']?></span></td>
                                    <td><span class="cell cell--draw"><?= $stats[$id]['draws']?></span></td>
                                    <td><span class="cell cell--defeat"><?= $stats[$id]['losses']?></span></td>
                                    <td class="td-scored"><span class="cell cell--scored"><?= $stats[$id]['goals_for']?> - <?= $stats[$id]['goals_against'] ?> </span></td>
                                    <td><span class="cell cell--total"><?= $stats[$id]['points']?></span></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="swiper-scrollbar-table"></div>
        </div>
    <?php endforeach; ?>
</section>

