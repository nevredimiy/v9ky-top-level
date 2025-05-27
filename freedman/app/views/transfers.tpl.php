<section class="transfer">
    <div class="container">
        <h2 class="transfer__title title">ТРАНСФЕРИ</h2>

        <div class="transfer__cards">
            <?php foreach($transfers as $transfer) : ?>
            <?php if($transfer['real_action']) : ?>
            <div class="transfer-card transfer-card--arrival">
                <a class="transfer-card__player">
                    <img src="<?= $player_face_path ?><?= $transfer['photo'] ?>"
                        alt="<?= $transfer['firstname'] ?> <?= $transfer['lastname'] ?>">
                    <p class="transfer-card__player-name">
                        <?= $transfer['firstname'] ?> <?= $transfer['lastname'] ?></p>
                </a>

                <div>
                    <div class="transfer-card__arrow"></div>
                    <p class="transfer-card__date"><?= $transfer['date'] ?></p>
                </div>

                <a class="transfer-card__team">
                    <img src="<?=$team_logo_path?><?= $transfer['team_photo'] ?>" alt="">
                    <p class="transfer-card__team-name"><?= $transfer['team_name'] ?></p>
                </a>

            </div>
            <?php else : ?>

            <div class="transfer-card transfer-card--departure">
                <a class="transfer-card__player">
                    <img src="<?= $player_face_path ?><?= $transfer['photo'] ?>"
                        alt="<?= $transfer['firstname'] ?> <?= $transfer['lastname'] ?>">
                    <p class="transfer-card__player-name"><?= $transfer['firstname'] ?> <?= $transfer['lastname'] ?></p>
                </a>

                <div>
                    <div class="transfer-card__arrow"></div>
                    <p class="transfer-card__date"><?= $transfer['date'] ?></p>
                </div>

                <a class="transfer-card__team">
                    <img src="<?=$team_logo_path?><?= $transfer['team_photo'] ?>" alt="">
                    <p class="transfer-card__team-name"><?= $transfer['team_name'] ?></p>
                </a>

            </div>
            <?php endif ?>
            <?php endforeach ?>

        </div>
    </div>
</section>