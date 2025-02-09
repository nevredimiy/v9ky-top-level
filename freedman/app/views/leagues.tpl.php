<section class="leagues">
	<div class="leagues__container swiper-leagues">
		<div class="leagues__wrap swiper-wrapper">
			
			<?php foreach ($leagues as $league) : ?>
			<?php $league['id']==$turnir ? $active = " leagues__item-active" : $active = ""; ?>
			

			<div class="swiper-slide">
				<div class="leagues__item<?= $active ?>">
				<a data-tournament="<?=$league['slug']?>" data-turnir="<?= $league['id'] ?>" href="<?=$site_url?>/<?=$league['link']?>">
					<span class="leagues__item-title"><?=$league['name']?></span>
					<div class="leagues__item-location">
						<img src="/css/components/leagues/assets/images/location-icon.svg" alt="location">
						<span><?= $league['locale_name'] ?></span>              
					</div>
				</a>
				</div>
			</div>
		
			<?php endforeach ?>
		</div>
		<div class="swiper-scrollbar-leagues"></div>
	</div>
</section>