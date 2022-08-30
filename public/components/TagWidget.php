<?php

namespace  app\components;
use app\modules\admin\models\App;
use app\modules\admin\models\Recal;
use yii\base\Widget;

class TagWidget extends Widget{

    public $content;

    public function init () {
        parent::init();
        if($this->content === null){
            $this->content = 0;
        }
    }

    public function run()
    {
        return $this->render('tag',
            [
                'content' => $this->content,
            ]);
    }
}