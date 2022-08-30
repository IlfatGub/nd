

<?php
//// создание нового ресурса cURL
//$ch = curl_init();
//// установка URL и других необходимых параметров
//curl_setopt($ch, CURLOPT_URL, "http://phone.snhrs.ru/web/index.php/api/index?search=".$search);
////curl_setopt($ch, CURLOPT_HEADER, 0);
//// загрузка страницы и выдача её браузеру
//
//$str = curl_exec($ch);
//// завершение сеанса и освобождение ресурсов
//curl_close($ch);
//?>



<?php
$apiData = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/index?search=".$search));
?>
<?php  if($apiData->status){ ?>
    <table class="table table-sm table-bordered col-12" style="font-size: 9pt">
        <tr>
            <td colspan="6" class="alert-primary">Телефонный справочник СНХРС/ЗСМиК</td>
        </tr>
        <?php foreach($apiData->data as $item){            ?>
            <tr style="<?=$style?>">
                <td><?=$item->podr?> </td>
                <td><?=$item->fio?></td>
                <td><?=$item->dolzhnost?></td>
                <td><?=$item->depart?></td>
                <td><?=$item->in?></td>
                <td><?=$item->out?></td>
            </tr>
        <?php  }  ?>
    </table>
<?php } ?>


<?php
$apiData = json_decode(file_get_contents("http://phone.a-consalt.ru/index.php/api/index?search=".$search));
?>
<?php  if($apiData->status){ ?>
    <table class="table table-sm table-bordered col-12" style="font-size: 9pt">
        <tr>
            <td colspan="6" class="alert-primary">Телефонный справочник Консалт-Аудит</td>
        </tr>
        <?php foreach($apiData->data as $item){            ?>
            <tr style="<?=$style?>">
                <td><?=$item->podr?> </td>
                <td><?=$item->fio?></td>
                <td><?=$item->dolzhnost?></td>
                <td><?=$item->depart?></td>
                <td><?=$item->in?></td>
                <td><?=$item->out?></td>
            </tr>
        <?php  }  ?>
    </table>
<?php } ?>






