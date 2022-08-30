<?php

namespace  app\components;
use app\models\Sitdesk;
use app\modules\admin\models\App;
use app\modules\admin\models\Help;
use app\modules\admin\models\Recal;
use yii\base\Widget;

class HelpWidget extends Widget{

    public $id;

    public function init () {
        parent::init();
        if($this->id === null){
            $this->id = 0;
        }
    }

    public function run()
    {
        $model = new Help();
        $help = Help::find()->where(['parent_id' => null])->all();

        return $this->render('help',
            [
                'help' => $help,
                'model' => $model,
            ]);
    }
}