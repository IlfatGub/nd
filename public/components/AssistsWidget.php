<?php

namespace app\components;

use app\modules\admin\models\App;
use app\modules\admin\models\Login;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AssistsWidget extends Widget
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
        $model = Login::find()->where(['depart' => \Yii::$app->user->identity->depart])->all();

//        $model = App::find()
//            ->joinWith(['problem'])
//            ->joinWith(['user' => function($q){$q->select(['id', 'username']);}])
//            ->andWhere(['type' => null])
//            ->andWhere(['id_user' => ArrayHelper::map( Login::find()->where(['depart' => $_SESSION['User']['depart']])->all(), 'id', 'id')])
//            ->andWhere(['status' => 1])
//            ->andWhere('id_user != :id_user', ['id_user' => $_SESSION['User']['id']])
//            ->select(['app.id', 'id_user', 'id_priority', 'id_problem', 'review'])
//            ->orderBy(['id_user' => SORT_DESC])
//            ->all();

        return $this->render('assists',
            [
                'model' => $model,
            ]);
    }
}