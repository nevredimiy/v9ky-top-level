<div class="calendar-of-matches__head">
            <h2 class="calendar-of-matches__title title">Календар матчів</h2>
</div><!-- calendar-of-matches__head -->

<div class="calendar-of-matches__head-nav">

    <div class="swiper swiper-month-controls">
        <div class="swiper-wrapper">
            <?php foreach($dateMatches as $key => $dateMatch): ?>
            <div class="swiper-slide swiper-slide-month-controls swiper-slide-active" role="group"
                aria-label="1 / 15" style="margin-right: 5px;">
                <a 
                    data-turnir="<?= $turnir ?>" 
                    data-lasttur="<?= $lastTur?>"
                    data-selecteddate = "<?= $selectedDate ?>"
                    data-first-day = "<?= $dateMatch['first_day'] ?>"
                    data-last-day = "<?= $dateMatch['last_day'] ?>"                    

                    class="month-controls__button 
                    <?= strtotime($dateMatch['first_day']) < strtotime(date("Y-m-d")) ? 'month-controls__button--past' : '' ?> 
                    <?= ($dateMatch['first_day'] == $selectedDate || $dateMatch['last_day'] == $selectedDate) ? 'month-controls__button--current' : '' ?>"

                    <?= ($dateMatch['first_day'] == $selectedDate || $dateMatch['last_day'] == $selectedDate ) ? '' : "href='{$site_url}/{$tournament}?first_day={$dateMatch['first_day']}&last_day={$dateMatch['last_day']}'"?>
                >
                    <p><?= $dateMatch['text_month'] ?></p>
                    <p><?= $dateMatch['text_day'] ?></p>
                    
                </a>
            </div>
            <?php endforeach ?>
        </div>

        <div class="swiper-scrollbar-month-controls"></div>
    </div>
</div> <!-- swiper  -->

