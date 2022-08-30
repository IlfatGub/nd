<?php

namespace  app\components;
use app\modules\admin\models\App;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\AppContent;
use app\modules\admin\models\Recal;
use yii\base\Widget;
use app\models\Sitdesk;


class FioCaseWidget extends Widget{

    public $id;
    public $type;

    public function init () {
        parent::init();
        if($this->id === null){
            $this->id = 0;
        }
        if($this->type === null){
            $this->type = 0;
        }
    }

    public function run()
    {

        $content = AppContent::findOne(['id_app' => $this->id]);

        $model = Sitdesk::getFioCase($content->content);

        return $this->render('fiocase',
            [
                'model' => $model,
                'type' => $this->type
            ]);
    }
}