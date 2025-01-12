<?
//проверка на вшивость
if (!defined('READFILE'))
  {exit('Wrong way to file');};

$items_num=5;
$date=date("d.m.Y"); // ?????.?????.???
$time=date("H:i:s"); // ????:??????:???????
$pict_path="../team_logo/"; //хранилище картинок
$gerb_path="../city_gerb/"; //хранилище гербов городов
$baner_path="../baners/"; //хранилище банеров спонсоров
$banerz_path="../../baner/"; //хранилище банеров внутри страниц
$news_path = "../../picts/news/"; //хранилище картинок новостей
$reglament_path="../../reglamenty/";

Error_Reporting(E_ALL & ~E_NOTICE);
 
  $dblocation = "localhost";
  // ??? ???? ??????, ?? ???????? ??? ????????? ??????
 $dbname = "corsa134_v9kyv2";

$dbuser = "corsa134_v9kyv2";

  $dbpasswd = "2pnr5p92";



   include('adodb5/adodb.inc.php');
   $db = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
   $db->debug = false;
   $db->Connect($dblocation, $dbname, $dbpasswd, $dbuser);
   $db->SetCharSet('utf8');
   //????????? ?????????? ??????
function filter_string($string_to_be_filtered)
     {
	   	$string_to_be_filtered=strip_tags($string_to_be_filtered);
		$string_to_be_filtered=htmlspecialchars($string_to_be_filtered);
		$string_to_be_filtered=mysql_real_escape_string($string_to_be_filtered);
		return $string_to_be_filtered;
		 }
function rus_date() {
// Перевод
 $translate = array(
 "Monday" => "Понедельник",
 "Mon" => "Пн",
 "Tuesday" => "Вторник",
 "Tue" => "Вт",
 "Wednesday" => "Среда",
 "Wed" => "Ср",
 "Thursday" => "Четверг",
 "Thu" => "Чт",
 "Friday" => "Пятница",
 "Fri" => "Пт",
 "Saturday" => "Суббота",
 "Sat" => "Сб",
 "Sunday" => "Воскресенье",
 "Sun" => "Вс",
 "January" => "Января",
 "Jan" => "Янв",
 "February" => "Февраля",
 "Feb" => "Фев",
 "March" => "Марта",
 "Mar" => "Мар",
 "April" => "Апреля",
 "Apr" => "Апр",
 "May" => "Мая",
 "May" => "Мая",
 "June" => "Июня",
 "Jun" => "Июн",
 "July" => "Июля",
 "Jul" => "Июл",
 "August" => "Августа",
 "Aug" => "Авг",
 "September" => "Сентября",
 "Sep" => "Сен",
 "October" => "Октября",
 "Oct" => "Окт",
 "November" => "Ноября",
 "Nov" => "Ноя",
 "December" => "Декабря",
 "Dec" => "Дек",
 "st" => "ое",
 "nd" => "ое",
 "rd" => "е",
 "th" => "ое"
 );
 // если передали дату, то переводим ее
 if (func_num_args() > 1) {
 $timestamp = func_get_arg(1);
 return strtr(date(func_get_arg(0), $timestamp), $translate);
 } else {
// иначе текущую дату
 return strtr(date(func_get_arg(0)), $translate);
 }
 }
