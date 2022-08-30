<?php
use app\models\Sitdesk;
?>


<style>
    .ldap-member, .ldap-post{
        display: none;
    }
</style>

<?php

$js = <<< JS
    $("#cb1").trigger("click");
JS;

$ldapClick= <<< JS
    $('#cb1').click(function () {
        $('.ldap-member').fadeToggle(200)();
    });
JS;

$ldapClickPost= <<< JS
    $('#cb2').click(function () {
        $('.ldap-post').fadeToggle(200)();
    });
JS;


?>
<div class="card-columns mb-3">
    <?php     $t = 1;


        $loginList = '';
        $loginList2 = '';

        ?>

    <?php foreach($model as $item): ?>
        <?php
        if($item != 1){

            $mail = isset($item['mail'][0]) ? $item['mail'][0] : null;

//            echo "<pre>"; print_r($item); die();
            $class='';
        $depart = Sitdesk::getDepartName($item['dn']);  // Получаем читабельный вид Отдела
        $login = isset($item['samaccountname'][0]) ? $item['samaccountname'][0] : null; //логин пользователя
        $mail_login = isset($item['mail'][0]) ? $item['mail'][0] : $item['samaccountname'][0];
        $passwords = date('dmY') . preg_replace('/[0-9]+/', '', $login);
        if ($mail){
            switch (Sitdesk::getDomain($mail)){
                case 'snhrs':
                    $class = 'alert-dark';            break;
                case 'zsmik':
                    $class = 'alert-primary';            break;
                case 'nhrs':
                    $class = 'alert-warning';            break;
                case 'a-consalt':
                    $class = 'alert-danger';            break;
            }
        }

        ?>


        <?php if(isset($item['samaccountname'][0])): ?>
                <div class="card border-dark mb-0">
                    <table class="table table-condensed table-hover table-sm fs-12 mb-0" id="ldap-info">
                        <tr class="<?= $class ?>">
                            <td>
                                <?= $item['cn'][0] ." / ". $mail  ." / ".  $item['samaccountname'][0] ?>
                            </td>
                        </tr>
                        <tr class="ldap-post">
                            <td>
                                <?php
                                $apiData = json_decode(file_get_contents("http://tel.snhrs.ru/index.php/api/index?search=".$item['cn'][0]."&fullfio=1"));
                                $apiDataConsalt = json_decode(file_get_contents("http://phone.a-consalt.ru/index.php/api/index?search=".$item['cn'][0]."&fullfio=1"));
                                if ($apiData->status){
                                    foreach ($apiData->data as $datum) {
                                        echo "<span class=\"badge badge-info\">".trim($datum->dolzhnost)."</span>";
                                        echo "<span class=\"badge badge-success\">".trim($datum->depart)."</span>";
                                    }
                                }elseif($apiDataConsalt->status){
                                    foreach ($apiDataConsalt->data as $datum) {
                                        echo "<span class=\"badge badge-info\">".trim($datum->dolzhnost)."</span>";
                                        echo "<span class=\"badge badge-success\">".trim($datum->depart)."</span>";
                                    }
                                } else{
                                    $phone = null;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php if (isset($item['memberof'])) { ?>
                            <tr class="fs-10 ldap-member">
                                <td colspan="2">
                                    <?php asort($item['memberof']) ?>
                                    <?php for ($i = 0; $i <= $item['memberof']['count'] - 1; $i++) { ?>
                                        <?php $r = explode(',', $item['memberof'][$i]) ?>
                                        <?= str_replace(["CN=", "CN"], "", array_shift($r)) . ';' ?>
                                        <br>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
            </div>

        <?php


            // спиок ФИО.Логин.Должность
            $loginList .= "<tr><td>".$t++."</td><td>".$item['cn'][0]."</td><td>".$item['title'][0]."</td><td>".$mail_login."</td><td>".$passwords."</td></tr>";
            // спиок ФИО.Логин.Должность
            $loginList2 .= "<div class=\"card p-1 table table-bordered\">
                                <div>ФИО: ".$item['cn'][0]."</div>
                                <div>Логин: ".$mail_login."</div>
                                <div>Пароль: ".$passwords."</div>
                            </div>";
        ?>
        <?php endif; ?>
    <?php } ?>
    <?php endforeach; ?>
</div>

<!--            Список пользователй      -->
<div class="card-columns mb-3">
 s  <?= $loginList2 ?>
</div>
<table class="table table-bordered table-sm w-75" >
    <?= $loginList ?>
</table>

<?php $this->registerJs( $ldapClick ); ?>
<?php $this->registerJs( $ldapClickPost ); ?>
<?php if ($model['count'] == 1){$this->registerJs( $js ); }?>
<!--            Список пользователй         -->

