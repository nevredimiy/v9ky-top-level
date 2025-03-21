<section class="calendar-of-matches">   

    <?php if(!isset($dateMatches)): ?>
        <div class="flex justify-center w-full">
            <h2 >Ще немає жодного матчу</h2>
        </div>
    <?php else: ?>
        <div class="calendar-of-matches__grid-container">
            <?php require_once 'calendar_of_matches_content.tpl.php' ?>  
        </div> <!-- calendar-of-matches__grid-container -->
    <?php endif ?>        
   
</section>