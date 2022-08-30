<?php
use app\modules\admin\models\AppFiles;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Html;

?>

<?php Pjax::begin(['enablePushState' => false, 'timeout' => 5000 ]); ?>
<?php if (count($document) > 0) : ?>
    <?php if($type): ?>
        <?php foreach ($document as $item) { ?>
                    <?= ++$i; ?>.
                    <?php $r = (explode('/', $item->path));  ?>
                    <?php $s = (explode('_', array_pop($r)));  ?>
                    <?= Html::a(array_shift($s), [AppFiles::DEFAULT_PATH . '/' . $item->path], ['data-pjax'=> 0, 'target' => '_blank']) ?>
        <?php } ?>
    <?php else: ?>

        <?php $i = 0; ?>
        <div style="font-size: 8pt" class="mb-2">
            <a href="#">
                <abbr class="btn btn-sm btn-link input fioCaseButton" role="alert" type="button"
                      data-toggle="collapse"
                      data-target="#collapseExamples" aria-expanded="false" aria-controls="collapseExample"> Прикрепленные
                    документы : <?= count($document) ?>
                </abbr>
            </a>
        </div>
        <div class="collapse" id="collapseExamples">
            <div class="card card-block px-2 py-2 mb-3 fioCase">
                <table class="fioCase" style="width: 100%">
                    <?php foreach ($document as $item) { ?>
                        <tr>
                            <td class="fioCaseLogin text-info">
                                <?= ++$i; ?>.
                                <?php $r = (explode('/', $item->path));  ?>
                                <?php $s = (explode('_', array_pop($r)));  ?>
                                <?= Html::a(array_shift($s), [AppFiles::DEFAULT_PATH . '/' . $item->path], ['data-pjax'=> 0, 'target' => '_blank']) ?>
                                <?php if (Yii::$app->user->can('Admin') or Yii::$app->user->can('Disp')) { ?>
                                    <?= Html::a("X", ['/site/document', 'document_del' => $item->id, 'id_app' => $item->id_app], ['class' => 'float-right text-danger']) ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

    <?php endif; ?>

<?php endif; ?>

<?php
if ($open == 1) {
    $this->registerJs(<<<JS
$('.collapse').addClass('show')
JS
    );
}
?>
<?php Pjax::end(); ?>
