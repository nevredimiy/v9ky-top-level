<?php if (!defined('READFILE'))
  {exit('Wrong way to file');};
  $season_name = $db->Execute("select season from v9ky_turnir where name='".$tournament."'");
  $gorod_en = $db->Execute("select * from v9ky_city where id=(select city from v9ky_turnir where name='".$tournament."')");
?>


<html lang="ru">
<head>
	<script data-skip-moving="true">
        (function(w,d,u){
                var s=d.createElement('script');s.async=1;s.src=u+'?'+(Date.now()/60000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn.bitrix24.ua/b8857989/crm/site_button/loader_1_lnk7oo.js');
</script>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '346520639956675');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=346520639956675&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
	<title><?=$title?></title>

        <meta charset="utf-8" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=0.75" />

<?
 if (isset($params['news'])) {$news=1*intval($params['news']); 
  $recordSet = $db->Execute("select * from v9ky_news where id=".$news);
  
?>
		
<? if ($recordSet->fields[head1]!==""){?>				
                                <meta property="og:title" content="<?=$recordSet->fields[head1]?>" />
<?}else{?> <meta property="og:title" content="Новини турніру" /> <?}?>
                               
<? if ($recordSet->fields[pict1]!=="") {?>
                                <meta property="og:image" content="<?=$recordSet->fields[pict1]?>" />
<?}?>	
<? function limit_words($string, $word_limit) {
	$words=explode(" ",stripcslashes($string));
	return implode(" ",array_splice($words,0,$word_limit));
  }
  if ($recordSet->fields[text1]!=="") {?>
                                <meta property="og:description" content="<?=limit_words($recordSet->fields[text1], 30)?>" />
<?}?>

<meta property="og:url" content="http://<?=$_SERVER['SERVER_NAME']?><?=$_SERVER['REQUEST_URI']?>" />
<?}?>
 
	<link rel="stylesheet" href="/css/reset.css" />
	<link rel="stylesheet" href="/libs/bootstrap/bootstrap-grid-3.3.1.min.css" />
	<link rel="stylesheet" href="/libs/font-awesome-4.2.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="/libs/fancybox/jquery.fancybox.css" />
	<link rel="stylesheet" href="/libs/owl-carousel/owl.carousel.css" />
	<link rel="stylesheet" type="text/css" href="/libs/slick/slick.css" />
  <link rel="stylesheet" type="text/css" href="/libs/slick/slick-theme.css"/>
	<link rel="stylesheet" href="/libs/countdown/jquery.countdown.css" />
	<link rel="stylesheet" href="/css/fonts.css" />
	<link rel="stylesheet" href="/css/main.css" />
	<link rel="stylesheet" href="/css/media.css" />
  <link rel="stylesheet" href="/css/royalsli.css" />
  <link rel="stylesheet" href="/css/modal.css" />
  <? if($nenadacss!==1){ ?>
    <link rel="stylesheet" href="/css/new_style.css" />
    <?}?>
    <link rel="stylesheet" href="/css/glavnaya.css" />
    <link rel="stylesheet" href="/css/v9ky-cards.css" />
 




<style type="text/css">
.carousel1 {
    white-space: nowrap;
    overflow-x: auto;
    
    
    height: 720px;
    }
#scrollbar-custom::-webkit-scrollbar:horizontal{
		height:30px;
	}
.carousel1 a {
    inline-block;
    
    margin: 3px;
    }
pre {
    white-space: pre-wrap;
    text-align: justify;
    text-indent: 20px;
}

.jeka_content{
  width: 100%;
  align: "center";
}
.jeka_calendar{
  width: 100%;
  color: #d8d8d8;
  background-color: #015869;
  display: table; 
  border: 3px solid #050505;
}
.jeka_calendar td{
  align: center;
  border-bottom: 3px solid #050505;
}
.jeka_calendar th{
  color: #d8dd1b;
  background-color: #043e64;
  border: 3px solid #050505;
}
.jeka_yel{
  color: #d8dd1b;
  border-right: 3px solid #050505;
}
.jeka_bord{
  border-right: 3px solid #050505;
}
</style>


<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '748205805612998');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=748205805612998&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->



</head>

<body class="body">


<header>
	<div class="top-line-header">
		<div class="column-head-1">
			<a href="<?=$site_url?>"><img class="gif" src="/img/Animation.gif" alt=""><img class="statica" src="/img/ukr-n.png" alt="">обери місто</a>
		</div>
		<div class="column-head-2">
			<a class="v9ky" href="<?=$site_url?>">v9ky.in.ua | <?=$gorod_en->fields[name_en]?> | <span style="font-size: 30px; font-family: 'HeliosCondRegular';"><?=$season_name->fields[season]?></span></a>
			<a class="btn-head"
			style="line-height: 1;" href="<?=$url;?>/addmyteam/">заявити команду <br><span>на найближчі турніри</span>
				<img id="turn" src="/img/arrovs.png" class="arrovs">
			</a>
		</div>
		<div class="column-head-3">
			<a href="<?=$site_url?>"><img src="/img/v9ky_wbl.png" alt=""></a>
		</div>
		<div class="column-head-5">
			<a class="btn-head" href="<?=$url;?>/addme1/">подати заявку<br>на пошук команди
				<img src="/img/arrovs.png" class="arrovs">
			</a>
<!--			<a class="btn-head" href="<?=$url;?>/loyalty">програма лояльності
				<img src="/img/arrovs.png" class="arrovs">
			</a>-->
		</div>
		<div class="column-head-4">
                        <a href="https://www.instagram.com/v9ky.ukraine/?igshid=YmMyMTA2M2Y%3D" target="_blank"><img src="/img/instagram.png"></a>
			
			<a href="https://www.facebook.com/v9ky.ukraine/" target="_blank"><img src="/img/fb.png" alt=""></a>
			<a href="https://www.youtube.com/c/V9kyInUa?sub_confirmation=1" target="_blank"><img src="/img/yt.png" alt=""></a><center><font size="4" color="white"><br>(093)431-94-92</font></center>

		</div>
	</div>
  </header>

