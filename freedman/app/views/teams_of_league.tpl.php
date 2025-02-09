<section class="teams">
    <div class="teams__wrap">
        <div class="teams__list team">

            <?php foreach($teams as $team) : ?>
            <a class="team__item" href="<?php echo "{$site_url}/{$tournament}/team_page/id/{$team['id']}" ?>">
                <span><?= trim($team['name']) ?></span>
                <img src="<?= $team_logo_path ?><?= $team['pict'] ?>" alt="Team Logo">
            </a>
            <?php endforeach ?>

        </div>
    </div>
</section>

