<section class="ratings">
    <h2 class="title">Рейтинги гравців ліги</h2>
    <div class="d-none"><?php var_dump($player['total_key']) ?></div>
    <div class="swiper-ratings ratings__container">

        <div class="swiper-wrapper ratings__wrap containers">
            <?php foreach($topPlayers as $key => $player) : ?>
                <?php if( !empty($player) && intval($player['total_key']) !== 0 ): ?>
            <div class="swiper-slide ratings__player player-card">
                <div data-player="<?= $player['player_id']?>"  data-total="<?= $player['total_key']?>" class="player-card">
                    <div class="player-card__photo-container">
                        <div class="player-card__left-icon">
                            <img src="/css/components/player-card/assets/images/<?= $topPlayersData[$key]['icon'] ?>" alt="">
                            <span><?= $player['total_key']?></span>
                        </div>

                        <img class=" player-card__right-icon"
                            src="<?= $team_logo_path; ?><?= $player['team_photo']; ?>" alt="Логотип команди">

                        <img class="player-card__photo" src="<?= $player_face_path; ?><?= $player['player_photo']; ?>"
                            alt="yarmol">
                    </div>

                    <div class="player-card__role"><?= $topPlayersData[$key]['label'] ?></div>
                    <div class="player-card__club"><?= $player['team_name']?></div>
                    <div class="player-card__name"><?= $player['first_name']?>
                        <?= $player['last_name']?>
                    </div>

                    <a href="<?= $site_url ?>/<?= $tournament ?>/<?= $key ?>" class="player-card__link">
                        <span>Таблиця</span>
                        <img src="/css/components/player-card/assets/images/arrow-icon.svg" alt="arrow">
                    </a>
                </div>
            </div>
            <?php endif ?>
            <?php endforeach ?>

        </div><!-- containers -->

        <div class="swiper-ratings-scrollbar"></div>
    </div><!-- ratings__container -->

</section>