swipersControls = new Swiper(".swiper-matches", {
  slidesPerView: 'auto',
  spaceBetween: 20,
  scrollbar: {
    el: '.swiper-scrollbar-matches',
    hide: false,
    draggable: true,
  },
  breakpoints: {
    // when window width is >= 820px
    1259: {
      scrollbar: false,
      allowTouchMove: false, // Отключает свайпы
      noSwiping: true, // Запрещает смахивание
      noSwipingClass: 'swiper-container',
    }
  }
});
