
<section class="controls">
    <div id="match-static" class="controls__container">
        <div class="controls__share">
            <button class="controls__share-btn save-image" data-target="match-static">
                <img src="css/components/match-stats/assets/images/button-share-icon.svg" alt="Зберегти зображення">
            </button>
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
                                <td data-player-id="<?= $event['player_id']?>">
                                    <?php $player_name = $event['team_id'] == $dataMatch['team1_id'] ? "{$event['lastname']} {$event['firstname']}" : '' ?>
                                    <?php if($player_name != ''): ?>
                                        <?= $player_name ?>
                                        <?php if($event['team_id_player_belong'] == $dataMatch['team1_id']): ?>
                                        <img src="<?= IMAGES . '/' . $eventType[$event['event_type']]['icon'] ?>" alt="<?= $eventType[$event['event_type']]['icon_desc'] ?>">
                                        <?php else: ?>
                                        <img src="<?= IMAGES . '/' . $eventType['autogoal']['icon'] ?>" alt="<?= $eventType['autogoal']['icon_desc'] ?>">
                                        <?php endif ?>
                                    <?php endif ?>
                                </td>
                                <td><?= $event['event_time']?>'</td>
                                <td>
                                    <?php $player_name = $event['team_id'] == $dataMatch['team2_id'] ? "{$event['lastname']} {$event['firstname']}" : '' ?>
                                    <?php if($player_name != ''): ?>
                                        
                                        <?php if($event['team_id_player_belong'] == $dataMatch['team2_id']): ?>
                                        <img src="<?= IMAGES . '/' . $eventType[$event['event_type']]['icon'] ?>" alt="<?= $eventType[$event['event_type']]['icon_desc'] ?>">
                                        <?php else: ?>
                                        <img src="<?= IMAGES . '/' . $eventType['autogoal']['icon'] ?>" alt="<?= $eventType['autogoal']['icon_desc'] ?>">
                                        <?php endif ?>
                                        <?= $player_name ?>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                            
                        </tbody>
                    </table>
                    <div class="flex justify-center">
                        <div class="player-card">
                        <?php if(!empty($bestPlayerOfMatch['player_photo'])):?>
                            <div class="player-card__photo-container">
                                    <img class="player-card__photo" src="<?= $player_face_path . $bestPlayerOfMatch['player_photo'] ?>" alt="<?= $bestPlayerOfMatch['lastname'] ?> <?= $bestPlayerOfMatch['firstname'] ?>">                               
                            </div>
                            <div class="player-card__role">Гравець матчу</div>
                            <div class="player-card__club"></div>
                            <div class="player-card__name"><?= $bestPlayerOfMatch['lastname'] ?> <?= $bestPlayerOfMatch['firstname'] ?></div>
                        <?php else: ?>
                            <div class="player-card__photo-container">
                                    <img class="player-card__photo" src="<?= IMAGES . '/no-image.jpeg' ?>" alt="Немає фото">
                            </div>
                            <div class="player-card__role">Гравець матчу</div>
                            <div class="player-card__club"></div>
                            <div class="player-card__name">Даних немає</div>
                        <?php endif ?>
                        </div>
                    </div>

                </div>
                <div class="tabs__block">
                    <div class="tabs_sostav">
                        <?php if(!empty($team1Composition)):?>
                        <table class="table__team table__team1">
                           <?php foreach($team1Composition as $team1): ?>
                            <tr>
                                <td data-player-id="<?=$team1['player_id']?>">
                                    <?php if($team1['player_id'] == $team1['capitan_id']): ?>
                                        <img  class="mr-2" width="15" height="15" src="<?= IMAGES . '/cap-icon.png' ?>" alt="Капітан команди">
                                    <?php endif?>
                                    <?php if($team1['player_id'] == $team1['manager_id']): ?>
                                        <img  class="mr-2" width="15" height="15" src="<?= IMAGES . '/manager-icon.png' ?>" alt="Менеджер команди">
                                    <?php endif?>
                                    <?php if($team1['player_id'] == $team1['trainer_id']): ?>
                                        <img  class="mr-2" width="15" height="15" src="<?= IMAGES . '/coach-icon.png' ?>" alt="Тренер команди">
                                    <?php endif?>

                                    <?= $team1['lastname'] ?> <?= $team1['firstname'] ?>
                                </td>
                                <td><?= $team1['nomer'] ?></td>
                            </tr>
                           <?php endforeach ?>
                           <?php if( !empty($trainerAndManager1) && count($trainerAndManager1) == 1 ): ?>
                            <tr>
                                <td colspan="2">
                                    <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/coach-icon.png' ?>" alt="Тренер">
                                    <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/manager-icon.png' ?>" alt="Менеджер">
                                    <?= $trainerAndManager1[0]['lastname'] ?> <?= $trainerAndManager1[0]['firstname'] ?>
                                </td>
                            </tr>
                            <?php elseif (!empty($trainerAndManager1) && count($trainerAndManager1) == 2): ?>
                                <tr>
                                    <td colspan="2">
                                        <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/coach-icon.png' ?>" alt="Тренер">
                                        <?= $trainerAndManager1['trainer']['lastname'] ?> <?= $trainerAndManager1['trainer']['firstname'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/manager-icon.png' ?>" alt="Менеджер">
                                        <?= $trainerAndManager1['manager']['lastname'] ?> <?= $trainerAndManager1['manager']['firstname'] ?>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>
                        <table class="table__team table__team2">
                            <?php foreach($team2Composition as $team2): ?>
                            <tr>
                                <td><?= $team2['nomer'] ?></td>
                                <td data-player-id="<?=$team2['player_id']?>">                               
                                    <?= $team2['lastname'] ?> <?= $team2['firstname'] ?>
                                    <?php if($team2['player_id'] == $team2['capitan_id']): ?>
                                        <img class="ml-2" width="15" height="15" src="<?= IMAGES . '/cap-icon.png' ?>" alt="Капітан команди">
                                    <?php endif?>
                                    <?php if($team2['player_id'] == $team2['manager_id']): ?>
                                        <img class="ml-2" width="15" height="15" src="<?= IMAGES . '/manager-icon.png' ?>" alt="Менеджер команди">
                                    <?php endif?>
                                    <?php if($team2['player_id'] == $team2['trainer_id']): ?>
                                        <img class="ml-2" width="15" height="15" src="<?= IMAGES . '/coach-icon.png' ?>" alt="Тренер команди">
                                    <?php endif?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                            <?php if( !empty($trainerAndManager2) && count($trainerAndManager2) == 1 ): ?>
                            <tr>
                                <td colspan="2">
                                    <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/coach-icon.png' ?>" alt="Тренер">
                                    <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/manager-icon.png' ?>" alt="Менеджер">
                                    <?= $trainerAndManager2[0]['lastname'] ?> <?= $trainerAndManager2[0]['firstname'] ?>
                                </td>
                            </tr>
                            <?php elseif (!empty($trainerAndManager2) && count($trainerAndManager2) == 2): ?>
                                <tr>
                                    <td colspan="2">
                                        <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/coach-icon.png' ?>" alt="Тренер">
                                        <?= $trainerAndManager2['trainer']['lastname'] ?> <?= $trainerAndManager2['trainer']['firstname'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <img class="mr-2" width="15" height="15" src="<?= IMAGES . '/manager-icon.png' ?>" alt="Менеджер">
                                        <?= $trainerAndManager2['manager']['lastname'] ?> <?= $trainerAndManager2['manager']['firstname'] ?>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>
                        <?php else :?>
                            <div class="danger-info">Дані складу команд ще не внесені адміністратором</div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="tabs__block">
                    <?php if($matchDate <= $currentDate ) :?>
                        <?php foreach ($statsList as $key => $stat) : ?>
                            <div class="tabs__block-stats stats">
                                <div class="stats__title"><?= $stat['title'] ?></div>
                                <div class="stats__view">
                                    <div class="stats__view-blue" data-value="<?= $staticMatch['team1']['data'][$stat['percentage_key']] ?>">
                                        <?= $staticMatch['team1']['data'][$key] ?>
                                    </div>
                                    <div class="stats__view-red" data-value="<?= $staticMatch['team2']['data'][$stat['percentage_key']] ?>">
                                        <?= $staticMatch['team2']['data'][$key] ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else :?>
                        <div class="tabs__block-stats stats ball__procession">
                            <div class="stats__title">Дані статистики ще не готові</div>
                        </div>
                    <?php endif ?>
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
    
    // -- Save image
    $(".save-image").click(function (e) {
        e.preventDefault(); // Отключаем переход по ссылке

        // Получаем ID блока из атрибута data-target
        var targetId = $(this).data("target");
        var content = $("#" + targetId); // Находим блок по ID

        // Сохраняем блок в изображение
        html2canvas(content[0]).then(function (canvas) {
            // Создаем ссылку для скачивания изображения
            var link = document.createElement("a");
            link.download = targetId + ".png"; // Название файла совпадает с ID блока
            link.href = canvas.toDataURL("image/png");
            link.click(); // Автоматически кликаем по ссылке для загрузки
        }).catch(function (error) {
            console.error("Ошибка при сохранении изображения:", error);
        });
    });

});
</script>
