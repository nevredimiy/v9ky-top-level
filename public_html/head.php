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

  <!-- ////////// Note 28.11.2024 ////////// -->
<?php if (!isset($_GET['foo'])): ?>
  
  
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
    
<?php else : ?>
      
<link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&family=Roboto+Condensed:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/css/style.css" />



<?php endif ?>



<!-- //////// Note 28.11.2024 /////// -->
<?php if (!isset($_GET['foo'])): ?>

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

<?php endif ?>

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

<?php if(!isset($_GET['foo'])): ?>
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

  <?php else: ?>

  <header class="header">
    <div class="container">
      <nav class="nav">
        <div class="nav__block">
          <div class="nav__item nav__title-block">
            <a href="<?=$site_url?>"><strong>V9KY.INUA</strong></a>
            &nbsp;|&nbsp;&nbsp;<?=$gorod_en->fields[name_en]?>&nbsp;&nbsp;|&nbsp;&nbsp;<?=$season_name->fields[season]?>
          </div>
  
          <div class="nav__item">
            <div>
              <a class="button-nav button--choose-city" href="<?=$url;?>/addmyteam/">
                <img
                  src="/css/components/header/assets/images/ukraine-logo.svg"
                  alt="ukraine-logo"
                >
                <span>Обери місто</span>
              </a>
            </div>
          </div>
        </div>
  
        <div class="nav__block">
          <a class="button-nav button-with-arrow button--order-team" href="<?=$site_url?>">
            <p class="button-with-arrow__title">Заявити команду</p>
            <p class="button-with-arrow__description">на найближчі турніри</p>
            <img
              src="/css/components/header/assets/images/arrow.svg"
              alt="arrow" 
              class="button-with-arrow__icon"
            >
          </a>
  
          <a class="button-nav button--v9ku-logo" href="<?=$site_url?>">
            <img
              src="/css/components/header/assets/images/v9ku-logo.png" 
              alt="v9ku-logo"
              class="button--img-v9ku-logo"
            >
          </a>
  
          <a class="button-nav button-with-arrow button--find-team" href="<?=$url;?>/addme1/">
            <p class="button-with-arrow__title">Подати заявку</p>
            <p class="button-with-arrow__description">на пошук команди</p>
            <img
              src="/css/components/header/assets/images/arrow.svg"
              alt="arrow"
              class="button-with-arrow__icon"
            >
          </a>
        </div>
  
        <div class="nav__block">
          <div class="nav__item">
            <div>
              <a class="button-nav button--choose-city" href="#">
                <img
                  src="/css/components/header/assets/images/ukraine-logo.svg"
                  alt="ukraine-logo"
                >
                <span>Обери місто</span>
              </a>
            </div>
          </div>
  
          <div class="nav__item">
            <a class="button-nav button--call-to" href="tel:+380934319492">
              <img src="/css/components/header/assets/images/phone-icon.svg" alt="phone-icon">
              <span>(093)431-94-92</span>
            </a>
          </div>
  
          <div class="nav__item nav__item--social-link">
            <a class="button-nav button--social-link" href="https://www.facebook.com/v9ky.ukraine/" target="_blank">
              <img src="/css/components/header/assets/images/facebook-icon.svg" alt="facebook-icon">
            </a>
  
            <a class="button-nav button--social-link" href="https://www.youtube.com/c/V9kyInUa?sub_confirmation=1" target="_blank">
              <img src="/css/components/header/assets/images/youtube-icon.svg" alt="youtube-icon">
            </a>
  
            <a class="button-nav button--social-link" href="https://www.instagram.com/v9ky.ukraine/?igshid=YmMyMTA2M2Y%3D" target="_blank">
              <img src="/css/components/header/assets/images/instagram-icon.svg" alt="instagram-icon">
            </a>
          </div>  
        </div>
      </nav>
    </div>
  </header>
  <main class="main"> 
  <?php endif ?>