<div class="calendar-of-matches__aside">
    <div class="swiper-matches swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
        <div class="swiper-wrapper" id="swiper-wrapper-1b58efbe70e5fba9" aria-live="polite"
            style="transform: translate3d(0px, 0px, 0px);">
            <?php if($dataMatchesOfDate) :?>
                <?php $i =1 ?>
                <?php foreach($dataMatchesOfDate as $match): ?>

                    <div 
                        data-match-id="<?= $match['id'] ?>"
                        class="swiper-slide swiper-slide-active" 
                        role="group" 
                        aria-label="1 / 5"
                        style="margin-right: 5px;"
                    >
                        <div class="card-of-matches">
                            <div class="card-of-matches__title-match">
                                <img class="card-of-matches__shirt card-of-matches__shirt--left"
                                    src="/css/components/card-of-matches/assets/images/yellow-shirt.svg" alt="yellow">

                                <img class="card-of-matches__team-logo card-of-matches__team-logo--left"
                                    src="<?= $team_logo_path ?>/<?= $match['team1_photo'] ?>" alt="Логотип команди">

                                    <div class="card-of-matches__score">                                
                                        <?php if( $match['goals1'] != null ): ?>        
                                            <span><?= $match['goals1'] ?></span>
                                            :
                                            <span><?= $match['goals2'] ?></span>
                                        <?php else:?>
                                            <span>VS</span>
                                        <?php endif?>
                                    </div>

                                <img class="card-of-matches__team-logo card-of-matches__team-logo--right"
                                    src="<?= $team_logo_path ?>/<?= $match['team2_photo'] ?>" alt="leicester">

                                <p class="card-of-matches__team card-of-matches__team--left"><?= $match['team1_name'] ?>
                                </p>

                                <div class="card-of-matches__date-and-time">
                                    <div class="card-of-matches__date"><?= $match['match_day'] ?></div>
                                    <div class="card-of-matches__time"><?= $match['match_time'] ?></div>
                                </div>

                                <p class="card-of-matches__team card-of-matches__team--right">
                                    <?= $match['team2_name'] ?></p>

                                <img class="card-of-matches__shirt card-of-matches__shirt--right"
                                    src="/css/components/card-of-matches/assets/images/blue-shirt.svg" alt="blue">

                                <div class="card-of-matches__marks">
                                    <a class="card-of-matches__mark"><span><?= $match['turnir_name'] ?></span></a>
                                    <a class="card-of-matches__mark"><span><?= $match['tur'] ?> тур</span></a>
                                    <a class="card-of-matches__mark"><span><?= $match['field_name'] ?></span></a>
                                </div>
                            </div>

                            <div class="card-of-matches__controls">
                                <?php $hrefAnons = (isset($match['anons']) && !empty(trim($match['anons']))) ? 'href="#"' : ''; ?>  
                                <a 
                                    data-anons="<?= $match['id'] ?>" 
                                    data-tur="<?= $match['tur'] ?>" 
                                    data-turnir="<?= $turnir ?>" 
                                    class="card-of-matches__controls-link" 
                                    <?= $hrefAnons ?> 
                                    title="Анонс матчу"
                                    id="anons-<?= $i ?>"
                                    <?= ($dateLastTur <= $dateNow && $selectedDate >= $dateNow && $i == 1) ? 'style="background:red"': '' ?>
                                >  
                                    <img src="/css/components/card-of-matches/assets/images/anons-icon.png" alt="Анонс матчу" title="Анонс матчу">
                                </a>
                                
                                <?php $href = $match['goals1'] == NULL ? '' : "href='{$site_url}/{$tournament}/?first_day={$dateMatch['first_day']}&last_day={$dateMatch['last_day']}'" ?>
                                <a 
                                    data-match-stats="<?= $match['id'] ?>" 
                                    data-tur="<?= $match['tur'] ?>" 
                                    data-turnir="<?= $turnir ?>" 
                                    data-team1-id="<?= $match['team1_id'] ?>"
                                    data-team2-id="<?= $match['team2_id'] ?>"
                                    <?= $href ?>
                                    class="card-of-matches__controls-link" title="Статистика матчу"
                                >
                                        <img src="/css/components/card-of-matches/assets/images/stat-match-icon.png" alt="Статистика матчу" title="Статистика матчу">
                                </a>
                                <?php $hrefVideo = $match['video'] != '' ? 'href="#"' : '' ?>
                                <a 
                                    data-preview="<?= $match['id'] ?>" 
                                    data-tur="<?= $match['tur'] ?>" 
                                    data-turnir="<?= $turnir ?>" 
                                    class="card-of-matches__controls-link" 
                                    <?= $hrefVideo ?>
                                    title="Прев'ю матчу"
                                >
                                    <img src="/css/components/card-of-matches/assets/images/live-video-icon.png"
                                        alt="Прев'ю матчу" title="Прев'ю матчу">
                                </a>
                                <?php $hrefHD = $match['video_hd'] != '' ? 'href="#"' : '' ?>
                                <a 
                                    data-video="<?= $match['id'] ?>" 
                                    data-tur="<?= $match['tur'] ?>" 
                                    data-turnir="<?= $turnir ?>" 
                                    class="card-of-matches__controls-link" 
                                    <?= $hrefHD ?>
                                    title="Відео HD якості"
                                >
                                        <img src="/css/components/card-of-matches/assets/images/hd-icon.png"
                                        alt="Відео HD якості" title="Відео HD якості">
                                </a>
                                <a 
                                    data-kkd="<?= $match['id'] ?>" 
                                    data-tur="<?= $match['tur'] ?>" 
                                    data-turnir="<?= $turnir ?>" 
                                    class="card-of-matches__controls-link" 
                                    <?= $href ?> 
                                    title="ККД Гравців"
                                >
                                    <img src="/css/components/card-of-matches/assets/images/ind-stat-icon.png"
                                        alt="ККД Гравців" title="ККД Гравців">
                                </a>

                                <?php $hrefPhoto = is_dir(PHOTO . '/' . $match['id']) ? ' href="#" ' : '' ?>
                                <a 
                                    data-photo="<?= $match['id'] ?>" 
                                    data-tur="<?= $match['tur'] ?>" 
                                    data-turnir="<?= $turnir ?>" 
                                    class="card-of-matches__controls-link" 
                                    <?= $hrefPhoto ?>
                                    title="Фото матчу"
                                >
                                        <img src="/css/components/card-of-matches/assets/images/photo-match-icon.png" alt="Фото матчу" title="Фото матчу">
                                </a>
                            </div>

                            <div data-match-canseled="<?=$match['canseled']  ?>" class="card-of-matches__status">
                                <?php if($match['goals1'] == NULL || $match['goals2'] == NULL ) : ?>
                                    МАТЧ ОЧІКУЄТЬСЯ
                                <?php else:?>
                                    <?php 
                                        switch($match['canseled']) {
                                            case 0:
                                                echo 'Матч очікується';
                                                break;
                                            case 1:
                                                echo ' Матч завершено';
                                                break;
                                            case 2:
                                                echo 'Матч у прямому ефірі';
                                                break;
                                            default:
                                                echo 'Матч очікується';
                                        }
                                    ?>
                                <?php endif?>
                            </div>

                            <a class="card-of-matches__share-button" href="#">
                                <img src="/css/components/card-of-matches/assets/images/share-icon.svg" alt="share">
                            </a>
                        </div>
                    </div>

                <?php $i++ ?>
                <?php endforeach ?>
            <?php else : ?>
                <p>У вибрану дату ніяких матчів не передбачено</p>
            <?php endif ?>

        </div>
        <div class="swiper-scrollbar-matches"></div>
    </div><!-- calendar-of-matches__aside -->
</div><!-- calendar-of-matches__head-nav -->

