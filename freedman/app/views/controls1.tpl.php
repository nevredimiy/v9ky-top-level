<section id="controls" class="controls">
    <?php if($currentTur <= $lastTur && $dateLastTur <= $dateNow) : //выводим только в случае если текущая дата меньше сыгранного тура + 5дней. Это времени достаточно для заполнения статистики ?>
        <?php require VIEWS . '/controls_content.tpl.php' ?>
    <?php endif ?>
</section>