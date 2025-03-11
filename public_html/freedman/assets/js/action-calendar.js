
$(document).ready(function () {

     // ВЕРХНИЙ СЛАЙДЕР С ДАТАМИ МАТЧЕЙ
    $('.calendar-of-matches__grid-container').on('click', '[data-first-day]', function (e) {

        e.preventDefault();

        var newUrl = $(this).attr('href'); // Получаем URL из атрибута href  

        // Обновляем адресную строку  
        window.history.pushState({ path: newUrl }, '', newUrl);

        let turnir = $(this).attr('data-turnir');
        let lastTur = $(this).attr('data-lasttur');
        let selectedDate = $(this).attr('data-first-day');
        let firstDay = $(this).attr('data-first-day');
        let lastDay = $(this).attr('data-last-day');

        if (firstDay) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ 
                    turnir: turnir, 
                    lasttur: lastTur, 
                    action: 'calendar_of_matches', 
                    selected_date: selectedDate,  
                    first_day: firstDay,
                    last_day: lastDay
                }),
                dataType: 'json', // Ожидаем данные в формате JSON
                success: function (response) {

                    $(".calendar-of-matches__grid-container").html(response.section1);

                    document.querySelectorAll(".card-of-matches").forEach(function (card) {
                        card.addEventListener("click", function (event) {
                            // Проверяем, кликнули ли по .card-of-matches__score
                            if (event.target.closest(".card-of-matches__score")) {
                                // Ищем внутри текущего блока элемент с data-match-stats
                                let statsLink = card.querySelector("[data-match-stats]");
                                if (statsLink) {
                                    statsLink.click(); // Имитируем клик по ссылке
                                }
                            }
                        });
                    });
   

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
                    
                    function toggleScrollbar(swiper) {
                        const scrollbar = document.querySelector('.swiper-scrollbar');
                        
                        if (!scrollbar) return; // Проверяем, есть ли скроллбар
                    
                        if (swiper.isLocked) {
                            scrollbar.style.display = 'none'; // Скрываем скроллбар
                        } else {
                            scrollbar.style.display = 'block'; // Показываем скроллбар
                        }
                    }

                    $("#controls").html(response.section2);

                },
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });


    // КНОПКА АНОНС. Регистрация обработчика события один раз для контейнера
    $('.calendar-of-matches__grid-container').on('click', '[data-anons]', function (e) {

        $('[data-turnir]').css({ 'background': '' });
        $(this).css({ 'background': 'red' });
        e.preventDefault();
        e.stopPropagation(); // Останавливаем всплытие события

        let matchId = $(this).data('anons');
        let tur = $(this).data('tur');
        let turnir = $(this).data('turnir');

        // Имя параметра и новое значение  
        var paramName = 'anons';       // Замените на нужное имя параметра  

        // Получаем текущий URL  
        var currentUrl = window.location.href;

        var urlObj = new URL(currentUrl);

        // Проверяем наличие параметра  
        if (urlObj.searchParams.has(paramName)) {
            // Если параметр найден, изменяем его значение  
            urlObj.searchParams.set(paramName, matchId);
        } else {
            // Если параметра нет, добавляем его с новым значением  
            urlObj.searchParams.append(paramName, matchId);
        }

        if (matchId) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ match_id: matchId, tur: tur, turnir: turnir, action: 'anons' }),
                success: function (response) {
                    $('.green-zone').html(response);
                }, // Привязываем контекст к функции 
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });


    // КНОПКА СТАТИСТИКА МАТЧА. Регистрация обработчика события один раз для контейнера
    $('.calendar-of-matches__grid-container').on('click', '[data-match-stats]', function (e) {

        $('[data-turnir]').css({ 'background': '' });
        $(this).css({ 'background': 'red' });
        e.preventDefault();
        e.stopPropagation(); // Останавливаем всплытие события

        let matchId = $(this).data('match-stats');
        let tur = $(this).data('tur');
        let turnir = $(this).data('turnir');
        let team1Id = $(this).data('team1-id');
        let team2Id = $(this).data('team2-id');

        // Имя параметра и новое значение  
        var paramName = 'match_stats';

        // Получаем текущий URL  
        var currentUrl = window.location.href;

        var urlObj = new URL(currentUrl);

        // Проверяем наличие параметра  
        if (urlObj.searchParams.has(paramName)) {
            // Если параметр найден, изменяем его значение  
            urlObj.searchParams.set(paramName, matchId);
        } else {
            // Если параметра нет, добавляем его с новым значением  
            urlObj.searchParams.append(paramName, matchId);
        }

        if (matchId) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ match_id: matchId, tur: tur, turnir: turnir, action: 'match_stats', team1_id: team1Id, team2_id: team2Id }),
                success: function (response) {
                    $('.green-zone').html(response);
                }, // Привязываем контекст к функции 
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    // КНОПКА ККД ГРАВЦЯ
    $('.calendar-of-matches__grid-container').on('click', '[data-kkd]', function (e) {

        $('[data-turnir]').css({ 'background': '' });
        $(this).css({ 'background': 'red' });
        e.preventDefault();
        e.stopPropagation(); // Останавливаем всплытие события

        let matchId = $(this).data('kkd');
        let tur = $(this).data('tur');
        let turnir = $(this).data('turnir');

        // Имя параметра и новое значение  
        var paramName = 'kkd';

        // Получаем текущий URL  
        var currentUrl = window.location.href;

        var urlObj = new URL(currentUrl);

        // Проверяем наличие параметра  
        if (urlObj.searchParams.has(paramName)) {
            // Если параметр найден, изменяем его значение  
            urlObj.searchParams.set(paramName, matchId);
        } else {
            // Если параметра нет, добавляем его с новым значением  
            urlObj.searchParams.append(paramName, matchId);
        }

        if (matchId) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ match_id: matchId, tur: tur, turnir: turnir, action: 'kkd' }),
                success: function (response) {
                    $('.green-zone').html(response);
                }, // Привязываем контекст к функции 
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    // КНОПКА ПРЕВЬЮ (короткие видео)
    $('.calendar-of-matches__grid-container').on('click', '[data-preview]', function (e) {

        $('[data-turnir]').css({ 'background': '' });
        $(this).css({ 'background': 'red' });
        e.preventDefault();
        e.stopPropagation(); // Останавливаем всплытие события

        let matchId = $(this).data('preview');
        let tur = $(this).data('tur');
        let turnir = $(this).data('turnir');

        // Имя параметра и новое значение  
        var paramName = 'preview';

        // Получаем текущий URL  
        var currentUrl = window.location.href;

        var urlObj = new URL(currentUrl);

        // Проверяем наличие параметра  
        if (urlObj.searchParams.has(paramName)) {
            // Если параметр найден, изменяем его значение  
            urlObj.searchParams.set(paramName, matchId);
        } else {
            // Если параметра нет, добавляем его с новым значением  
            urlObj.searchParams.append(paramName, matchId);
        }

        if (matchId) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ match_id: matchId, tur: tur, turnir: turnir, action: 'preview' }),
                success: function (response) {
                    $('.green-zone').html(response);
                }, // Привязываем контекст к функции 
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    // КНОПКА ВИДЕО
    $('.calendar-of-matches__grid-container').on('click', '[data-video]', function (e) {

        $('[data-turnir]').css({ 'background': '' });
        $(this).css({ 'background': 'red' });
        e.preventDefault();
        e.stopPropagation(); // Останавливаем всплытие события

        let matchId = $(this).data('video');
        let tur = $(this).data('tur');
        let turnir = $(this).data('turnir');

        // Имя параметра и новое значение  
        var paramName = 'video';

        // Получаем текущий URL  
        var currentUrl = window.location.href;

        var urlObj = new URL(currentUrl);

        // Проверяем наличие параметра  
        if (urlObj.searchParams.has(paramName)) {
            // Если параметр найден, изменяем его значение  
            urlObj.searchParams.set(paramName, matchId);
        } else {
            // Если параметра нет, добавляем его с новым значением  
            urlObj.searchParams.append(paramName, matchId);
        }

        if (matchId) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ match_id: matchId, tur: tur, turnir: turnir, action: 'video' }),
                success: function (response) {
                    $('.green-zone').html(response);
                }, // Привязываем контекст к функции 
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    // КНОПКА ФОТО
    $('.calendar-of-matches__grid-container').on('click', '[data-photo]', function (e) {

        $('[data-turnir]').css({ 'background': '' });
        $(this).css({ 'background': 'red' });
        e.preventDefault();
        e.stopPropagation(); // Останавливаем всплытие события

        let matchId = $(this).data('photo');
        let tur = $(this).data('tur');
        let turnir = $(this).data('turnir');

        // Имя параметра и новое значение  
        var paramName = 'photo';

        // Получаем текущий URL  
        var currentUrl = window.location.href;

        var urlObj = new URL(currentUrl);

        // Проверяем наличие параметра  
        if (urlObj.searchParams.has(paramName)) {
            // Если параметр найден, изменяем его значение  
            urlObj.searchParams.set(paramName, matchId);
        } else {
            // Если параметра нет, добавляем его с новым значением  
            urlObj.searchParams.append(paramName, matchId);
        }

        if (matchId) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ match_id: matchId, tur: tur, turnir: turnir, action: 'photo' }),
                success: function (response) {
                    $('.green-zone').html(response);
                }, // Привязываем контекст к функции 
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    // КНОПКА
    $('.match-calendar').on('click', '[data-switch-tur]', function (e) {


        e.preventDefault();

        // Получаем URL из атрибута href
        var newUrl = $(this).attr('href');
        // Обновляем адресную строку  
        window.history.pushState({ path: newUrl }, '', newUrl);

        let tur = $(this).attr('data-switch-tur');
        let turnir = $(this).attr('data-turnir');
        let lastTur = $(this).attr('data-lasttur');
        let originalUrl = $(this).attr('href');

        if (tur) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ tur: tur, turnir: turnir, lasttur: lastTur, action: 'match_calendar', url: originalUrl }),
                success: function (response) {
                    $(".match-calendar").html(response);

                    swiperMonthControls = new Swiper(".swiper-month-controls", {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        scrollbar: {
                            el: '.swiper-scrollbar',
                            hide: false,
                            draggable: true,
                        },
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

                    swipersMatches = new Swiper(".swiper-matches", {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        scrollbar: {
                            el: '.swiper-scrollbar',
                            hide: false,
                            draggable: true,
                        },
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    $('#controls').on('click', '[data-turid]', function (e) {

        e.preventDefault();



        $('.card-of-matches__controls-link').css('background', '');
        console.log($('.card-of-matches__controls-link'));


        // Инициализация: записываем GET-параметр в data-атрибут
        const turId = getUrlParameter('tur');
        if (turId) {
            $(this).attr('data-turid', turId);
        }

        let tur = $(this).attr('data-turid');
        let turnir = $(this).attr('data-turnir');
        let lastTur = $(this).attr('data-lasttur');

        if (tur) {
            $.ajax({
                type: "post",
                url: "../freedman/actions/actions.php",
                data: JSON.stringify({ tur: tur, turnir: turnir, lasttur: lastTur, action: 'green_zone' }),
                success: function (response) {
                    $('.green-zone').html(response);
                },
                error: function (xhr, status, error) {
                    console.error('Ошибка AJAX:', error); // Логируем ошибку
                    alert('Ошибка при загрузке данных. Попробуйте позже.');
                }
            });
        }
    });

    $('#controls').on('click', '#after-play', function (e) {
        e.preventDefault();

        link = $(this).attr('href');
        console.log(link);
        $.ajax({
            type: "post",
            url: "../freedman/actions/actions.php",
            data: JSON.stringify({ link: link, action: 'after-play' }),
            success: function (response) {
                $('.green-zone').html(response);
            },
            error: function (xhr, status, error) {
                console.error('Ошибка AJAX:', error); // Логируем ошибку
                alert('Ошибка при загрузке данных. Попробуйте позже.');
            }
        });

    });

    $('#controls').on('click', '#top-goals', function (e) {
        e.preventDefault();

        link = $(this).attr('href');
        console.log(link);
        $.ajax({
            type: "post",
            url: "../freedman/actions/actions.php",
            data: JSON.stringify({ link: link, action: 'top-goals' }),
            success: function (response) {
                $('.green-zone').html(response);
            },
            error: function (xhr, status, error) {
                console.error('Ошибка AJAX:', error); // Логируем ошибку
                alert('Ошибка при загрузке данных. Попробуйте позже.');
            }
        });

    });

    $('#controls').on('click', '#top-save', function (e) {
        e.preventDefault();

        link = $(this).attr('href');
        console.log(link);
        $.ajax({
            type: "post",
            url: "../freedman/actions/actions.php",
            data: JSON.stringify({ link: link, action: 'top-save' }),
            success: function (response) {
                $('.green-zone').html(response);
            },
            error: function (xhr, status, error) {
                console.error('Ошибка AJAX:', error); // Логируем ошибку
                alert('Ошибка при загрузке данных. Попробуйте позже.');
            }
        });

    });


    colorRedBtnCurrent();


});


function colorRedBtnCurrent() {
    let tur = $('.month-controls__button--current').attr('data-turid');
    let lastTur = $('.month-controls__button--current').attr('data-lasttur');
    let dateLastTur = $('.month-controls__button--current').attr('data-first-day');// Дата в формате строки

    // Получаем сегодняшнюю дату без времени (YYYY-MM-DD)
    let currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Убираем время

    // Преобразуем строку в объект Date
    let dateLastTurObject = new Date(dateLastTur.replace(" ", "T")); // Преобразуем в формат ISO

    // Прибавляем 5 дней
    dateLastTurObject.setDate(dateLastTurObject.getDate() + 4);

    if (tur <= lastTur && dateLastTurObject > currentDate) {
        $('#anons-1').css({ 'background': 'red' });
    }
}

// Функция для получения значения GET-параметра
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}


