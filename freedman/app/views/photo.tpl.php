<section class="controls">
    <div class="controls__container">
        <div class="controls__share">
            <button class="controls__share-btn"><img src="css/components/match-stats/assets/images/button-share-icon.svg" alt="Зберегти зображення"></button>
        </div>
        <div class="controls__head">
            <div class="controls__head-title">Фото матчу</div>
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
            <?php if(!empty($photo)): ?>
            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper swiperMatchPhoto">
                <div class="swiper-wrapper">
                    <?php foreach($photo as $picture): ?>
                        <?php if(($picture != ".") && ($picture != "..")&&($picture !="tmp")&&($picture !="photos.zip")): ?>
                            <div class="swiper-slide">
                                <!-- <div class="placeholder222" id="placeholder333">Loading...</div> -->
                                <!-- <img 
                                    style="width:100%" 
                                    src="<?= PHOTO_URL .'/'. $data['match_id'] .'/'. $picture ?>" 
                                    alt="" loading="lazy" 
                                    class="swiper-lazy" 
                                    onload="document.getElementById('placeholder333').style.display = 'none';"
                                /> -->
                                <img 
                                    style="width:100%" 
                                    src="<?= PHOTO_URL .'/'. $data['match_id'] .'/'. $picture ?>" 
                                    alt="" loading="lazy" 
                                />
                                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-photo-pagination"></div>
            </div>
            <?php else: ?>
                <div class="danger-info">У цьому матчі фото немає</div>
            <?php endif ?>
        </div>
    </div>
</section>

<script>
    var swiper = new Swiper(".swiperMatchPhoto", {
        // lazy: {
        //     loadPrevNext: true,
        //     loadPrevNextAmount: 2,
        // },
        slidesPerView: 'auto',        
        lazy: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-photo-pagination",
            clickable: true
        },
        // on: {
        //     lazyImageReady: function (swiper, slideEl, imageEl) {
        //         // Удаляем кастомный плейсхолдер, когда изображение загружено
        //         const placeholder = slideEl.querySelector('.placeholder222');
        //         if (placeholder) {
        //             placeholder.style.display = 'none';
        //         }
        //     },
        // },
    });
</script>