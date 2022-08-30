<?php
    /**
     * @var  $old_login ;
     * @var  $new_login ;
     */

    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Html;

    $i = 1;
?>

<div class="container-fluid px-5">
    <div class="row justify-content-md-center">
        <div class="col-8 ">
            <div class="col card px-2  " >

                <div class="alert alert-primary mt-3 pl-3 py-1" role="alert">
                    Перевод пользователя
                </div>

                <?php $form = ActiveForm::begin(['id' => 'domain-translation']) ?>

                <div class="form-group row mb-0">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Старая учетка</label>

                    <div class="form-row col-10">
                        <div class="col-md-4 mb-3">
                            <?= Html::input('text', 'old_login', $old_login, ['class' => 'form-control', 'placeholder' => 'Старая уч. запись']) ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <select name="old_prefix" class="custom-select" id="validationTooltip04" required>
                                <option selected value="@zsmik.com">@zsmik.com</option>
                                <option value="@a-consalt.ru">@a-consalt.ru</option>
                                <option value="@nhrs.ru">@nhrs.ru</option>
                                <option value="@snhrs.ru">@snhrs.ru</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Новая учетка</label>
                    <div class="form-row col-10">
                        <div class="col-md-4 mb-3">
                            <?= Html::input('text', 'new_login', $new_login, ['class' => 'form-control', 'placeholder' => 'Новая уч. запись']) ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <select name="new_prefix" class="custom-select" id="validationTooltip04" required>
                                <option value="@zsmik.com">@zsmik.com</option>
                                <option value="@a-consalt.ru">@a-consalt.ru</option>
                                <option selected value="@nhrs.ru">@nhrs.ru</option>
                                <option value="@snhrs.ru">@snhrs.ru</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-10">
                        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary mb-2']) ?>
                    </div>
                </div>

                <?php if($result): ?>
                    <div class="alert alert-success mt-3" role="alert">
                        <?= $result ?>
                    </div>
                <?php endif; ?>

                <?php ActiveForm::end(); ?>
            </div>

            <div class="card px-2 mt-2">
                <?= \app\components\temp\TempView::widget(['type' => 1]) ?>

            </div>

        </div>

        <div class="col-4 card pt-3">
            <div class="row">
                <div class="col-10 mr-0 pr-0">
                    <?= Html::input('text', 'api-uri', $old_login, ['class' => 'form-control', 'placeholder' => 'api uri', 'id' => 'api-uri']) ?>
                </div>
                <div class="col-2 m-0 p-0">
                    <?= Html::button('Ok', ['class' => 'btn btn-primary mb-2', 'id' => 'api-uri-add']) ?>
                </div>
            </div>

            <div id="api-uri-error">
            </div>
            <div id="api-uri-content">
                <?= \app\components\temp\TempView::widget(['type' => 2]) ?>
            </div>
        </div>

    </div>
</div>