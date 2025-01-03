<?php 
require_once CONTROLLERS . '/head.php';
require_once CONTROLLERS . '/menu.php';
require_once CONTROLLERS . '/leagues.php';
?>

<section class="teams">
    <div class="teams__wrap">
        <div class="teams__list team">

            <?php foreach($teams as $team) : ?>
            <a class="team__item" href="<?php echo "{$site_url}/{$tournament}/team_page/id/{$team['id']}" ?>">
                <span><?= trim($team['name']) ?></span>
                <img src="<?= $team_logo_path ?><?= $team['pict'] ?>" width="120" alt="Team Logo">
            </a>
            <?php endforeach ?>

        </div>
    </div>
</section>

<?php require_once CONTROLLERS . '/footer.php'; ?>