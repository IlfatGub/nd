
<?php

use app\components\ldapUserInfo;
use app\models\Sitdesk;
use app\modules\admin\models\FioCase;

?>


<!--        Вывод найденных ФИО  -->

<?php ;
$i = 0 ?>
<?php $count = count($model); ?>
<?php if ($count > 0) : ?>
    <?php $fio = array() ?>
    <?php foreach ($model as $item) {
        $fio[] = FioCase::getName($item);?>
    <?php } ?>
<?php sort($fio); $fio=array_unique($fio); $count = count($fio)?>


    <div class="collapse" id="collapseExample">
        <div class="inline-block float-right">
            <a href="#">
            <span title="Закрыть" class="text-danger fas fa-window-close " role="alert" type="button" data-toggle="collapse"
                  data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            </span>
            </a>
        </div>
        <label><input class="mx-2" type='checkbox' checked id='fioCaseLogin'/>Login</label>
        <label><input class="mx-2" type='checkbox' checked id='fioCaseName'/>ФИО</label>
        <label><input class="mx-2" type='checkbox' checked id='fioCaseIp'/>Ip</label>

        <div class="card card-block px-2 py-2 mb-3 fioCase">
            <small class='text-danger mb-2'>Возможны неточности! Данные сформированы по последнему входу в систему. </small>
            <small class='text-success mb-2'>При двойном клике, строка исчезнет</small>
            <table class="fioCase">
            <?php foreach ($fio as $item) {
                $domains = FioCase::getDomains($item); ?>
                <?php if(!$type): ?>
                    <tr class="check_hide">
                        <td class="fioCaseLogin inline-block text-info"> <?= isset($domains) ? FioCase::replaceDomain($domains->login) : "<span class='text-danger'> Нет данных </span>" ?> </td>
                        <td class="fioCaseIp inline-block text-primary"> <?= isset($domains) ? $domains->ip : "<span class='text-danger'> Нет данных </span>" ?> </td>
                        <td class="text-dark fioCaseName inline-block"> <?=$item?> </td>
                    </tr>
                <?php endif; ?>

                <!--  Выводим группы для пользователей-->
                <?php if($type){
                    echo "<tr><td>";

                    $d = explode('\\', $domains->login);
                    $config = Sitdesk::domainConfig(Sitdesk::getDomainIdByEng(array_shift($d)));   //Получаем идентификатор домена, по логину



                    $res = Sitdesk::ldap($config[0], [1 => $item] ,$config[1],$config[2],$config[3]); //Результат из ActiveDirectory

                    $replace2 = array("CN=", "CN");

//                    echo "<pre>";
//                    print_r($res[0]); die();
                        echo "<h5 class='m-0 p-0'><span class='badge badge-info'>" . $res[0]['cn'][0] . '. ' . $res[0]['mail'][0] . "</span></h5>";
                        foreach ($res[0]['memberof'] as $member) {
                            $explod = explode(',',$member);
                            $t = str_replace($replace2, "", $explod[0]);
                            if(strlen($t) > 3){
                                echo $t."<br>";
                            }
                        }
                        echo "<br>";

//                    echo ldapUserInfo::widget(['model' => $res[0]]);
                    echo "</td></tr>";
                } ?>
                <!-- Выводим группы для пользователей-->
            <?php } ?>
            </table>

        </div>
    </div>
<?php else: ?>
    <?php echo "<small class='text-danger mb-2'>Ничего не нашлось</small>" ?>
<?php endif ?>
<!--         Вывод найденных ФИО  -->


<script>
    $(function(){
        $("#fioCaseLogin").change(function(){
            $('.fioCaseLogin').fadeToggle(100);
        });
        $(".check_hide").dblclick(function(){
            $(this).hide(100);
        });
        $("#fioCaseName").change(function(){
            $('.fioCaseName').fadeToggle(100);
        });
        $("#fioCaseIp").change(function(){
            $('.fioCaseIp').fadeToggle(100);
        });
    });
</script>


