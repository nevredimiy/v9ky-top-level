<?php if(!empty($video)) :?>
    <div class="green-zone__wrap">
        <iframe 
            width="<?= $video['width'] ?>" 
            height="<?= $video['height'] ?>" 
            src="<?= $video['link'] ?>" 
            title="<?= $video['title'] ?>" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            referrerpolicy="strict-origin-when-cross-origin" 
            allowfullscreen>
        </iframe>
    </div>
<?php endif ?>