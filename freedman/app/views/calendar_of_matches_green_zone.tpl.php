<div class="green-zone__current">
    <h2 class="green-zone__title title">ЗБІРНА ТУРУ</h2>
    <div class=" <?= $currentTur <= $lastTur ? 'green-zone__players' : '' ?>">
    

        <?php if($currentTur <= $lastTur && $dateLastTur <= $currentDate) : ?>

            <?php foreach($bestPlayersForTable as $player) : ?>

            <div class="player-card">
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
            
        <?php else: ?>
            <div class="green-zone__footer-title">
                <h2 class="green-zone__title text-center">Цей турнір ще не відбувся, або дані турніру не внесені
                    адміністратором </h2>
            </div>
        <?php endif ?>

        <div class="green-zone__footer-title">
            <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png"
                alt="v9ku-logo">

            <h3><?= $dataCurrentTur[0]['season'] ?>&nbsp;&nbsp;ӏ&nbsp;&nbsp;<?= $dataCurrentTur[0]['turnir_name'] ?>&nbsp;&nbsp;ӏ&nbsp;&nbsp;5Х5&nbsp;&nbsp;ӏ&nbsp;&nbsp;<?= $dataCurrentTur[0]['tur'] ?>
            </h3>

            <img src="/css/components/green-zone/assets/images/v9ku-logo-on-white-back.png"
                alt="v9ku-logo">
        </div>

    </div>

</div>