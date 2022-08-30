<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->session['error'];
?>

<div class="col-lg-12" style="margin-top: 10px">
    <div class="alert alert-danger">
        <?php if(isset(Yii::$app->session['error'])) : ?>
            <?= $this->title  ?>
        <?php else: ?>
            Ошибка 404 <strong> <a href='<?=Url::home()?>'> Вернуться на главную </a> </strong>
        <?php endif ?>
    </div>
</div>
<?php Unset(Yii::$app->session['error']);?>