//класс тура
  class turi
  {
    function grey_box($dbee1, $turnir, $page_name, $paths, $sez)
	{
	   $recordturs = $dbee1->Execute("select * from v9ky_match where turnir='".$turnir."' and canseled<3 group by tur ORDER BY tur DESC");
	   $i=0;
	   if ($page_name=="video"){
	       while (!$recordturs->EOF) {
	         $recordphoto = $dbee1->Execute("select videothumb from v9ky_match where id='".$recordturs->fields[id]."' ");
	         $matchdate = rus_date("l j F Y ", strtotime($recordturs->fields[date]));
	         $i = $i+1;
             echo'<div class="grey-box';
		     if ($i==3) {echo " last"; $i=0;}
		     if (isset($recordphoto->fields[videothumb])&&($recordphoto->fields[videothumb]!=="")) $photo = $recordphoto->fields[videothumb]; else $photo = $paths."css/images/field.jpg";
             echo '">
					<h3><a href="/'.$sez.'/'.$page_name.'/tur/'.$recordturs->fields[tur].'">';
					if ($recordturs->fields[tur]=="13") echo 'Полуфинал</a></h3>';
		            else if ($recordturs->fields[tur]=="14") echo 'Финал</a></h3>';
		            else echo $recordturs->fields[tur].'-й тур</a></h3>';

                    echo '<a href="/'.$sez.'/'.$page_name.'/tur/'.$recordturs->fields[tur].'"><img src="'.$photo.'" alt="" height="130" width="205" /></a>
					 <p><br>
						<span>'.$matchdate.'</span>
						<a href="/'.$sez.'/'.$page_name.'/tur/'.$recordturs->fields[tur].'" class="button">Открыть</a>
					 </p>
				</div>';
		     $recordturs->MoveNext();
          }
	   }else{
	      while (!$recordturs->EOF) {
		     $recordphoto = $dbee1->Execute("select thumb from v9ky_photo where matc='".$recordturs->fields[id]."' ORDER BY date LIMIT 0,1");

      $dir = "2v_turnir/photo/".$recordturs->fields[id];
      $files = scandir($dir); 
      
      if ($files !== false){
           if (($files[2] != ".") && ($files[2] != "..")&&($files[2] !="tmp")) { 
             $strthumb = "http://v9ky.in.ua/2v_turnir/photo/".$recordturs->fields[id]."/tmp/".$files[2];
      }}else{$strthumb="";}
			 

			 $matchdate = rus_date("l j F Y ", strtotime($recordturs->fields[date]));
	         $i = $i+1;
             echo'<div class="grey-box';
		     if ($i==3) {echo " last"; $i=0;}
		     if ($strthumb!=="") $photo = $strthumb; else $photo = $paths."css/images/field.jpg";
             echo '">
					<h3><a href="/'.$sez.'/'.$page_name.'/tur/'.$recordturs->fields[tur].'">';
					echo $recordturs->fields[tur].'-й тур</a></h3>';
					echo'<a href="/'.$sez.'/'.$page_name.'/tur/'.$recordturs->fields[tur].'"><img src="'.$photo.'" alt="" height="130" width="205" /></a>
					 <p><br>
						<span>'.$matchdate.'</span>
						<a href="/'.$sez.'/'.$page_name.'/tur/'.$recordturs->fields[tur].'" class="button">Открыть</a>
					 </p>
				</div>';
		     $recordturs->MoveNext();
          }
		}
	}

	function grey_box_new($dbee1, $turnir, $page_name, $paths, $tur)
	{
	   $recordturs = $dbee1->Execute("select * from v9ky_match where turnir='".$turnir."' and canseled<3 and tur='".$tur."' ORDER BY date DESC");
	   $i=0;
	   if ($page_name=="video"){
	       while (!$recordturs->EOF) {
	         $recordphoto = $dbee1->Execute("select videothumb from v9ky_match where id='".$recordturs->fields[id]."' ");
	         $matchdate = rus_date("l j F Y ", strtotime($recordturs->fields[date]));
	         $i = $i+1;
             echo'<div class="col-sm-6 col-md-4"><div class="thumbnail">';
		     if ($i==3) {$i=0;}
		     if (isset($recordphoto->fields[videothumb])&&($recordphoto->fields[videothumb]!=="")) $photo = $recordphoto->fields[videothumb]; else $photo = $paths."css/images/field.jpg";
             echo '">
					<h3><a href="'.$page_name.'/tur/'.$recordturs->fields[tur].'">';
					echo $recordturs->fields[tur].'-й тур</a></h3>';

                    echo '<a href="'.$page_name.'/tur/'.$recordturs->fields[tur].'"><img src="'.$photo.'" alt="" height="130" width="205" /></a>
					 <div class="caption">
						<span>'.$matchdate.'</span>
						<a href="'.$page_name.'/tur/'.$recordturs->fields[tur].'" class="button">Открыть</a>
					 </div>
				</div></div>';
		     $recordturs->MoveNext();
          }
	   }else{
		  echo'<div class="row">';
	      while (!$recordturs->EOF) {
		     $recordphoto = $dbee1->Execute("select thumb from v9ky_photo where matc='".$recordturs->fields[id]."' ORDER BY date LIMIT 0,1");
			 $rec_team1 = $dbee1->Execute("select name from v9ky_team where id='".$recordturs->fields[team1]."'");
			 $rec_team2 = $dbee1->Execute("select name from v9ky_team where id='".$recordturs->fields[team2]."'");
             $matchdate = rus_date("l j F Y H:i ", strtotime($recordturs->fields[date]));
             
      $dir = "2v_turnir/photo/".$recordturs->fields[id];
      $files = scandir($dir); 
      
      if ($files !== false){
           if (($files[2] != ".") && ($files[2] != "..")&&($files[2] !="tmp")) { 
             $strthumb = "http://v9ky.in.ua/2v_turnir/photo/".$recordturs->fields[id]."/tmp/".$files[2];
      }}else{$strthumb="";}

             $omatch = new match();
			 $goly1 = $omatch->Gol1($dbee1, $recordturs->fields[id]);
			 $goly2 = $omatch->Gol2($dbee1, $recordturs->fields[id]);
	         $i = $i+1;
             echo'<div class="col-sm-6 col-md-4"><div class="thumbnail">';
             
             if ($rec_team1->fields[name]=='') {$teamname1='Команда 1';} else {$teamname1=stripcslashes($rec_team1->fields[name]);}//для кубков
             if ($rec_team2->fields[name]=='') {$teamname2='Команда 2';} else {$teamname2=stripcslashes($rec_team2->fields[name]);}
             //if ($recordSet2->fields[pict]=='') {$teampict1='7YgWJplYjJei.png';} else {$teampict1=stripcslashes($recordSet2->fields[pict]);}
             //if ($recordSet3->fields[pict]=='') {$teampict2='7YgWJplYjJei.png';} else {$teampict2=stripcslashes($recordSet3->fields[pict]);}     

	     if ($strthumb!=="") $photo = $strthumb; else $photo = $paths."css/images/field.jpg";
			  echo '<a href="'.$page_name.'/match/'.$recordturs->fields[id].'">'.$teamname1.' '.$goly1.':'.$goly2.' '.$teamname2.'</a>';
					echo'<a href="'.$page_name.'/match/'.$recordturs->fields[id].'"><img src="'.$photo.'" alt="" height="130" width="205" /></a>
					 <div class="caption">
						<span>'.$matchdate.'</span>

					 </div>
				</div></div>';
			  if ($i==3) { echo'</div><div class="row">'; $i=0;}
		     $recordturs->MoveNext();
          }
		   echo'</div>';
		}
	}
  }
//класс тура
//класс матча
  class match
  {

	function Gol1($dbee1, $fmatch)
	  {
	    $recSet = $dbee1->Execute("select * from v9ky_match where id='".$fmatch."'");
        $recgol1 = $dbee1->Execute("select count(*) from v9ky_gol where matc='".$fmatch."' and team='".$recSet->fields[team1]."'");
		//если матч не закончен то на голах пробелы
		if (($recSet->fields[canseled]==1)or($recSet->fields[canseled]==2)) {
		    $goly1 = $recgol1->fields[0];
		}else {
			$goly1 = "-";
		}
       return $goly1;
	  }
	function Gol2($dbee1, $fmatch)
	  {
	    $recSet = $dbee1->Execute("select * from v9ky_match where id='".$fmatch."'");
		$recgol2 = $dbee1->Execute("select count(*) from v9ky_gol where matc='".$fmatch."' and team='".$recSet->fields[team2]."'");
		//если матч не закончен то на голах пробелы
		if (($recSet->fields[canseled]==1)or($recSet->fields[canseled]==2)) {
			$goly2 = $recgol2->fields[0];
		}else {
			$goly2 = "-";
		}
       return $goly2;
	  }

  }
//класс матча

?>