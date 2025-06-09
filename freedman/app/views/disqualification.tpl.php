<section class="disqualification">
    <h2 class="disqualification__title title">Дискваліфікація</h2>

    <div class="swiper swiper-disqualification swiper-initialized swiper-horizontal swiper-backface-hidden"
        style="display: flex; justify-content: center; align-items: center; gap: 10px;">
        <div class="swiper-wrapper swiper-wrapper-disqualification" id="swiper-wrapper-7ee335d5bfdfaaed"
            aria-live="polite" style="max-width: 1440px; width: fit-content;">
            <?php if(!empty($disPlayers)) : ?>
                <?php foreach ($disPlayers as $player) : ?>
                    <div class="swiper-slide swiper-slide-disqualification swiper-slide-active" role="group" aria-label="1 / 4"
                        style="margin-right: 10px;">
                        <div class="player-card player-card--disqualification">
                            <div class="player-card__photo-container">

                                <!-- <div class="player-card__left-icon">
                                    <img src="css/components/disqualification/assets/images/yellow-red-card-icon.svg"
                                        alt="star">
                                    <span>1 </span>
                                </div> -->

                                <img class="player-card__right-icon" src="<?= $team_logo_path ?><?= $player['team_logo'] ?>"
                                    alt="Логотип команди">

                                <img class="player-card__photo" src="<?= $player_face_path ?><?= $player['player_photo'] ?>"
                                    alt="Фото гравця">
                            </div>

                            <div class="player-card__role">Дискваліфікація</div>
                            <div class="player-card__club"><?= $player['team_name'] ?></div>
                            <div class="player-card__name"><?= $player['firstname'] ?> <?= $player['lastname'] ?></div>

                            <a href="<?= $site_url ?>/<?= $tournament ?>/disqualification_table " class="player-card__link">
                                <span>Таблиця</span>
                                <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                            </a>
                        </div>

                    </div>
                <?php endforeach ?>
            <?php else :?>
            <h2>На даний момент в лізі немає дискваліфікованних гравців</h2>
            <a href="<?= $site_url ?>/<?= $tournament ?>/disqualification_table " class="player-card__link">
                        <span>Перейти до Таблиці</span>
                        <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
            <?php endif ?>
        </div>

        <div class="swiper-scrollbar swiper-scrollbar-horizontal swiper-scrollbar-lock" style="display: none;">
            <div class="swiper-scrollbar-drag" style="transform: translate3d(0px, 0px, 0px); width: 70px;">
            </div>
        </div>
        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
    </div>

    <div class="disqualification__list">
    </div>
</section>