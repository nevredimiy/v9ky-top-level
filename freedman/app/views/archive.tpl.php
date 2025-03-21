<?php if(isset($listTurnirs)):?>
    <div class="container">
        <?php foreach($listTurnirs as $key => $data) : ?>
            <?php foreach($data as $item): ?>
                <?php foreach($item as $it) : ?>
                    <div class="flex justify-center fz-16">
                        <p> 
                            <a href="<?=$site_url?>/<?= $it['name'] ?>/city_news">
                                    <?= $key ?> / <?= $it['season'] ?> / <?= $it['ru'] ?>
                            </a>
                        </p>

                    </div>

                    <br>

                <?php endforeach ?>
                <br>
                
            <?php endforeach ?>
        <?php endforeach ?>
    </div>
<?php else :?>
    <h1>Сторінка у розробці</h1>
<?php endif?>