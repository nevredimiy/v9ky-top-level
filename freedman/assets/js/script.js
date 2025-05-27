$(document).ready(function () {
    $(".save-image").click(function (e) {
        e.preventDefault(); // Отключаем переход по ссылке

        // Получаем ID блока из атрибута data-target
        var targetId = $(this).data("target");
        var content = $("#" + targetId); // Находим блок по ID
        console.log('targetId - ' + targetId);


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

    // Плавное перемещение после клика на якорную ссылку
    // Обработчик клика по ссылке
    $('.scroll-link').click(function (e) {
        e.preventDefault(); // Отключаем стандартное поведение ссылки

        // Получаем ID целевого блока из href ссылки
        var target = $(this).attr('href');

        // Плавная прокрутка
        $('html, body').animate({
            scrollTop: $(target).offset().top // Прокрутка до верхней части блока
        }, 800); // Скорость прокрутки в миллисекундах (800 = 0.8 секунды)
    });


});

document.addEventListener("DOMContentLoaded", function () {
    let postTitle = document.title;
    let postUrl = window.location.href;
    let telegramButton = document.querySelector(".share-telegram");
    let loadingMessage = document.querySelector(".loading-message");

    function openShare(url) {
        window.open(url, "_blank");
    }

    function setButtonState(isLoading) {
        if (isLoading) {
            telegramButton.disabled = true; // Блокируем кнопку
            loadingMessage.innerText = "Створення скриншота..."; // Меняем текст
            telegramButton.classList.add("loading"); // Добавляем класс для стилизации
        } else {
            telegramButton.disabled = false; // Разблокируем кнопку
            loadingMessage.innerText = ""; // Возвращаем текст
            telegramButton.classList.remove("loading"); // Убираем класс загрузки
        }
    }

    function captureAndSendToServer(callback) {
        let captureElement = document.querySelector("#capture"); // Элемент, который будет скриншотиться
        if (!captureElement) {
            alert("Ошибка: элемент для скриншота не найден!");
            return;
        }

        setButtonState(true); // Блокируем кнопку во время обработки

        html2canvas(captureElement).then(canvas => {
            canvas.toBlob(blob => {
                let formData = new FormData();
                formData.append("image", blob, "screenshot.png");

                // Отправка скриншота на сервер
                fetch("../freedman/actions/upload.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {

                        setButtonState(false); // Разблокируем кнопку после загрузки

                        if (data.success) {
                            callback(data.link); // Передаем ссылку на изображение в Telegram
                        } else {
                            console.error("Ошибка загрузки:", data.error);
                            alert("Ошибка загрузки изображения!");
                        }
                    })
                    .catch(error => {
                        setButtonState(false); // Разблокируем кнопку после загрузки

                        console.error("Ошибка AJAX:", error);
                        alert("Ошибка при загрузке данных!");
                    });
            });
        });
    }

});




// Делаем Имя и Фамилию короче. Имя сокращаем до заглавной буквы
function shortenNames() {
    const isSmallScreen = window.matchMedia('(max-width: 800px)').matches;

    if (isSmallScreen) {
        document.querySelectorAll('.name-cell').forEach(cell => {
            const [lastName, firstName] = cell.textContent.split(' ');
            if (firstName) {
                cell.textContent = `${lastName} ${firstName.charAt(0)}.`; // Сокращаем имя
            }
        });
    }
}

// Выполняем при загрузке
shortenNames();

// Отслеживаем изменения
window.matchMedia('(max-width: 800px)').addEventListener('change', shortenNames);


// Код для драга в десктопе
// Находим контейнер
const container = document.querySelector('.draggable-container');


let isDragging = false; // Флаг для отслеживания состояния "перетаскивания"
let startX, startY; // Начальная позиция мыши
let scrollLeft, scrollTop; // Текущая прокрутка контейнера

// Событие начала перетаскивания
container?.addEventListener('mousedown', (e) => {
    isDragging = true; // Активируем режим перетаскивания
    startX = e.pageX - container.offsetLeft; // Позиция мыши относительно контейнера
    startY = e.pageY - container.offsetTop;
    scrollLeft = container.scrollLeft; // Текущая горизонтальная прокрутка
    scrollTop = container.scrollTop; // Текущая вертикальная прокрутка
    container.classList.add('active'); // Добавляем эффект
});

// Событие движения мыши
container?.addEventListener('mousemove', (e) => {
    if (!isDragging) return; // Если не активен режим перетаскивания, выходим
    e.preventDefault();
    const x = e.pageX - container.offsetLeft; // Текущая позиция мыши
    const y = e.pageY - container.offsetTop;
    const walkX = (x - startX) * 2; // Скорость перемещения по горизонтали
    const walkY = (y - startY) * 2; // Скорость перемещения по вертикали
    container.scrollLeft = scrollLeft - walkX; // Обновляем горизонтальную прокрутку
    container.scrollTop = scrollTop - walkY; // Обновляем вертикальную прокрутку
});

// Событие завершения перетаскивания
container?.addEventListener('mouseup', () => {
    isDragging = false; // Деактивируем режим перетаскивания
});

// Событие выхода мыши за пределы контейнера
container?.addEventListener('mouseleave', () => {
    isDragging = false; // Деактивируем режим перетаскивания
});


// Делимся скриншотом в телеграме и вайбере
$(document).ready(function () {
    console.log('222')
    let captureBtn = $("#captureAndShare");
    let modal = $("#shareModal");
    let closeModalBtn = $("#closeModal");
    let shareViber = $("#shareViber");
    let shareTelegram = $("#shareTelegram");

    captureBtn.on("click", function () {
        let captureElement = $(".content-to-capture");

        if (captureElement.length === 0) {
            alert("Помилка: елемент для скріншоту не знайдено!");
            return;
        }

        // Отключаем кнопку, чтобы предотвратить двойные клики
        // captureBtn.prop("disabled", true).text("Створення скриншоту...");
        captureBtn.prop("disabled", true).css("oppacity", "0.5");

        // Создание скриншота
        html2canvas(captureElement[0]).then(canvas => {
            canvas.toBlob(blob => {
                let formData = new FormData();
                formData.append("image", blob, "screenshot.png");

                // Отправка скриншота на сервер
                $.ajax({
                    url: "../freedman/actions/uploads1.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // captureBtn.prop("disabled", false).html("<img src='https://v9ky.in.ua/css/components/match-stats/assets/images/button-share-icon.svg' alt='Зберегти зображення'>");
                        captureBtn.prop("disabled", false).css("opacity", "0.9");

                        try {
                            let data = JSON.parse(response);
                            if (data.success) {
                                let imageUrl = encodeURIComponent(data.link);

                                // Устанавливаем ссылки на Viber и Telegram
                                shareViber.attr("href", `viber://forward?text=${imageUrl}`);
                                shareTelegram.attr("href", `https://t.me/share/url?url=${imageUrl}`);

                                // Показываем модальное окно
                                modal.show();
                            } else {
                                alert("Помилка завантаження зображення!");
                            }
                        } catch (e) {
                            alert("Помилка обробки даних!");
                            console.error("Помилка:", e);
                        }
                    },
                    error: function (xhr, status, error) {
                        captureBtn.prop("disabled", false).html("<img src='https://v9ky.in.ua/css/components/match-stats/assets/images/button-share-icon.svg' alt='Зберегти зображення'>");
                        console.error("Помилка:", error);
                        alert("Помилка завантаження даних!");
                    }
                });
            });
        });
    });

    // Закрытие модального окна
    closeModalBtn.on("click", function () {
        modal.hide();
    });

    // Закрытие при клике вне окна
    $(window).on("click", function (event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });
});
