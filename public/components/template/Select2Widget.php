<?php

    namespace  app\components\template;
    use yii\base\Widget;

    /**
     *
     * Select 2 widget
     */
    class Select2Widget extends Widget{

        public $form;
        public $model;
        public $data;
        public $label  = false;
        public $placeholder  = false;
        public $multiple = false;
        public $var;

        public function init () {
            parent::init();
            if($this->model === null){
                $this->model = 0;
            }
            if($this->form === null){
                $this->form = 0;
            }
        }

        public function run()
        {

            return $this->render('select2',
                [
                    'form' => $this->form,
                    'model' => $this->model,
                    'data' => $this->data,
                    'label' => $this->label,
                    'placeholder' => $this->placeholder,
                    'multiple' => $this->multiple,
                    'var' => $this->var,
                ]);
        }
    }