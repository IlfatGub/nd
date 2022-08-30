<?php


    /**
     * $type
     * Правый Сайдбар
     * Общаф информация
     *
     */

    use yii\base\Widget;

    class SideInfo extends Widget
    {
        public $id;

        public function init()
        {
            parent::init();
            if ($this->id === null) {
                $this->id = 0;
            }
        }


        public function run()
        {
            return $this->render('sideinfo');
        }
    }