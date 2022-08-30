<?php

use app\modules\admin\models\Buh;
use app\modules\admin\models\Login;
use app\modules\admin\models\Podr;
use app\modules\admin\models\Problem;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\widgets\DatePicker;
use yii\widgets\Pjax;
/**
 * @var  $model  \app\modules\admin\models\Problem;
 * @var  $parent  \app\modules\admin\models\Problem;
 * @var  $temp  \app\modules\admin\models\AppTemp;
 */

$podr = Podr::getList();
$buh = Buh::getBuh();
$loginSap = Login::getLoginSap();

$a_podr = ArrayHelper::map($podr, 'id', 'name');
$a_buh = ArrayHelper::map($buh, 'id', 'name');
$a_user = ArrayHelper::map($loginSap, 'id', 'username');

$t = ArrayHelper::map($temp, 'id_temp', 'type', 'id_problem');

$_podr = ArrayHelper::map(Podr::getList(), 'id', 'name');

$problem = ArrayHelper::map($list, 'id', 'name');
ksort($problem);
//echo "<pre>"; print_r($problem); die();
//echo "<pre>"; print_r($t); die();

$parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;
?>


<div class="col-12 mb-2">
    <?php foreach($parent as $item): ?>
        <a href="<?= Url::toRoute(['adm/service2', 'parent_id' => $item->id]) ?>" class="btn btn-outline-primary btn-sm "><?= $item->name ?></a>
    <?php endforeach; ?>
</div>


<div class="org-form col-12">
    <?php Pjax::begin([
        'id' =>'problem',
//        'size' => '15px',
        'enablePushState' => false
    ]); ?>
    <?php $form = ActiveForm::begin(['action' => ['/adm/service'],'options' => ['data-pjax' => true]]); ?>

    <div class="row" style=" font-size: 10pt">
        <div class="col-12 col-md-offset-2" >


            <table class="table table-border table-sm">
                <tr class="bg-info text-white">
                    <td>№</td>
                    <td>Наименование</td>
                    <td>БД</td>
                    <td>Подр</td>
                    <td>Исп.</td>
                    <td></td>
                </tr>
                <?php  foreach ($problem as $key_p => $item){ ?>
                    <tr>
                        <td><?= $key_p ?></td>
                        <td><?= $item ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Bd
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?php foreach ($buh as $items) { ?>
                                        <a href="<?= Url::to(['adm/service2', 'id_problem' => $key_p, 'id_temp' => $items->id, 'type' => 'db', 'parent_id' => $parent_id ]) ?>" class="dropdown-item fs-8"><?= $items->name ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Podr
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?php foreach ($podr as $items) { ?>
                                        <a href="<?= Url::to(['adm/service2', 'id_problem' => $key_p, 'id_temp' => $items->id, 'type' => 'podr', 'parent_id' => $parent_id ]) ?>" class="dropdown-item fs-8"><?= $items->name ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    User
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?php foreach ($loginSap as $items) { ?>
                                        <a href="<?= Url::to(['adm/service2', 'id_problem' => $key_p, 'id_temp' => $items->id, 'type' => 'user', 'parent_id' => $parent_id ]) ?>" class="dropdown-item fs-8"><?= $items->username ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            if (array_key_exists($key_p, $t)) {
//                                echo "<pre>"; print_r($t[$key_p]);
                                $array = $t[$key_p];
                                $_buh = array_keys($array, 1);
                                $_podr = array_keys($array, 2);
                                $_user = array_keys($array, 3);
//                                 print_r($_bd); echo '<br>';
//                                 print_r($_podr); echo '<br>';
//                                 print_r($_user); echo '<br>';

                                foreach ($_buh as $key => $__buh) {
                                    echo "<a href= ".Url::to(['adm/service2', 'delete' => $__buh, 'id_problem' => $key_p, 'type' => 'db', 'parent_id' => $parent_id ])." class='badge badge-warning ml-1'>".$a_buh[$__buh]."</a>";
                                }
                                echo "<br>";
                                foreach ($_podr as $key => $__podr) {
                                    echo "<a href= ".Url::to(['adm/service2', 'delete' => $__podr, 'id_problem' => $key_p, 'type' => 'podr', 'parent_id' => $parent_id ])." class='badge badge-primary ml-1'>".$a_podr[$__podr]."</a>";
                                }
                                echo "<br>";

                                foreach ($_user as $key => $__user) {
                                    echo "<a href= ".Url::to(['adm/service2', 'delete' => $__user, 'id_problem' => $key_p, 'type' => 'user', 'parent_id' => $parent_id ])." class='badge badge-info ml-1'>".$a_user[$__user]."</a>";
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <?php ActiveForm::end(); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>


</div>


<?php

$js = <<<JS
    $('#problem-btn').unbind('click')
JS;
$this->registerJs($js)
?>
