swiperMonthControls = new Swiper('.swiper-month-controls', {
  enabled: true,
  slidesPerView: 'auto',
  spaceBetween: 10,
  speed: 400,
  scrollbar: {
    el: '.swiper-scrollbar-month-controls',
    dragSize: 70,
    hide: false,
    draggable: true,
  },
  on: {
      init: function () {
          toggleScrollbar(this);
      },
      resize: function () {
          toggleScrollbar(this);
      }
  }
});


// перемещаем слайдер на текущий тур (тот который красненький)
const slideIndex = $('.swiper-slide').toArray().findIndex(slide =>
  $(slide).find('.month-controls__button--current').length > 0
);

console.log(slideIndex);

if (slideIndex !== -1) {
  // Центрируем найденный слайд
  swiperMonthControls.slideTo(slideIndex);
} else {
  console.warn('Слайд с указанным дочерним классом не найден.');
}

function toggleScrollbar(swiper) {
  const scrollbar = document.querySelector('.swiper-scrollbar');
  
  if (!scrollbar) return; // Проверяем, есть ли скроллбар

  if (swiper.isLocked) {
      scrollbar.style.display = 'none'; // Скрываем скроллбар
  } else {
      scrollbar.style.display = 'block'; // Показываем скроллбар
  }
}