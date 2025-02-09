<div class="swiper-controls">
            <div class="swiper-wrapper">
                <?php  isset($_GET['tur']) ? $currentTur = $_GET['tur'] : $currentTur = $lastTur ?>
                <button 
                        data-turnir="<?= $turnir ?>" 
                        data-lasttur="<?= $lastTur?>"
                        data-turid="<?= isset($_GET['tur']) ? $_GET['tur'] : $lastTur ?>"
                    class="swiper-slide controls__button"
                >
                    <div class="controls__title">ЗБІРНА ТУРУ</div>
                    <div class="controls__image">
                        <div class="match-review">
                            <img class="match-review__image"
                                src="css/components/match-review/assets/images/calendar-of-matches.png">
                        </div>
                    </div>
                </button>

                <a href="<?= $site_url ?>/<?= $tournament ?>/news" class="swiper-slide controls__button">
                    <div class="controls__title">ЕКСПРЕС-ПІДСУМКИ</div>
                    <div class="controls__image">
                        <div class="match-review">
                            <img class="match-review__image" src="<?= $randomNews['pict1'] ?>" alt="Експрес-Підсумки">
                        </div>
                        <div class="match-review__body">
                            <h3 class="match-review__title"><?= $randomNews['head1'] ?></h3>
                            <div class="match-review__description text-block-three-rows"><?= $randomNews['text1'] ?></div>
                        </div>
                    </div>
                </a>
                
                <?php if(isset($resultTur['after_play'])): ?>
                <a id="after-play" href="<?= $resultOfTur['url1'] ?>" class="swiper-slide controls__button" target="_blank">
                    <div class="controls__title">Після гри</div>
                    <div class="controls__image relative">
                        <div class="match-review">
                            <img class="match-review__image" src="https://img.youtube.com/vi/<?= $resultTur['after_play']?>/mqdefault.jpg" alt="Після гри">
                        </div>
                    </div>
                </a>
                <?php endif ?>

                <?php if(isset($resultTur['top_goals'])): ?>
                <a id="top-goals" href="<?= $resultOfTur['url2'] ?>" class="swiper-slide controls__button" target="_blank">
                    <div class="controls__title">Топ гол</div>
                    <div class="controls__image relative">
                        <div class="match-review">
                            <img class="match-review__image" src="https://img.youtube.com/vi/<?= $resultTur['top_goals']?>/mqdefault.jpg" alt="Топ Гол">
                            <div class="youtube-wrap">
                                <div class="youtube-button"><div class="youtube-triangle"></div></div> 
                            </div>
                        </div>
                    </div>
                </a>
                <?php endif ?>

                <?php if(isset($resultTur['top_save'])): ?>
                <a id="top-save " href="<?= $resultOfTur['url3'] ?>" class="swiper-slide controls__button" target="_blank">
                    <div class="controls__title">Топ сейв</div>
                    <div class="controls__image relative">
                        <div class="match-review">
                            <img class="match-review__image" src="https://img.youtube.com/vi/<?= $resultTur['top_save']?>/mqdefault.jpg" alt="Топ Сейв">
                        </div>
                        <div class="youtube-wrap">
                            <div class="youtube-button"><div class="youtube-triangle"></div></div> 
                        </div>
                    </div>
                </a>
                <?php endif ?>
            </div>
            <div class="swiper-scrollbar-controls"></div>
        </div>