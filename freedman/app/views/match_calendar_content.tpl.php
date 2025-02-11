<div class="calendar-of-matches__head-nav">

    <div class="swiper swiper-month-controls swiper-initialized swiper-horizontal">
        <div class="swiper-wrapper swiper-wrapper-month-controls" id="swiper-wrapper-2894150f39673565"
            aria-live="polite" style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px);">
            
            <?php foreach($dateTurs as $dateTur): ?>
            <div class="swiper-slide swiper-slide-month-controls swiper-slide-active" role="group" aria-label="1 / 15"
                style="margin-right: 5px;">
                <a data-switch-tur="<?=$dateTur['tur'] ?>" data-turnir="<?= $turnir ?>" data-lasttur="<?= $lastTur?>"
                    class="month-controls__button
                    <?= $dateTur['tur'] <= $lastTur ? 'month-controls__button--past ' : '' ?>
                    <?= $currentTur ==  $dateTur['tur'] ? 'month-controls__button--current' : '' ?>"
                    <?= $currentTur != $dateTur['tur'] ? "href={$site_url}{$dateTur['link']}" : '' ?>>
                    <p><?= date_translate($dateTur['month_min_name']) ?></p>
                    <p><?= $dateTur['day_min']?></p>
                </a>
            </div>
            <?php endforeach ?>
        </div>

        <div class="swiper-scrollbar"></div>
    </div> <!-- swiper  -->

</div>
<div class="match-zone__current-date">
    <?php $i = 1 ?>
    <?php foreach($groupedData as $key => $value) : ?>
    <h2 class="match-zone__title title"><?= getFormateDate($key) ?></h2>
    <?php $j = 1 ?>
    <?php foreach($value as $k => $v) : ?>
    <div class="match-zone__matches">
        <div id="matchCalendar<?= $i.$j ?>" class="card-of-matches card-of-matches--match-zone">
            <a data-target="matchCalendar<?= $i.$j ?>" title="Зберегти матч" class="card-of-matches__share-button save-image" href="#">
                <img src="<?= $site_url?>/freedman/assets/images/button-share-icon.svg" alt="share">
            </a>
            <div class="card-of-matches__title-match">
                <img data-shirt="<?= $v['color_tshirt1'] ?>" class=" card-of-matches__shirt
                        card-of-matches__shirt--left" src="<?= $tshirtImages[$v['color_tshirt1']] ?>" alt="yellow">

                <img class="card-of-matches__team-logo card-of-matches__team-logo--left"
                    src="<?= $team_logo_path?><?= $v['team1_photo']?>" alt="Logo Team <?= $v['team1_name'] ?>">

                <?php if($v['goals1'] != NULL):?>
                <div class="card-of-matches__score">
                    <span><?= $v['goals1'] ?></span>
                    :
                    <span><?= $v['goals2'] ?></span>
                </div>
                <?php else :?>
                <div class="card-of-matches__score"><span>VS</span></div>
                <?php endif  ?>

                <img class="card-of-matches__team-logo card-of-matches__team-logo--right"
                    src="<?= $team_logo_path?><?= $v['team2_photo']?>" alt="Logo Team <?= $v['team2_name'] ?>">

                <p class="card-of-matches__team card-of-matches__team--left"><?= $v['team1_name'] ?>
                </p>

                <div class=" card-of-matches__date-and-time">
                    <div class="card-of-matches__date">
                        <?= getFormateDate($v['date'], true) ?>
                    </div>
                    <div class="card-of-matches__time"><?= getTime($v['date']);?></div>
                </div>

                <p class="card-of-matches__team card-of-matches__team--right"><?= $v['team2_name'] ?></p>

                <img data-shirt="<?= $v['color_tshirt2'] ?>"
                    class="card-of-matches__shirt card-of-matches__shirt--right"
                    src="<?= $tshirtImages[$v['color_tshirt2']] ?>" alt="blue">

                <div class="card-of-matches__marks">
                    <a class="card-of-matches__mark"><span><?= $v['season'] ?></span></a>
                    <a class="card-of-matches__mark"><span><?= $v['tur'] ?> тур</span></a>
                    <a class="card-of-matches__mark"><span><?= $v['field_name'] ?></span></a>
                </div>
            </div>

            <div class="card-of-matches__controls">
                <?php if($v['canseled'] == 1):?>
                <div class="card-of-matches__status card-of-matches--completed">МАТЧ ЗАВЕРШЕНО</div>     
                <?php elseif($v['canseled'] == 0) :?>          
                <div class="card-of-matches__status card-of-matches--expected">МАТЧ ОЧІКУЄТЬСЯ</div>     
                <?php elseif($v['canseled'] == 2) :?>          
                <div class="card-of-matches__status card-of-matches--in-progress">МАТЧ ТРИВАЄ</div>     
                <?php endif?>          
            </div>

        </div>
    </div>
    <?php $j++ ?>
    <?php endforeach ?>
    <?php $i++ ?>
    <?php endforeach ?>
</div>