<?php
use phpnt\yandexMap\YandexMaps;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>

<div class="col-11 " style="padding: 0 0 0 40px">
    <hr>
    <div>
        <?php $form = ActiveForm::begin([
            'id' => 'map-form',
        ]); ?>
        <?php $formClass = 'form-control ' ?>
        <div class="row">
            <div class="col-3">
                <?= $form->field($model, 'coordinates')->textInput(['placeholder' => '53.383913, 55.890709', 'class' => $formClass])->label(false); ?>
            </div>
            <div class="col-2">
                <?= $form->field($model, 'name')->textInput(['placeholder' => 'Наименование', 'class' => $formClass])->label(false); ?>
            </div>
            <div class="col-3">
                <?= $form->field($model, 'description')->textInput(['placeholder' => 'Описание', 'class' => $formClass])->label(false); ?>
            </div>
            <div class="col-3">
                <?= $form->field($model, 'address')->textInput(['placeholder' => 'Адрес', 'class' => $formClass])->label(false); ?>
            </div>
            <div class="col-1">
                <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary' , 'name' => 'contact-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

        <?php
        // Массив меток
        foreach ($sitmap as $item) {
            $coordinates = explode(',', $item->coordinates);
            $items[] =
                [
                    'latitude' => trim(array_shift($coordinates)),
                    'longitude' => $coordinates,
                    'options' => [
                        [
                            'hintContent' => $item->name,
                            'balloonContentHeader' => $item->name,
                            'balloonContentBody' => $item->description,
                            'balloonContentFooter' => $item->address,
                            'iconCaption' => $item->name,
                        ],
                        [
                            'preset' => 'islands#icon',
                            'iconColor' => '#19a111',
                        ]
                    ]
                ];
        }

        // вывод карты
        echo YandexMaps::widget([
            'myPlacemarks' => $items,
            'mapOptions' => [
                'center' => [53.382919, 55.904254],                                             // центр карты
                'zoom' => 16,                                                                   // показывать в масштабе
                'type' => 'yandex#satellite',
                'controls' => [],                                           // использовать эл. управления
                'control' => [
                    'zoomControl' => [                                                          // расположение кнопок управлением масштабом
                        'top' => 75,
                        'left' => 15
                    ],
                ],
            ],
            'disableScroll' => false,                                                            // отключить скролл колесиком мыши (по умолчанию true)
            'windowWidth' => '100%',                                                            // длинна карты (по умолчанию 100%)
            'windowHeight' => '600px',                                                          // высота карты (по умолчанию 400px)
        ]);
        ?>
    </div>

    <hr>

    <p>
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            Посмотреть список координат
        </button>
    </p>
    <div class="collapse" id="collapseExample">
        <div class="card card-block">
            <table class="table table-sm table-hover">
                <thead>
                <tr class="bg-sitdesk-block">
                    <th scope="col">Название</th>
                    <th scope="col">Координаты</th>
                    <th scope="col">Описание</th>
                    <th scope="col">Адрес</th>
                    <th scope="col">-</th>
                </tr>
                </thead>
                <tbody>
                <?php  foreach($sitmap as $item) : ?>
                    <tr>
                        <td><?=$item->name?></td>
                        <td><?=$item->coordinates?></td>
                        <td><?=$item->description?></td>
                        <td><?=$item->address?></td>
                        <td>
                            <?=Html::a('<span class="fas fa-edit"></span>', [\yii\helpers\Url::to(['adm/sitmap', 'upd' => $item->id])])?>
                            <?=Html::a('<span class="fas fa-trash-alt"></span>', [\yii\helpers\Url::to(['adm/sitmap', 'del' => $item->id])])?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

