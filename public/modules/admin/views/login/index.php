<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>



<div class="executor-index">
    <p> <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>   </p>

    <div class="bs-callout bs-callout-info" >
        <div style="display:inline-block;">
            <h4>Role - 101</h4>
            <p>Админские права. Права для SAP</p>
        </div>
        <div style="display:inline-block; margin: 0 0 0 40px"">
        <h4>Role - 102</h4>
        <p>Права для программиста 1С</p>
    </div>
</div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'login',
            'username',
            'role',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>
