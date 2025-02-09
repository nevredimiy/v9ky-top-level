const swipersTable = document.querySelector('.swiper-table');

if (swipersTable) {
  new Swiper('.swiper-table', {
    // enabled: Boolean(window.innerWidth < 1260),
    slidesPerView: 'auto',
    spaceBetween: 0, // Без отступов
    freeMode: true, // Разрешает свободное перетаскивание
    freeModeMomentum: false, // Отключает автоматический возврат
    freeModeSticky: false, // Отключает прилипание к ближайшему элементу
    resistance: true, // Добавляет эффект сопротивления
    resistanceRatio: 0.9, // Уменьшает "отскок" за границы
    speed: 1000,
    allowTouchMove: true,
    scrollbar: {
      el: '.swiper-scrollbar-table',
      hide:false
    },
    breakpoint: {
      840: {
        scroollbar: {
          hide: true
        }
      }
    }
  
  });
} 