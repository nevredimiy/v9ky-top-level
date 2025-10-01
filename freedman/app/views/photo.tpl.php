<section class="controls">
    <button id="captureAndShare" class="anons__share-btn">
        <img src="<?= IMAGES . '/button-share-icon.svg' ?>" alt="Зберегти зображення">
    </button>
    <div id="players-photo" class="controls__container content-to-capture">        
        <div class="controls__head">
            <div class="controls__head-title">Фото матчу</div>
            <div class="controls__head-info">
                <div class="info"><?= $dataMatch['season'] ?></div>
                <div class="info"><?= $dataMatch['tur'] ?> тур</div>
                <div class="info"><?= $dataMatch['field_name'] ?></div>
                <div class="info"><?= $dataMatch['match_day'] ?></div>
                <div class="info"><?= $dataMatch['match_time'] ?></div>
            </div>
            <div class="controls__teams">
                <div class="controls__teams-content">
                    <div data-team1-id="<?= $dataMatch['team1_id'] ?>" class="controls__logo logo-team1">
                        <img src="<?= $team_logo_path ?><?= $dataMatch['team1_photo'] ?>">
                    </div>
                    <div class="match-state state">
                     
                        <?php if($dataMatch['goals1'] != null) :?>
                        <div class="state__score"><?= $dataMatch['goals1'] ?></div>
                        <div class="state__score-middle">:</div>
                        <div class="state__score"><?= $dataMatch['goals2'] ?></div>
                        <?php else: ?>
                            <div class="state__score-middle grey-text">VS</div>
                        <?php endif ?>
                     
                    </div>
                    <div data-team2-id="<?= $dataMatch['team2_id'] ?>" class="controls__logo logo-team2">
                        <img src="<?= $team_logo_path ?><?= $dataMatch['team2_photo'] ?>">
                    </div>
                </div>
                <?php if($dataMatch['goals1'] != null) :?>
                    <div data-match-canseled="<?=$dataMatch['canseled']  ?>" class="state__text">
                        
                        <?php if($dataMatch['canseled'] != null)  :?>
                            <?php if($dataMatch['canseled'] == 1) :?>
                                Матч завершено
                            <?php elseif($dataMatch['canseled'] == 2) :?>
                                Матч у прямому ефірі
                            <?php else :?>   
                                Матч очікується
                            <?php endif ?>
                        <?php else :?>   
                            Матч завершено
                        <?php endif ?>

                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="preview">
            <?php if(!empty($photo)): ?>
            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper swiperMatchPhoto">
                <div class="swiper-wrapper">
                    <?php foreach($photo as $picture): ?>
                        <?php if(($picture != ".") && ($picture != "..")&&($picture !="tmp")&&($picture !="photos.zip")): ?>
                            <div class="swiper-slide">
                                <!-- <div class="placeholder222" id="placeholder333">Loading...</div> -->
                                <!-- <img 
                                    style="width:100%" 
                                    src="<?= PHOTO_URL .'/'. $data['match_id'] .'/'. $picture ?>" 
                                    alt="" loading="lazy" 
                                    class="swiper-lazy" 
                                    onload="document.getElementById('placeholder333').style.display = 'none';"
                                /> -->
                                <img 
                                    style="width:100%" 
                                    src="<?= PHOTO_URL .'/'. $data['match_id'] .'/'. $picture ?>" 
                                    alt="" loading="lazy" 
                                />
                                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-photo-pagination"></div>
            </div>
            <?php else: ?>
                <div class="danger-info">У цьому матчі фото немає</div>
            <?php endif ?>
        </div>
    </div>

    <!-- Модальное окно с ссылками -->
    <div id="shareModal" class="modal">
        <div class="modal-content">
            <p>Виберіть месенджер для надсилання скріншоту:</p>
            <a id="shareViber" href="#" class="share-btn viber"><img src="<?= IMAGES . '/viber-logo-icon.svg' ?>" alt="Відправити у Viber"> Відправити у Viber</a>
            <a id="shareTelegram" href="#" class="share-btn telegram"><img src="<?= IMAGES . '/telegram-logo-icon.svg' ?>" alt="Відправити у Telegram"> Відправити у Telegram</a>
            <button id="closeModal" class="close-btn">Закрити</button>
        </div>
    </div>
    
</section>

<script>
    var swiper = new Swiper(".swiperMatchPhoto", {
        slidesPerView: 'auto',        
        lazy: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-photo-pagination",
            clickable: true
        },
    });

$(document).ready(function(){
	
	// -- Save image
	$(".save-image").click(function (e) {
		e.preventDefault(); // Отключаем переход по ссылке

		// Получаем ID блока из атрибута data-target
		var targetId = $(this).data("target");
		var content = $("#" + targetId); // Находим блок по ID

		// Сохраняем блок в изображение
		html2canvas(content[0]).then(function (canvas) {
			// Создаем ссылку для скачивания изображения
			var link = document.createElement("a");
			link.download = targetId + ".png"; // Название файла совпадает с ID блока
			link.href = canvas.toDataURL("image/png");
			link.click(); // Автоматически кликаем по ссылке для загрузки
		}).catch(function (error) {
			console.error("Ошибка при сохранении изображения:", error);
		});
	});
});

</script>

