<section class="stadiums">
    <div class="container">
    <h3 class="stadiums__title title">СТАДІОНИ</h3>

    <div class="stadiums__list">
        <?php foreach($fields as $field): ?>
            <div class="stadium-card">
                <div class="stadium-card__title"><?= $field['name'] ?></div>

                <div class="stadium-card__photo"></div>

                <div class="stadium-card__description">
                    <div class="stadium-card__location">
                    <img src="<?= $site_url ?>/css/components/stadiums/assets/images/location-icon.svg" alt="location">
                    <p><?= $field['address'] ?></p>
                    </div>

                    <ul class="stadium-card__facilities">
                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/stadium-40x20-icon.svg" alt="stadium">
                        <p>4</p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/stadium-60x40-icon.svg" alt="stadium">
                        <p>1</p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/parking-icon.svg" alt="parking">
                        <p>50</p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/wash-icon.svg" alt="wash">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/true-icon.svg" alt="true">
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/sound-icon.svg" alt="sound">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/true-icon.svg" alt="true">
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/dressing-icon.svg" alt="dressing">
                        <p>3</p>
                    </li>

                    <li>
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/wc-icon.svg" alt="wc">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/false-icon.svg" alt="false">
                    </li>
                    </ul>

                    <div class="stadium-card__contact">
                    <a class="stadium-card__phone-button">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/phone-icon.svg" alt="phone">
                        <p>(093) 275 54 13  (АДМІНІСТРАЦІЯ)</p>
                    </a>

                    <a class="stadium-card__map-button">
                        <img src="<?= $site_url ?>/css/components/stadiums/assets/images/map-icon.svg" alt="map">
                    </a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>