swipersLeagues = new Swiper(".swiper-leagues", {
  slidesPerView: 'auto',
  spaceBetween: 10,
  scrollbar: {
    el: '.swiper-scrollbar-leagues',
    hide: false,
    draggable: true,
  },
});

$(document).ready(function () {
  function adjustSwiperAlignment() {
      const $swiperWrapper = $('.swiper-leagues .swiper-wrapper');
      const $swiperSlides = $('.swiper-leagues .swiper-slide');

      if ($swiperWrapper.length && $swiperSlides.length) {
          // Ширина контейнера
          const wrapperWidth = $swiperWrapper.width();

          // Суммарная ширина всех слайдов
          const slidesWidth = $swiperSlides.toArray().reduce((total, slide) => {
              return total + $(slide).outerWidth(true); // Включаем margin
          }, 0);

          // Устанавливаем justify-content
          if (slidesWidth <= wrapperWidth) {
              $swiperWrapper.css('justify-content', 'center');
          } else {
              $swiperWrapper.css('justify-content', 'flex-start');
          }
      }
  }

  // Вызываем при загрузке страницы
  adjustSwiperAlignment();

  // Обновляем при изменении размера окна
  $(window).on('resize', adjustSwiperAlignment);
});

