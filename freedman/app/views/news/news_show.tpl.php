<section class="news-show">
    <div class="container">
        <div class="mb-2">
            <h1 class="news-show__title"><?= $news_show['head1'] ?></h1>
            <img class="news-show__img" src="<?= $news_show['pict1'] ?>" alt="<?= $news_show['head1'] ?>">
            <div class="news-show__body" class="n"><pre class="news-content"><?= $news_show['text1'] ?></pre></div>
        </div>
        <div class="">
            <a href="<?= $site_url?>/<?= $tournament?>/news" class="btn">Повернутися до списку статей</a>
        </div>
    </div>
</section>