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
