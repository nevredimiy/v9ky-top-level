swiperMonthControls = new Swiper('.swiper-month-controls', {
  enabled: true,
  slidesPerView: 'auto',
  spaceBetween: 10,
  speed: 400,
  scrollbar: {
    el: '.swiper-scrollbar',
    dragSize: 70,
    hide: false
  },
  breakpoint: {
    860: {
      scrollbar: {
        hide: true
      }
    },
    1260: {
      scrollbar: {
        hide: false
      }
    }
  }
});

// перемещаем слайдер на текущий тур (тот который красненький)
const slideIndex = $('.swiper-slide').toArray().findIndex(slide => 
  $(slide).find('.month-controls__button--current').length > 0
);

if (slideIndex !== -1) {
  // Центрируем найденный слайд
  swiperMonthControls.slideTo(slideIndex);
} else {
  console.warn('Слайд с указанным дочерним классом не найден.');
}