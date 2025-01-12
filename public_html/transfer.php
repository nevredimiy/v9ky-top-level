<? 
  include_once "turnir_head.php";
?>
<div class="content">


    <div class="jeka_content">
        <table class="jeka_calendar">


            <?
$recordlog = $db->Execute("select * from v9ky_transfer_log where turnir='".$turnir."' ORDER BY date DESC");


while (!$recordlog->EOF) {?>




            <tr>
                <td class="jeka_bord"><?=$recordlog->fields[date]?></td>
                <td><?=$recordlog->fields[log]?></td>
            </tr>


            <?
   $recordlog->MoveNext();
}
?>






        </table>


    </div>
</div>
</article>
<?
  include_once "footer.php";
?>