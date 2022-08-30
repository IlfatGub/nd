<?php

/**
 * Поиск по телефонным справочникам
 */


$style = '';
?>


<?php
//$apiData = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/index?search=".$search));

?>

<?php
$apiData = json_decode(file_get_contents("http://tel.nhrs.ru/index.php/api/index?search=".$search));
?>
<?php  if($apiData->status){ ?>
    <table class="table table-sm table-bordered col-12 fs-8" style="text-transform: capitalize">
        <tr>
            <td colspan="6" class="alert-primary">Телефонный справочник Консалт-Аудит</td>
        </tr>
        <?php foreach($apiData->data as $item){            ?>
            <tr style="<?=$style?>">
                <td style="text-transform: lowercase"><?=$item->podr?> </td>
                <td><a href="http://tel.nhrs.ru/index.php/site/search?search=<?=$item->fio?>" target="_blank"><?=$item->fio?></a> </td>
                <td><?=$item->dolzhnost?></td>
                <td style="text-transform: lowercase"><?=$item->depart?></td>
                <td><?=$item->in?></td>
                <td><?=$item->out?></td>
            </tr>
        <?php  }  ?>
    </table>
<?php } ?>






