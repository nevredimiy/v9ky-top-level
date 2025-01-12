<div class="slides-frame">
    <div class="slider-spons">
        <?php
        $sponses = $dbF->query("SELECT * FROM `v9ky_sponsor` WHERE `active` = 1 ORDER BY rand()")->findAll();
        $i = 0;

        foreach($sponses as $spons){
            if( ($gorod_en['id'] == $spons['city']) || $spons['city'] == 1 ){
                if( $i == 0 ) {
                    echo '<div class="slide">';
                }
                echo '<noindex><a href="'.$spons['site'].'" target="_blank" rel="nofollow"><img src="'.$baner_path.$spons['pict'].'" alt="'.$spons['name'].'"></a></noindex>';
                $i = $i + 1;
                if ($i == 4) {
                    echo'</div>'; 
                    $i = 0;
                }
            }
        }
        if ($i > 0) echo'</div>';	
        ?>		


	    </div>
	</div>
