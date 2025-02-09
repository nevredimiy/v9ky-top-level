  </main>
  <footer class="footer">
    <div class="foot-line box-widht">

      <button id="scroll-up" class="footer__top-arrow" style="display: none;">
        <img src="/css/components/footer/assets/images/top-arrow.svg" alt="top-link">
      </button>

      <p class="copyright">V9KY.IN.UA since © 2014</p>
      <p class="contact-num-ph">Контактний номер турніру (093)431-94-92</p>
                  <a href="https://www.facebook.com/profile.php?id=100004986226128" target="_blank">Website by</a>
      
        <?php
        $time = microtime(true) - $start;
        printf("Страница сгенерирована за %f секунд",$time);
        $recordt["page"] = $module;
        $recordt["period"] = $time;
        ?>
    </div>
  </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="<?= JS . '/variables.js' ?>"></script>
    <script src="<?= JS . '/script.js' ?>"></script>
    <script src="<?= JS . '/swiper-nav.js' ?>"></script>
    <script src="<?= JS . '/swiper-ratings.js' ?>"></script>
    <script src="<?= JS . '/swiper-disqualification.js' ?>"></script>
    <script src="<?= JS . '/swiper-leagues.js' ?>"></script>
    <script src="<?= JS . '/swiper-table.js' ?>"></script>
    <script src="<?= JS . '/swiper-matches.js' ?>"></script>
    <script src="<?= JS . '/swiper-controls.js' ?>"></script>
    <script src="<?= JS . '/swiper-month-controls.js' ?>"></script>
    <script src="/js/swiper-photo.js"></script>
    <script src="/css/components/video-content/video.script.js"></script>
    <script src="<?= JS . '/action-calendar.js' ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
      $(document).ready(function() {
        // Показать/скрыть кнопку в зависимости от прокрутки
        $(window).scroll(function() {
          if ($(this).scrollTop() > 300) {
            $('#scroll-up').show();
            $('#scroll-up').addClass('show');  // Показываем кнопку
          } else {
            $('#scroll-up').removeClass('show');  // Скрываем кнопку
            $('#scroll-up').hide();
          }
        });

        // Плавный скролл вверх при клике на кнопку
        $('#scroll-up').click(function() {
          $('html, body').animate({
            scrollTop: 0  // Прокрутка к верху страницы
          }, 500);  // Длительность анимации (в миллисекундах)
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