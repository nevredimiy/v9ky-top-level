<section class="controls">
	<button id="captureAndShare" class="anons__share-btn">
		<img src="<?= IMAGES . '/button-share-icon.svg' ?>" alt="Зберегти зображення">
	</button>
	<div id="kkd-players" class="controls__container content-to-capture">		
		<div class="controls__head">
			<div class="controls__head-title">ККД Гравців</div>
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
                <div class="state__text">Матч завершено</div>
                <?php endif ?>
            </div>
		</div>
		<?php if(!empty($teamCompositionAndStats1)) : ?>
		<div class="kkd__tables">
			<table class="kkd__table team1">
				<div class="kkd__tables-head"><?= $dataMatch['team1_name'] ?></div>
				<tr>
					<td></td>
					<td>15</td>
					<td>10</td>
					<td>10</td>
					<td>3</td>
					<td>3</td>
					<td>3</td>
					<td>7</td>
					<td>4</td>
					<td>5</td>
					<td>3</td>
					<td>8</td>
					<td>5</td>
					<td>4</td>
					<td>15</td>
					<td>7</td>
					<td></td>
				</tr>
				<tr>
					<td>Гравець</td>
					<td>Г</td>
					<td>ГП</td>
					<td>ЗП</td>
					<td>П</td>
					<td>П</td>
					<td>ВМ</td>
					<td>У</td>
					<td>У</td>
					<td>ОБ</td>
					<td>ОБ</td>
					<td>В</td>
					<td>В</td>
					<td>Б</td>
					<td>С</td>
					<td>С</td>
					<td>Т</td>
				</tr>
				<?php foreach($teamCompositionAndStats1 as $player) :?>
				<tr>
					<td><?= $player['lastname'] ?> <?= $player['firstname'] ?></td>
					<td><?= $player['goals_scored'] ?></td>
					<td><?= $player['asist'] ?></td>
					<td><?= $player['build_up'] ?></td>
					<td><?= $player['success_pass'] ?></td>
					<td><?= $player['bad_pass'] ?></td>
					<td><?= $player['loss_ball'] ?></td>
					<td><?= $player['shot_on'] ?></td>
					<td><?= $player['shot_off'] ?></td>
					<td><?= $player['successfull_dribble'] ?></td>
					<td><?= $player['failed_dribble'] ?></td>
					<td><?= $player['successful_tackle'] ?></td>
					<td><?= $player['failed_tackle'] ?></td>
					<td><?= $player['success_block'] ?></td>
					<td><?= $player['success_save'] ?></td>
					<td><?= $player['failed_save'] ?></td>
					<td><?= $player['total'] ?></td>
				</tr>
				<?php endforeach ?>
				
			</table>
			<table class="kkd__table team2">
				<div class="kkd__tables-head"><?= $dataMatch['team2_name'] ?></div>
				<tr>
					<td></td>
					<td>15</td>
					<td>10</td>
					<td>10</td>
					<td>3</td>
					<td>3</td>
					<td>3</td>
					<td>7</td>
					<td>4</td>
					<td>5</td>
					<td>3</td>
					<td>8</td>
					<td>5</td>
					<td>4</td>
					<td>15</td>
					<td>7</td>
					<td></td>
				</tr>
				<tr>
					<td>Гравець</td>
					<td>Г</td>
					<td>ГП</td>
					<td>ЗП</td>
					<td>П</td>
					<td>П</td>
					<td>ВМ</td>
					<td>У</td>
					<td>У</td>
					<td>ОБ</td>
					<td>ОБ</td>
					<td>В</td>
					<td>В</td>
					<td>Б</td>
					<td>С</td>
					<td>С</td>
					<td>Т</td>
				</tr>
				<?php foreach($teamCompositionAndStats2 as $player) :?>
				<tr>
					<td><?= $player['lastname'] ?> <?= $player['firstname'] ?></td>
					<td><?= $player['goals_scored'] ?></td>
					<td><?= $player['asist'] ?></td>
					<td><?= $player['build_up'] ?></td>
					<td><?= $player['success_pass'] ?></td>
					<td><?= $player['bad_pass'] ?></td>
					<td><?= $player['loss_ball'] ?></td>
					<td><?= $player['shot_on'] ?></td>
					<td><?= $player['shot_off'] ?></td>
					<td><?= $player['successfull_dribble'] ?></td>
					<td><?= $player['failed_dribble'] ?></td>
					<td><?= $player['successful_tackle'] ?></td>
					<td><?= $player['failed_tackle'] ?></td>
					<td><?= $player['success_block'] ?></td>
					<td><?= $player['success_save'] ?></td>
					<td><?= $player['failed_save'] ?></td>
					<td><?= $player['total'] ?></td>
				</tr>
				<?php endforeach ?>
			</table>
		</div>
		<?php else: ?>
			<div class="danger-info">Дані статистики гравців ще не внесені адміністратором. Зайдіть пізніше</div>
		<?php endif ?>
		<div class="kkd__info">
			<ul class="kkd__list">
				<li class="kkd__item"><span class="text-green">Г</span> - Гол</li>
				<li class="kkd__item"><span class="text-green">ГП</span> - Гольова передача</li>
				<li class="kkd__item"><span class="text-green">ЗП</span> - Загострюючий пас</li>
				<li class="kkd__item"><span class="text-green">П</span> - Вдалий пас</li>
				<li class="kkd__item"><span class="text-red">П</span> - Невдалий пас</li>
				<li class="kkd__item"><span class="text-red">ВМ</span> - Втрата м'яча</li>
				<li class="kkd__item"><span class="text-green">У</span> - Удар по воротам</li>
				<li class="kkd__item"><span class="text-red">У</span> - Удар повз воріт</li>
				<li class="kkd__item"><span class="text-green">ОБ</span> - Вдала обводка</li>
				<li class="kkd__item"><span class="text-red">ОБ</span> - Невдала обводка</li>
				<li class="kkd__item"><span class="text-green">В</span> - Вдалий відбір м'яча</li>
				<li class="kkd__item"><span class="text-red">В</span> - Невдалий відбір м'яча</li>
				<li class="kkd__item"><span class="text-green">Б</span> - Блок</li>
				<li class="kkd__item"><span class="text-green">С</span> - Вдалий сейв</li>
				<li class="kkd__item"><span class="text-red">С</span> - Невдалий сейв</li>
				<li class="kkd__item"><span>Т</span> - Сумарний тотал</li>
			</ul>
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