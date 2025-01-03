
<section class="controls">
    <div class="controls__container">
        <div class="controls__share">
            <button class="controls__share-btn"><img src="css/components/match-stats/assets/images/button-share-icon.svg" alt="Зберегти зображення"></button>
        </div>
        <div class="controls__head">
            <div class="controls__head-title">Статистика матчу</div>
            <div class="controls__head-info">
                <div class="info"><?= $dataMatch['season'] ?></div>
                <div class="info"><?= $dataMatch['tur'] ?> тур</div>
                <div class="info"><?= $dataMatch['field_name'] ?></div>
                <div class="info"><?= $dataMatch['match_day'] ?></div>
                <div class="info"><?= $dataMatch['match_time'] ?></div>
            </div>
            <div class="controls__teams">
                <div class="controls__teams-content">
                    <div data-team1-id="<?= $dataMatch['team1_id'] ?>" class="controls__logo logo-team1">
                        <img src="<?= $team_logo_path ?><?= $dataMatch['team1_photo'] ?>">
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
                    <div data-team2-id="<?= $dataMatch['team2_id'] ?>" class="controls__logo logo-team2">
                        <img src="<?= $team_logo_path ?><?= $dataMatch['team2_photo'] ?>">
                    </div>
                </div>
                <?php if($dataMatch['goals1'] != null) :?>
                <div class="state__text">Матч завершено</div>
                <?php endif ?>
            </div>
        </div>

        <div class="controls__action tabs">
            <nav class="tabs__items">
                <button class="controls__btn tabs__item btn-green">Протокол матчу</button>
                <button class="controls__btn tabs__item btn-green">Склади команд</button>
                <button class="controls__btn tabs__item btn-green">Статистика матчу</button>
            </nav>
            <div class="tabs__body">
                <div class="tabs__block">
                    <table class="three-row">
                        <tbody>
                            <?php foreach($matchReport as $event): ?>
                            <tr>
                                <td>
                                    <?php $player_name = $event['team_id'] == $dataMatch['team1_id'] ? "{$event['lastname']} {$event['firstname']}" : '' ?>
                                    <?php if($player_name != ''): ?>
                                        <?= $player_name ?>
                                        <img src="<?= IMAGES . '/' . $eventType[$event['event_type']] ?>" alt="">
                                    <?php endif ?>
                                </td>
                                <td><?= $event['event_time']?>'</td>
                                <td>
                                    <?php $player_name = $event['team_id'] == $dataMatch['team2_id'] ? "{$event['lastname']} {$event['firstname']}" : '' ?>
                                    <?php if($player_name != ''): ?>
                                        <img src="<?= IMAGES . '/' . $eventType[$event['event_type']] ?>" alt="">
                                        <?= $player_name ?>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                            
                        </tbody>
                    </table>
                </div>
                <div class="tabs__block">
                    <div class="tabs_sostav">
                        <?php if(!empty($team1Composition)):?>
                        <table class="table__team table__team1">
                           <?php foreach($team1Composition as $team1): ?>
                            <tr>
                                <td><?= $team1['lastname'] ?> <?= $team1['firstname'] ?></td>
                                <td><?= $team1['nomer'] ?></td>
                            </tr>
                           <?php endforeach ?>
                           <?php if(!empty($trainerAndManager1)): ?>
                            <tr class="trainer">
                                <td colspan="2">Тренер</td>
                            </tr>
                            <tr>
                                <td colspan="2"><?= $trainerAndManager1[0]['lastname'] ?> <?= $trainerAndManager1[0]['firstname'] ?></td>
                            </tr>
                            <?php endif ?>
                        </table>
                        <table class="table__team table__team2">
                            <?php foreach($team2Composition as $team2): ?>
                            <tr>
                                <td><?= $team2['nomer'] ?></td>
                                <td><?= $team2['lastname'] ?> <?= $team2['firstname'] ?></td>
                            </tr>
                            <?php endforeach ?>
                            <?php if(!empty($trainerAndManager2)): ?>
                            <tr class="trainer">
                                <td colspan="2">Тренер</td>
                            </tr>
                            <tr>
                                <td colspan="2"><?= $trainerAndManager2[0]['lastname'] ?> <?= $trainerAndManager2[0]['firstname'] ?></td>
                            </tr>
                            <?php endif ?>
                        </table>
                        <?php else :?>
                            <div class="danger-info">Дані складу команд ще не внесені адміністратором</div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="tabs__block">
                    <div class="tabs__block-stats stats ball__procession">
                        <div class="stats__title">Володіння м'ячем</div>
                        <div class="stats__view">
                            <div class="stats__view-blue" data-value="10">10</div>
                            <div class="stats__view-red" data-value="90">90</div>
                        </div>
                    </div>
                    <div class="tabs__block-stats stats goal__area">
                        <div class="stats__title">Удари в площину воріт</div>
                        <div class="stats__view">
                            <div class="stats__view-blue" data-value="30">30</div>
                            <div class="stats__view-red" data-value="70">70</div>
                        </div>
                    </div>
                    <div class="tabs__block-stats stats passes">
                        <div class="stats__title">Вдалі паси</div>
                        <div class="stats__view">
                            <div class="stats__view-blue" data-value="50">50</div>
                            <div class="stats__view-red" data-value="50">50</div>
                        </div>
                    </div>
                    <div class="tabs__block-stats stats dribbling">
                        <div class="stats__title">Вдалі обводки</div>
                        <div class="stats__view">
                            <div class="stats__view-blue" data-value="60">60</div>
                            <div class="stats__view-red" data-value="40">40</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
// -- MATCH-STATS
$('.tabs__block:first').show();
$('.tabs__item:first').addClass('btn-red deactive');

$('.tabs__item').click(function() {
    index = $(this).index();
    $('.tabs__item').removeClass('btn-red deactive');
    $('.tabs__block').hide();
    $(this).addClass('btn-red deactive');
    $('.tabs__block').eq(index).show();
});

$('[data-value]').each(function () {
    var $this = $(this); // Текущий элемент
    var value = parseInt($this.data('value'), 10); // Получаем значение из атрибута data-value

    if (value >= 0 && value <= 100) {
        $this.css('width', value + '%'); // Устанавливаем ширину блока
    } else {
        console.error('Недопустимое значение: ', value); // Логируем ошибку, если значение выходит за диапазон
    }
});

});
</script>
