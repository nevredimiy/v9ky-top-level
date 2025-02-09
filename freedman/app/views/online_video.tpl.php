<div class="container">
    <div class="online__videos">
        <?php foreach($onlineVideos as $video):?>
        <div class="video">
            <div class="video__wrap">
                <a href="<?=$video['url']?>"><img src="http://v9ky.in.ua/video_online_pics/<?=$video['id']?>.png"  width=100%></a>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>