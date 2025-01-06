<section class="anons">
    <div class="anons__container">
        <div class="anons__share">
            <button class="anons__share-btn"><img src="css/components/card-of-matches/assets/images/share-icon.svg" alt="Зберегти зображення"></button>
        </div>
        <div class="anons__head">
            <div class="anons__teams">
                <div class="anons__teams-content">
                    <div class="anons__logo logo-team1">
                        <img src="<?= $team_logo_path ?>/<?= $dataMatch['team1_photo'] ?>">
                        <div class="anons__team-name"><?= $dataMatch['team1_name'] ?></div>
                    </div>
                    <div class="match-state state">
                        <?php if($dataMatch['goals1'] != null) :?>
                        <div class="state__score"><?= $dataMatch['goals1'] ?></div>
                        <div class="state__score-middle">:</div>
                        <div class="state__score"><?= $dataMatch['goals2'] ?></div>
                        <?php else: ?>
                            <div class="state__score-middle grey-text">VS</div>
                        <?php endif ?>
                    </div>
                    <div class="anons__logo logo-team2">
                        <img src="<?= $team_logo_path ?>/<?= $dataMatch['team2_photo'] ?>">
                        <div class="anons__team-name"><?= $dataMatch['team2_name'] ?></div>
                    </div>
                </div>
                <?php if($dataMatch['goals1'] != null) :?>
                <div class="state__text">Матч завершено</div>
                <?php endif ?>
            </div>
        </div>
        <div class="anons__body">
            <h2 class="anons__title">Анонс</h2>
            <div class="anons__text"><?= $dataMatch['anons'] ?></div>
            <div class="anons__history-meet">
                <?php if(!empty($historyMeets)): ?>
                <table class="table">
                        <div class="anons__history-meet table-title">Історія зустрічей між собою</div>
                    <thead>
                        <tr>
                            <th>Сезон</th>
                            <th>Ліга</th>
                            <th>Матчі</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <? foreach($historyMeets as $match): ?>
                        <tr>
                            <td><?= $match['season_name'] ?></td>
                            <td><?= $match['liga_name'] ?></td>
                            <td><?= $match['team1_name'] ?>
                                <span class="text-red"><?= $match['goals1'] ?>:<?= $match['goals2'] ?></span>
                                <?= $match['team2_name'] ?></td>
                        </tr>
                        <?php endforeach ?>
                        
                    </tbody>
                </table>
                
                <table class="table__count">
                    <tbody>
                        <tr>
                            <td><?= $team1Wins ?></td>
                            <td>Перемог</td>
                            <td><?= $team2Wins ?></td>
                        </tr>
                        <tr>
                            <td><?= $draws ?></td>
                            <td>Нічиїх</td>
                            <td><?= $draws ?></td>
                        </tr>
                        <tr>
                            <td><?= $countGoals1 ?></td>
                            <td>Забитих м'ячів</td>
                            <td><?= $countGoals2 ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php else : ?>
                    <div class="table-title title-info">Дані команди раніше не зустрічались</div>
                <?php endif ?>
            </div>
            <div class="anons__totalizator">
                <div class="totalizator totalizator-text">Шанси команд на думку редакції</div>
                <div class="totalizator totalizator-t1">
                    <div class="totalizator__item value">П1</div>
                    <div class="totalizator__item percent"><?= round($percentages['team1Win']) ?>%</div>
                </div>
                <div class="totalizator totalizator-x">
                    <div class="totalizator__item value">Х</div>
                    <div class="totalizator__item percent"><?= round($percentages['draw']) ?>%</div>
                </div>
                <div class="totalizator totalizator-t2">
                    <div class="totalizator__item value">П2</div>
                    <div class="totalizator__item percent"><?= round($percentages['team2Win']) ?>%</div>
                </div>
            </div>
        </div>
    </div>
</section>

