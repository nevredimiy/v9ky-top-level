<?php

$banners = $dbF->query("SELECT * FROM v9ky_baners WHERE page1=1 AND nizverh=0 AND active=1")->findAll();

?>

<?php if(isset($banners) && count($banners) > 0) :?>
<div class="container">
    <?php foreach ($banners as $banner) :?>
    <div class="flex justify-center mt-2 mb-2 pl-2 pr-2 ">
        <a href="<?=$banner['site']?>" target="_blank" rel="nofollow">
            <img width=100% src="<?= $site_url ?>/baner/<?=$banner['pict']?>">
        </a>
    </div>
    <?php endforeach ?>
</div>
<?php endif ?>



