<? 
  //include_once "turnir_head.php";
  include_once "dates.php";
include_once "head.php";
include_once "slider_spons.php";
include_once "menu.php";
include_once "run_line.php";
?>
<?
require_once('ajax_forms/PHPLiveX.php');
$ajax = new PHPLiveX();
$ajax->Run();
?>
<script type="text/javascript">
function sendForm(form){
  return PLX.Submit(form, {
    "preloader":"pr",
	"target":"pr1"
  });
}
</script>

		<div class="content">
			<div class="content-contakts">
				<div class="num-phone-tur">
					<p>контакти чемпіонату: (093) 431-94-92</p>
				</div>
				<div class="foto_of_boss">
					<div class="block_vith_foto">
						<p><img src="/img/first_boss.png"></p>
						<p>мамедов максим</p>
						<p>головний організатор</p>
						<p>v9ky@ukr.net</p>
                                                <p>(093) 273-57-67</p>
					</div>
					<div class="block_vith_foto">
						<p><img src="/img/palamarchuk.jpg"></p>
						<p>Паламарчук Юрій</p>
						<p>Тім-менеджер</p>

                                                <p>(093) 431-94-92</p>
					</div>
                                        <div class="block_vith_foto">
						<p><img src="/img/Kraseha.jpg"></p>
						<p>Красьоха Юрій</p>
						<p>Адміністратор</p>
						<p>(098)427-71-85</p>
					</div>
					
                                 </div>
                                 <div class="foto_of_boss">
                                        <div class="block_vith_foto">
						<p><img src="/img/petrushov.jpg"></p>
						<p>Петрушов Віктор</p>
						<p>Шеф-редактор</p>
						<p>(063)597-72-02</p>
					</div>
					<div class="block_vith_foto">
						<p><img src="/img/skirda.jpg"></p>
						<p>Скирда Дмитро</p>
						<p>Адміністратор м. Харків</p>
						<p>(093)134-66-38</p>
					</div>
					<div class="block_vith_foto">
						<p><img src="/img/necheboy.png"></p>
						<p>Нечєбой Євген</p>
						<p>Адміністратор м. Одеса</p>
						<p>(063)039-51-03</p>
					</div>
				</div>
                                <div class="foto_of_boss">
                                        <div class="block_vith_foto">
						<p><img src="/img/sebalo_v.jpg"></p>
						<p>Себало Василь</p>
						<p>Адміністратор м. Львів</p>
						<p>(067)908-78-82</p>
					</div>
					<div class="block_vith_foto">
						<p><img src="/img/neroduk.png"></p>
						<p>Неродюк Леонід</p>
						<p>Адміністратор м. Кривий Ріг</p>
						<p>(096)679-72-04</p>
					</div>
					
				
                                        <div class="block_vith_foto">
						<p><img src="/img/belyaev.jpg"></p>
						<p>Беляєв Володимир</p>
						<p>Адміністратор м. Запоріжжя</p>
						<p>(050)453-17-33</p>
					</div>
					
				</div>
                                

<?php




if ((!empty($_POST))){

    if (isset($_POST['name'])) {$name=filter_string($_POST['name']);
      } ELSE {$name="";}
     if (isset($_POST['mail'])) {$mail=filter_string($_POST['mail']);
      } ELSE {$mail="";}
	if (isset($_POST['ps'])) {$ps=filter_string($_POST['ps']);
      } ELSE {$ps="";}


  }?>
				<div class="form">
					<p>зворотній зв’язок</p>

<form action="<?=$site_url?>/<?=$tournament?>/contacts/" method="POST" ENCTYPE='multipart/form-data'>

					<input type="text" name="name" placeholder="ІМ'Я">
					<input type="text" name="mail" placeholder="*E-MAIL">
					<textarea name="ps">повідомлення</textarea>
					<a href="" onClick="this.parentNode.submit(); return false;">НАДІСЛАТИ</a>
				</div>					
				
			</div>
		</div>
	</div>
</article>

<?
if (!empty($_POST)){
     if (stripos($ps, "http")===false){
	$message = "Ім'я  ".$name."\n";
        $message .="Email  ".$mail."\n";

	$message .="Повідомлення:\n".str_replace('\r\n',"\r\n", $ps)."\n";


	$headers  = "Content-type: text/plain; charset=utf-8 \n";
   
	mail('v9ky@ukr.net, v9ky.ukraine@gmail.com', 'Повідомлення з сайту v9ky', $message, $headers);
     }
}
?>
<?
  include_once "footer.php";
?>
<?
if (!empty($_POST)){
  echo"<script>window.onload = function() {alert('Повідомлення відправлено. Менеджер турніру відповість Вам найближчим часом.');}</script>";}
?>