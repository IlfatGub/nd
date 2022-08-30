<?php

namespace  app\components;
use app\modules\admin\models\App;
use app\modules\admin\models\Recal;
use yii\base\Widget;

class PhoneWidget extends Widget{

    public $id;

    public function init () {
        parent::init();
        if($this->id === null){
            $this->id = 0;
        }
    }

    public function run()
    {
        return $this->render('phone');
    }
}