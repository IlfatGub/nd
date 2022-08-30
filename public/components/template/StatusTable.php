<?php


    namespace app\components\template;

    use yii\base\Widget;

    class StatusTable extends Widget
    {
        public $model;
        public $max;

        public function init()
        {
            parent::init();
            if ($this->model === null) {
                $this->model = 0;
            }
        }


        public function run()
        {

            return $this->render('statusTable', ['model' => $this->model, 'max' => $this->max]);
        }
    }
