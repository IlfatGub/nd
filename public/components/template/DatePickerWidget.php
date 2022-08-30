<?php

    namespace  app\components\template;
    use yii\base\Widget;

    /**
     *
     * Select 2 widget
     */
    class DatePickerWidget extends Widget{

        public $form;
        public $model;
        public $placeholder;
        public $label;
        public $var;

        public function run()
        {
            return $this->render('date-picker',
                [
                    'form' => $this->form,
                    'model' => $this->model,
                    'placeholder' => $this->placeholder,
                    'label' => $this->label,
                    'var' => $this->var,
                ]);
        }
    }