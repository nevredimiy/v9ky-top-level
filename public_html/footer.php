<? 
if (!defined('READFILE'))
  {exit('Wrong way to file');} 
?>

<!-- ////////// Note 29.11.2024 //////////// -->
<?php if( isset($_GET['foo'])): ?>
</main>
<?php endif ?>
<!-- /////////// END Note 29.11.2024 /////////// -->

<footer class="footer">
	<div class="foot-line box-widht">

    <?php if (!isset($_GET['foo'])): ?>

		<p id="scroll-up" class="scroll-up"><img src="/img/scroll-up.png" alt=""></p>
    
    <?php else : ?>
    
    <button id="scroll-up" class="footer__top-arrow" style="display: none;">
      <img src="/css/components/footer/assets/images/top-arrow.svg" alt="top-link">
    </button>

    <?php endif ?>

		<p class="copyright">V9KY.IN.UA since © 2014</p>
		<!--<a href="<?=$site_url?>/rules/">Правила футзалу</a>
                <a href="http://v9ky.in.ua/kiev/lyubitelskiy_futbol_kiev/">Блог</a>-->
		<p class="contact-num-ph">Контактний номер турніру (093)431-94-92</p>
                <a href="https://www.facebook.com/profile.php?id=100004986226128" target="_blank">Website by</a>
    
<?php

#------ файл end.php


$time = microtime(true) - $start;
printf("Страница сгенерирована за %f секунд",$time);


    $recordt["page"] = $module;
	$recordt["period"] = $time;

	//$db->AutoExecute('test1',$recordt,'INSERT');

?>
	</div>
</footer>



<script src="/js/html2canvas.min.js"></script>
  
<script src="/libs/jquery/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/libs/html5gallery/html5gallery.js"></script>
<script src="/libs/jquery-mousewheel/jquery.mousewheel.min.js"></script>
<script src="/libs/scrollto/jquery.scrollTo.min.js"></script>
<script src="/libs/owl-carousel/owl.carousel.js"></script>
<script src="/js/jquery00.js"></script>
<script src="/libs/slick/slick.min.js"></script>


<script src="/js/common.js"></script>

<script>
  $(document).ready(function() {
    // Обработчик клика по кнопке
    $('#scroll-up').click(function() {
      // Плавная прокрутка к верху страницы
      $('html, body').animate({
        scrollTop: 0
      }, 500);  // 500 - это время анимации в миллисекундах
    });
  });
</script>




<!-- Google analytics -->
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-55866339-2', 'auto');
  ga('send', 'pageview');

</script>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1900524799975761'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1900524799975761&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->

<!-- Global site tag (gtag.js) - Google Ads: 791660257 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-791660257"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-791660257');
</script>
<!-- Event snippet for Оставили заявку В9КУ Зима 2019 conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': 'AW-791660257/RiGCCOPclIgBEOGNv_kC',
      'transaction_id': ''
  });
</script>

</body>
</html>