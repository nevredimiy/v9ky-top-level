
$(document).ready(function () {

    $('.calendar-of-matches__grid-container').on('click', '[data-turid]', function (e) {

        e.preventDefault();

        var newUrl = $(this).attr('href'); // Получаем URL из атрибута href  

        // Обновляем адресную строку  
        window.history.pushState({ path: newUrl }, '', newUrl);

        let tur = $(this).attr('data-turid');
        let turnir = $(this).attr('data-turnir');
        let lastTur = $(this).attr('data-lasttur');

        if (tur) {
            $.ajax({
                type: "post",
                url: "actions.php",
                data: JSON.stringify({ tur: tur, turnir: turnir, lasttur: lastTur, action: 'calendar_of_matches' }),
                success: function (response) {
                    $(".calendar-of-matches__grid-container").html(response);

                    swipersLeagues = new Swiper(".swiper-month-controls", {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        scrollbar: {
                            el: '.swiper-scrollbar',
                            hide: false,
                            draggable: true,
                        },
                    });

                    swipersLeagues = new Swiper(".swiper-matches", {
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


    // Регистрация обработчика события один раз для контейнера
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
                url: "actions.php",
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


    // Регистрация обработчика события один раз для контейнера
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
                url: "actions.php",
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
                url: "actions.php",
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
                url: "actions.php",
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
                url: "actions.php",
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
                url: "actions.php",
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
});


