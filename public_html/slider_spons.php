<?php
if (!defined('READFILE')){exit('Wrong way to file');} 
?>

	<div class="slides-frame">
	  	<div class="slider-spons">
<?php
$recordspons = $db->Execute("select * from v9ky_sponsor where active=1 order by rand()");
$i = 0;

while (!$recordspons->EOF) {
  if (($gorod_en->fields['id']==$recordspons->fields['city'])or($recordspons->fields['city']==1)){
  if ($i ==0) echo'<div class="slide">
';
  echo '		    	<noindex><a href="'.$recordspons->fields['site'].'" target="_blank" rel="nofollow"><img src="'.$baner_path.$recordspons->fields['pict'].'" alt="'.$recordspons->fields['name'].'"></a></noindex>
';
  
  
  
  $i = $i + 1;
  if ($i == 4) {echo'</div>
'; $i = 0;}
  }
  $recordspons->MoveNext();
}
if ($i > 0) echo'</div>';	
?>		


	    </div>
	</div>
