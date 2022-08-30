
<?php

$count = explode('-', $search);
if(count($count) > 2){
    $search = str_replace('-', ' ', $search);
    $search = str_replace(' ', '_', $search);
}else{
    $search = $count[0];
}



$apiData = json_decode(
	file_get_contents("http://logs.snhrs.ru/index.php/api/index?search=".urlencode($search).'&limit='.urlencode($lim))
);



$i = 0;




$text = "<table class='search-history-table'>"
?>
<?php
//
//echo "<pre>";
//print_r($apiData->data ); die();
//?>
<?php  if($apiData->status){ ?>
    <table class="table table-sm table-bordered" style="font-size: 9pt">
        <?php
        foreach($apiData->data as $item){
            switch (explode("\\", $item->login)[0]) {
                case 'ZSMIK':
                    $style = 'background: #D9EDF7 !important';
                    break;
                case 'NHRS':
                    $style = 'background: #FCF8E3 !important';
                    break;
                case 'A-CONSALT':
                    $style = 'background: #F2DEDE !important';
                    break;
                case 'SNHRS':
                    $style = 'background: #FFFFFF !important';
                    break;
                default:
                    $style = '';
                    break;
            }
            ?>
            <tr style="<?=$style?>">
                <td><?=$item->datehost?> </td>
                <td><?=$item->host?></td>
                <td><?=$item->login?></td>
                <td><?=$item->name?></td>
                <td><?=$item->ip?></td>
                <td><?=$item->dolzhnost?></td>
                <td><?=$item->depart?></td>
                <td><?= !empty($item->phone) ? $item->phone : ''?></td>
            </tr>

            <?php if($i <= 5): ?>
<!--                --><?php //$text .= "<tr><td>$item->host</td><td>$item->login</td><td>$item->name</td><td>$item->ip</td><td>$item->mac</td><td>$item->dolzhnost</td></tr>" ?>
                <?php $text .= "<tr style=\"$style\">
                <td>$item->host</td>
                <td>$item->login</td>
                <td>$item->name</td>
                <td>$item->ip</td>
                <td>$item->mac</td>
            </tr>" ?>
            <?php endif; ?>
            <?php $i++; ?>
        <?php  }  ?>
    </table>

    <?php $text .= "</table>" ?>

    <?php
    \app\modules\admin\models\AppSearchHistory::record($search, $text, 'logs');
    ?>


<?php }else{  echo 'Нет Данных'; } ?>

