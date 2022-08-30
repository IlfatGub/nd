<?php

namespace app\components;

use app\modules\admin\models\App;
use app\modules\admin\models\AppAnalog;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\Login;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AnalogTicket extends Widget
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


        $res1 = $res2 = $res3 = $res4 = array();
        $analog = new AppAnalog(['id_app' => $this->id]);

        $app = $analog->getAnalog();

        if ($app){
            $apps = App::find()->where(['in', 'id', $app])->all();
            $comment = AppComment::commentList($app);


            return $this->render('analogTicket',
                [
                    'model' => $apps,
                    'comment' => $comment,
                    'id' => $this->id,
                ]);
        }


    }
}