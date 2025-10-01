$(document).ready(function () {
    const $modal = $("#matchStatsModal");
    const $modalContent = $("#stats_modal-content");
    const $modalLoading = $("#stats_modal-loading");
    const $closeButton = $(".close-btn");
    const $body = $(".body");

    $(".info_of_match").on("click", function (event) {

        if ($(event.target).closest(".score-of-match").length) {
            let $statsLink = $(this).find("[data-match-id]");

            if ($statsLink.length) {
                let matchId = $statsLink.data("match-id");

                // Открываем модальное окно и показываем "Загрузка..."
                $modal.css("display", "flex");
                $modalLoading.show();
                $modalContent.hide().empty();
                $body.addClass("no-scroll");

                // Отправляем AJAX-запрос на сервер
                $.ajax({
                    url: "/freedman/actions/action_team_page.php",
                    type: "POST",
                    data: { match_id: matchId },
                    dataType: "html", // Изменено на HTML, так как сервер возвращает шаблон
                    success: function (response) {
                        // Показываем загруженные данные (HTML-шаблон)
                        $modalLoading.hide();
                        $modalContent.show().html(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("Ошибка AJAX:", textStatus, errorThrown);
                        console.log("Ответ сервера:", jqXHR.responseText);
                        $modalLoading.hide();
                        $modalContent.show().html("<p>Помилка завантаження даних</p>");
                    }
                });
            }
        }
    });

    // Закрытие модального окна
    $closeButton.on("click", function () {
        $modal.hide();
        $body.removeClass("no-scroll"); // Включаем скролл обратно
    });

    // Закрытие модального окна при клике вне контента
    $(window).on("click", function (event) {
        if ($(event.target).is("#matchStatsModal")) {
            $modal.hide();
            $body.removeClass("no-scroll"); // Включаем скролл обратно
        }
    });
});
