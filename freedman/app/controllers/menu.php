<? 
  // //название турнира из символьного названия
  // $rec_ru = $db->Execute("select * from v9ky_turnir where name='".$tournament."'");
  // if ($rec_ru->fields['ru']) {$turnir_ru = $rec_ru->fields['ru'];} else $turnir_ru = '';
  // if ($rec_ru->fields['id']) {$turnir = $rec_ru->fields['id'];} else $turnir = 0;

  if (file_exists("reglamenty/".$gorod_en['name_en'].".pdf")) {
          $reglamfile = $gorod_en['name_en'].".pdf";
        } else {
          $reglamfile = "Ukraine.pdf";
        }
require_once VIEWS . '/menu.tpl.php';
