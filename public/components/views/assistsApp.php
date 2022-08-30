

<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Sitdesk;
?>


<?php  foreach ($model as $item) { ?>
    <?php $user = $item->user->username ?>
    <?php $style = $item->id_priority == 3 ? 'background: #F2DEDE; ' : 'background: white; '; ?>
    <?php $style .= $item->review == 1 ? ' border-left: 5px solid #337AB7 ' : ' border-left: 5px solid #B1D6F0'; ?>
    <div style="display: inline-block " data-toggle="tooltip" data-placement="left" title="<?= $item->appContent->content ?>">
        <?= Html::a(
            '№'. $item->id,
            ['/index', 'id'=>$item->id, '#'=> 'hs_'.$item->id],
            [
                'class' => $item->type == 1 ?  'btn btn-sm btn-outline-success' :  'btn btn-sm btn-outline-primary',
                'style'=> 'margin: 4px 2px; text-align: left; '
            ]
        ) ?>
    </div>
<?php } ?>

