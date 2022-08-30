<?php

namespace  app\components;

/**
 * This is the model class for table "app".
 *
 * @property integer $id
 * @property integer $type

 *
 *
 * @var $type = 1 Коментари по проекту
 * @var $type = null Коментари по заявке
 */

use app\modules\admin\models\AppComment;
use yii\base\Widget;

class CommentWidget extends Widget{

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

        return $this->render('comment',
            [
                'comment' => AppComment::commentList($_GET['id'], $this->type),
                'type' => $this->type,
            ]);
    }
}