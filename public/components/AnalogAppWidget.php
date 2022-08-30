<?php

namespace app\components;

use app\modules\admin\models\App;
use app\modules\admin\models\AppAnalog;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\Login;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AnalogAppWidget extends Widget
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
        $app = App::findOne($this->id);
        $comment = AppComment::commentList($this->id);    //вывод коментарий заявки

        return $this->render('analogApp',
            [
                'app' => $app,
                'comment' => $comment,
                'id' => $this->id,
            ]);
    }
}