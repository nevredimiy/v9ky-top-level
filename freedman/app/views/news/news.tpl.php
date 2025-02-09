<section class="news">
    <div class="container">
        <h3 class="stadiums__title title">Новини</h3>

        <div class="stadiums__list">
            <?php foreach($news as $item): ?>
            <div class="stadium-card">
                <div class="stadium-card__photo">
                    <img src="<?= $item['pict1'] ?>" alt="<?= $item['head1'] ?>">
                </div>  
                <div class="stadium-card__title"><?= $item['head1'] ?></div>
                <div class="match-review__description text-block-three-rows"><?= $item['text1'] ?></div>    
                <div class="flex justify-center">
                    <a class="btn mt-2 mb-2" href="<?= $site_url ?>/<?= $tournament ?>/news_show?id=<?= $item['id'] ?>">Читати далі</a>
                </div>
            </div>
            <?php endforeach ?>
        </div>   
        <?= $pagination ?>                    
    </div>
</section>