<div class="calendar-of-matches__dynamic-content">
        <section class="green-zone">
            <?php if($dateLastTur <= $dateNow) : ?>
                <button id="captureAndShare" class="anons__share-btn">
                    <img src="<?= IMAGES . '/button-share-icon.svg' ?>" alt="Зберегти зображення">
                </button>
            <div id="capture" class="green-zone__current content-to-capture">
                <h2 class="green-zone__title title">ЗБІРНА ТУРУ</h2>
                
                
                <div class=" <?= $currentTur <= $lastTur ? 'green-zone__players' : '' ?>">

                        <?php if(!empty($bestPlayersForTable)):?>
                            <?php foreach($bestPlayersForTable as $player) : ?>

                                <div data-player="<?= $player['player'] ?>" class="player-card">
                                    <div class="player-card__photo-container">
                                        <div class="player-card__left-icon">
                                            <img src="/css/components/player-card/assets/images/<?= $labels[$player['best_player']]['icon'] ?>"
                                                alt="star">
                                            <span><?= $player['count_points'] ?></span>
                                        </div>

                                        <img class="player-card__right-icon" src="<?= $team_logo_path ?>/<?= $player['team_photo'] ?>"
                                            alt="Логотип команды">

                                        <img class="player-card__photo" src="<?= $player_face_path ?>/<?= $player['player_photo'] ?>" alt="yarmol">
                                    </div>

                                    <div class="player-card__role"><?= $labels[$player['best_player']]['role'] ?></div>
                                    <div class="player-card__club"><?= $player['team_name'] ?></div>
                                    <div class="player-card__name"><?= $player['first_name'] ?> <?= $player['last_name'] ?>
                                    </div>

                                    <a href="#" class="player-card__link">
                                        <span>Таблиця</span>
                                        <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                                    </a>
                                </div>

                            <?php endforeach ?>
                        <?php else : ?>
                            <div class="green-zone__footer-title">
                                <h2 class="green-zone__title text-center">Дані туру ще не внесені адміністратором. Зайдіть пізніше</h2>
                            </div>
                        <?php endif ?>

                    <div class="green-zone__footer-title">
                        <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png"
                            alt="v9ku-logo">

                        <h3><?= $dataMatchesOfDate[0]['season'] ?>&nbsp;&nbsp;ӏ&nbsp;&nbsp;<?= $dataMatchesOfDate[0]['turnir_name'] ?>&nbsp;&nbsp;ӏ&nbsp;&nbsp;5Х5&nbsp;&nbsp;ӏ&nbsp;&nbsp;<?= $dataMatchesOfDate[0]['tur'] ?>
                        </h3>

                        <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png"
                            alt="v9ku-logo">
                            <div class="share-btn-item d-none">
                                <button class="share-telegram" id="share-telegram">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="40" width="60" viewBox="-36 -60 312 360"><defs><linearGradient gradientUnits="userSpaceOnUse" y2="51.9" y1="11.536" x2="28.836" x1="46.136" id="a"><stop offset="0" stop-color="#37aee2"/><stop offset="1" stop-color="#1e96c8"/></linearGradient></defs><g transform="scale(3.4682)"><circle fill="url(#a)" r="34.6" cx="34.6" cy="34.6"/><path fill="#fff" d="M14.4 34.3l23.3-9.6c2.3-1 10.1-4.2 10.1-4.2s3.6-1.4 3.3 2c-.1 1.4-.9 6.3-1.7 11.6l-2.5 15.7s-.2 2.3-1.9 2.7c-1.7.4-4.5-1.4-5-1.8-.4-.3-7.5-4.8-10.1-7-.7-.6-1.5-1.8.1-3.2 3.6-3.3 7.9-7.4 10.5-10 1.2-1.2 2.4-4-2.6-.6l-14.1 9.5s-1.6 1-4.6.1c-3-.9-6.5-2.1-6.5-2.1s-2.4-1.5 1.7-3.1z"/></g></svg>
                                </button>  
                                <div class="text-green-on-green loading-message"></div>
                            </div>
                    </div>

                </div>

            </div>
            
            <?php else: ?>
                        
                <?php require_once VIEWS . '/anons.tpl.php' ?>
            <?php endif ?>
        </section>
    
    </div> <!-- calendar-of-matches__dynamic-content -->

    <!-- Модальное окно с ссылками -->
    <div id="shareModal" class="modal">
        <div class="modal-content">
            <p>Виберіть месенджер для надсилання скріншоту:</p>
            <a id="shareViber" href="#" class="share-btn viber"><img src="<?= IMAGES . '/viber-logo-icon.svg' ?>" alt="Відправити у Viber"> Відправити у Viber</a>
            <a id="shareTelegram" href="#" class="share-btn telegram"><img src="<?= IMAGES . '/telegram-logo-icon.svg' ?>" alt="Відправити у Telegram"> Відправити у Telegram</a>
            <button id="closeModal" class="close-btn">Закрити</button>
        </div>
    </div>

</div>
  
<script>
    // делегирует событие клик с одного элемента на другой в блоке .card-of-matches
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".card-of-matches").forEach(function (card) {
            card.addEventListener("click", function (event) {
                // Проверяем, кликнули ли по .card-of-matches__score
                if (event.target.closest(".card-of-matches__score")) {
                    // Ищем внутри текущего блока элемент с data-match-stats
                    let statsLink = card.querySelector("[data-match-stats]");
                    if (statsLink) {
                        statsLink.click(); // Имитируем клик по ссылке
                    }
                }
            });
        });
    });

</script>