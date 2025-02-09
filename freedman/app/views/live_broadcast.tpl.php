<section class="live_broadcast">
    <div class="container">
        <h2 class="table-league__title title title--inverse ">
        <span>Live</span>
        <span>Прямий ефір</span>
        </h2>
        <div class="live">
            <div class="live__head">
                <div class="live__cell">Місто</div>
                <div class="live__cell">Тур</div>
                <div class="live__cell">Дата</div>
                <div class="live__cell">Час</div>
                <div class="live__cell">Поле</div>
                <div class="live__cell">Матч</div>
            </div>
            <?php foreach($liveMatches as $liveMatch):?>
            <div class="live__row">
                <div class="live__cell"><?= $liveMatch['city_name'] ?></div>
                <div class="live__cell"><?= $liveMatch['tur'] ?></div>
                <div class="live__cell"><?= getFormateDate($liveMatch['date'], true) ?></div>
                <div class="live__cell"><?= getTime($liveMatch['date']) ?></div>
                <div class="live__cell"><?= $liveMatch['field_name'] ?></div>
                <div class="live__cell expand-5">
                    <div class="block-multiline"><?= $liveMatch['team1_name'] ?></div>
                    <div><img src="<?= $team_logo_path ?><?= $liveMatch['team1_logo'] ?>" alt="" width="30" height="30"></div>
                    <div><?= $liveMatch['goals1'] ?>:<?= $liveMatch['goals2'] ?></div>
                    <div><img src="<?= $team_logo_path ?><?= $liveMatch['team2_logo'] ?>" alt="" width="30" height="30"></div>
                    <div class="block-multiline"><?= $liveMatch['team2_name'] ?></div>
                </div>
            </div>
            <?php endforeach ?>
        </div>

        <h2 class="table-league__title title title--inverse ">
            <span>Очікуются</span>
        </h2>
        <div class="live">
            <div class="live__head">
                <div class="live__cell">Місто</div>
                <div class="live__cell">Тур</div>
                <div class="live__cell">Дата</div>
                <div class="live__cell">Час</div>
                <div class="live__cell">Поле</div>
                <div class="live__cell">Матч</div>
            </div>
            <?php foreach($incommingMatches as $incommingMatch):?>
            <div class="live__row">
                <div class="live__cell"><?= $incommingMatch['city_name'] ?></div>
                <div class="live__cell"><?= $incommingMatch['tur'] ?></div>
                <div class="live__cell"><?= getFormateDate($incommingMatch['date'], true) ?></div>
                <div class="live__cell"><?= getTime($incommingMatch['date']) ?></div>
                <div class="live__cell"><?= $incommingMatch['field_name'] ?></div>
                <div class="live__cell expand-5">
                    <div class="block-multiline"><?= $incommingMatch['team1_name'] ?></div>
                    <div><img src="<?= $team_logo_path ?><?= $incommingMatch['team1_logo'] ?>" alt="" width="30" height="30"></div>
                    <div>VS</div>
                    <div><img src="<?= $team_logo_path ?><?= $incommingMatch['team2_logo'] ?>" alt="" width="30" height="30"></div>
                    <div class="block-multiline"><?= $incommingMatch['team2_name'] ?></div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
       
    </div>
</section>