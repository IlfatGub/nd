<?php

namespace  app\components;
use app\models\Sitdesk;
use app\modules\admin\models\App;
use app\modules\admin\models\Help;
use app\modules\admin\models\Recal;
use yii\base\Widget;

class ldapUserInfo extends Widget{

    public $model;

    public function init () {
        parent::init();
        if($this->model === null){
            $this->model = 0;
        }
    }

    public function run()
    {

        echo "<pre>";
        print_r($this->model); die();

        return $this->render('ldapUserInfo',
            [
                'model' => $this->model,
            ]);
    }
}