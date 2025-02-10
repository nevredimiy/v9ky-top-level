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
