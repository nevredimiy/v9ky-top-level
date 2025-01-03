<style>
    /* Контейнер видео */
    .video-container {
        position: relative;
        width: 100%;
        max-width: 600px; /* Ограничение по ширине */
        aspect-ratio: 16 / 9; /* Соотношение сторон */
        background: #ccc; /* Цвет фона для плейсхолдера */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* Плейсхолдер */
    .placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        /* Картинка для плейсхолдера */
        /*background: url('placeholder.jpg') no-repeat center center; */
        background-size: cover;
    }

    /* Видео */
    iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0; /* Скрыть до загрузки */
        transition: opacity 0.5s ease;
    }

    iframe.loaded {
        opacity: 1; /* Показать после загрузки */
    }
</style>

<section class="controls">
    <div class="controls__container">
        <div class="controls__share">
            <button class="controls__share-btn"><img src="css/components/match-stats/assets/images/button-share-icon.svg" alt="Зберегти зображення"></button>
        </div>
        <div class="controls__head">
            <div class="controls__head-title">Відео в HD якості</div>
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
        <div class="preview">
            <?php if(!empty($video[0]['videohiden'])): ?>
            <div class="preview__item">
                <div class="video-container">
                    <div class="placeholder videoPlaceholder">
                        Загрузка відео...
                    </div>
                    <iframe 
                        class="videoFrame"
                        src="<?= $video[0]['videohiden'] ?>" 
                        title="YouTube video"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            <?php else: ?>
                <div class="danger-info">
                    Відео в HD якості відсутнє
                </div>
            <?php endif ?>
        </div>
    </div>
</section>


<script>
    $(document).ready(function() {
        // Получаем iframe и плейсхолдер
        const $videoFrame = $('.videoFrame');
        const $placeholder = $('.videoPlaceholder');

        // Слушаем событие загрузки видео
        $videoFrame.on('load', function() {
            // Убираем плейсхолдер
            $placeholder.hide();

            // Показываем видео
            $videoFrame.addClass('loaded');
        });
    });
</script>