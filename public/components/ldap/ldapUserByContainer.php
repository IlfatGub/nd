<?php

namespace  app\components\ldap;

use yii\base\Widget;

class ldapUserByContainerWidget extends Widget{

    public $model;

    public function init () {
        parent::init();
        if($this->model === null){
            $this->model = 0;
        }
    }

    public function run()
    {

        return $this->render('userByContainer',
            [
                'model' => $this->model,
            ]);
    }
}