<section class="stadiums">
    <div class="container">
    <h3 class="stadiums__title title">СТАДІОНИ</h3>

    <div class="stadiums__list">
        <?php foreach($fields as $field): ?>
            <div class="stadium-card">
                <div class="stadium-card__title"><?= $field['name'] ?></div>

                <div class="stadium-card__photo">
                    <img src="<?=$site_url .'/'. $field['photo']?>" alt="<?= $field['name'] ?>">
                </div>

                <div class="stadium-card__description">
                    <div class="stadium-card__location">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/location-icon.svg" alt="location">
                        <p><?= $field['address'] ?></p>
                    </div>

                    <ul class="stadium-card__facilities">
                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/stadium-40x20-icon.svg" alt="stadium">
                        <p><?= $field['fields_40x20'] ?></p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/stadium-60x40-icon.svg" alt="stadium">
                        <p><?= $field['fields_60x40'] ?></p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/parking-icon.svg" alt="parking">
                        <p><?= $field['parking'] ?></p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/wash-icon.svg" alt="wash">
                        <?php if($field['shower'] > 0):?>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/true-icon.svg" alt="true">
                        <?php else : ?>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/false-icon.svg" alt="false">
                        <?php endif ?>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/sound-icon.svg" alt="sound">
                        <?php if($field['loudspeaker'] > 0):?>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/true-icon.svg" alt="true">
                        <?php else : ?>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/false-icon.svg" alt="false">
                        <?php endif ?>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/dressing-icon.svg" alt="dressing">
                        <p><?= $field['cloakroom'] ?></p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/wc-icon.svg" alt="wc">
                        <?php if($field['toilet'] > 0):?>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/true-icon.svg" alt="true">
                        <?php else : ?>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/false-icon.svg" alt="false">
                        <?php endif ?>
                    </li>
                    </ul>

                    <div class="stadium-card__contact">
                    <a href="tel:+380932755413" class="stadium-card__phone-button">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/phone-icon.svg" alt="phone">
                        <p>(093) 275 54 13  (АДМІНІСТРАЦІЯ)</p>
                    </a>

                    <?php if($field['latitude'] != NULL || $field['longitude'] != NULL) : ?>
                    <a href="https://www.google.com/maps?q=<?= $field['latitude'] ?>,<?= $field['longitude'] ?>&z=15" class="stadium-card__map-button" target="_blank">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/map-icon.svg" alt="map">
                    </a>
                    <?php endif ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>