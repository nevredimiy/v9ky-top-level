<style>
body {
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    /* Высота экрана */
}

.main {
    flex: 1;
    /* Заполняет оставшееся пространство */
}
</style>


<section class="match-calendar">

    <?php require_once VIEWS . "/match_calendar_content.tpl.php"; ?>

</section>