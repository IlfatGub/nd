








<?php
$apiData = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/search-depart?name=".urlencode("Отдел безопасности, информационных технологий")));


?>


<?php
function depart($item){
    echo "  <tr class=\"text-danger\">
                <td colspan=\"3\">$item->title</td>
            </tr>";
    phone($item->code);
}
?>

<?php
function phone($code){
     $phones = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/phone-for-depart?code=".$code));
     foreach($phones->data as $phone){
        echo "<tr class=\"text-muted\">
            <td>$phone->fio</td>
            <td style='min-width: 50px'>$phone->phone</td>
            <td>$phone->ext</td>
        </tr>";
     }
}
?>



<?php  if($apiData->status){ ?>
<div class="">
    <table class="table table-sm table-bordered table-hover" style="font-size: 8pt">
        <?php foreach($apiData->data as $item){ ?>
            <?php depart($item) ?>
            <?php if($item->children): ?>
                <?php foreach($item->children as $child){ ?>
                    <?php depart($child) ?>
                <?php  }  ?>
            <?php endif; ?>
        <?php  }  ?>
    </table>
</div>
<?php } ?>




<!--<div class="col-12">-->
<!--    <table class="table table-sm table-bordered table-hover" style="font-size: 8pt">-->
<!--        <tr class="text-muted">-->
<!--            <td>Кулябин Алексей</td>-->
<!--            <td>42-10, 37-92-22</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Капошко Ольга</td>-->
<!--            <td>16-01, 37-92-29</td>-->
<!--        </tr>-->
<!---->
<!--        <tr class="text-danger">-->
<!--            <td colspan="2">СИТ</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Губайдуллин Ильшат</td>-->
<!--            <td>30-03, 37-92-00</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Воронов Константин</td>-->
<!--            <td>39-12-32</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Минаев Сергей</td>-->
<!--            <td>36-06</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Прокофьев Дмитрий</td>-->
<!--            <td>16-05</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Губайдуллин Ильфат</td>-->
<!--            <td>36-99</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Тарасов Сергей</td>-->
<!--            <td>16-10</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td></td>-->
<!--            <td>16-09</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Фатхулов Денис Марсович</td>-->
<!--            <td>16-80</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Муратов Айдар</td>-->
<!--            <td>16-15</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Юсупов Рустам</td>-->
<!--            <td>17-58</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Поляков Александр Юрьевич</td>-->
<!--            <td>16-13</td>-->
<!--        </tr>-->
<!---->
<!--        <tr class="text-danger">-->
<!--            <td colspan="2">Сектор Связи</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Волгин Дмитрий</td>-->
<!--            <td>11-00, 37-91-71</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Растимешина Дарья Сергеевна</td>-->
<!--            <td> - </td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Баранов Григорий</td>-->
<!--            <td>36-90</td>-->
<!--        </tr>-->
<!---->
<!--        <tr class="text-danger">-->
<!--            <td colspan="2">СИРИАС</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Васильева Эльмира</td>-->
<!--            <td>36-11, 37-94-49</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Гайнуллина Юлия</td>-->
<!--            <td>36-09, 37-91-71</td>-->
<!--        </tr>-->
<!---->
<!--        <tr class="text-danger">-->
<!--            <td colspan="2">Сектор по КИС</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Холодов Андрей</td>-->
<!--            <td>36-91, 37-91-41</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Сисин Иван</td>-->
<!--            <td>16-19</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Ахметшина Альбина</td>-->
<!--            <td>16-20</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Давлетбаева Татьяна</td>-->
<!--            <td>16-38</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Гусарова Татьяна</td>-->
<!--            <td>16-57</td>-->
<!--        </tr>-->
<!--        <tr class="text-danger">-->
<!--            <td colspan="2">Заправщики</td>-->
<!--        </tr>-->
<!--        <tr class="text-muted">-->
<!--            <td>Заправщики</td>-->
<!--            <td>16-31</td>-->
<!--        </tr>-->
<!--    </table>-->
<!--</div>-->
