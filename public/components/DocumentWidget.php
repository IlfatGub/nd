<?php

namespace  app\components;
use app\modules\admin\models\App;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\AppFiles;
use app\modules\admin\models\Recal;
use yii\base\Widget;

class DocumentWidget extends Widget{

    public $id_app;
    public $open;
    public $type;

    public function init () {
        parent::init();
        if($this->id_app === null){
            $this->id_app = array();
        }
        if($this->open === null){
            $this->open = 0;
        }
    }

    public function run()
    {

        return $this->render('document',
            [
                'document' => AppFiles::find()->where(['id_app' => $this->id_app, 'type' => [null, 2]])->all(),
                'open' => $this->open,
                'type' => $this->type
            ]);
    }
}