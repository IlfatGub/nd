<?php


/**
 * $type
 * 1 - Новая заявка, по умолчанию
 * 2 - Переведена
 * 3 - Напоминание
 * 4 - Статус изменен
 *
 *
 */

namespace app\components\template;

use app\modules\admin\models\App;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\Login;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AppInfoTemplate extends Widget
{
    public $id;
    public $type;

    public function init()
    {
        parent::init();
        if ($this->id === null) {
            $this->id = 0;
        }
        if ($this->type === null) {
            $this->type = 1;
        }
    }


    public function run()
    {
        $model =  App::find()
            ->where(['app.id' => $this->id])
            ->joinWith(['problem' , 'priority'])
            ->joinWith(['user' => function($q){$q->select(['id', 'login']);}])
            ->joinWith(['priority', 'podr'])
            ->joinWith(['appContent' => function($q) {$q->joinwith(['fio']); }])
            ->one();

        $comment = AppComment::commentList($this->id);

        $app_user = App::find()
            ->where(['app.id_user' => $model->id_user])
            ->andWhere(['app.status' => [11,12,1]])
            ->joinWith(['problem' , 'priority'])
            ->joinWith(['user' => function($q){$q->select(['id', 'login']);}])
            ->joinWith(['priority', 'podr'])
            ->joinWith(['appContent' => function($q) {$q->joinwith(['fio']); }])
            ->all();

        return $this->render('appinfo',
            [
                'model' => $model,
                'type' => $this->type,
                'comment' => $comment,
                'app_user' => $app_user
            ]);
    }
}