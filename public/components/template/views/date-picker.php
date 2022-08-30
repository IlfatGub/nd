<?php
    /**
     * @var  $form
     * @var  $model
     * @var  $placeholder
     * @var  $multiple
     * @var  $var
     */

    use kartik\widgets\DatePicker;

    $placeholder = $placeholder ? $placeholder : null;
    $label = $label ? $label : null;


    echo $form->field($model, $var)->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Плановая дата',  'autocomplete' => 'off' ,'class' => 'form-control form-control-sm'],
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'class' => 'form-control form-control-sm'
        ]
    ]);