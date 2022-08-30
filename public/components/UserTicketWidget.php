<?php

    /**
     * Заявки от пользователя
     * за период времени
     * выводиться при выборе заявки
     */

    namespace app\components;

    use app\modules\admin\models\App;
    use yii\base\Widget;

    class UserTicketWidget extends Widget
    {

        public $username;
        public $id;

        public function init()
        {
            parent::init();
            if ($this->username === null) {
                $this->username = 0;
            }
            if ($this->id === null) {
                $this->id = 0;
            }
        }

        public function run()
        {

            $model = null;

            if ($this->username) {
                $model = App::find()
                    ->joinWith(['appContent' => function ($q) {
                        $q->joinwith(['fio']);
                    }])
                    ->joinWith(['user'])
                    ->where(['fio.name' => $this->username])
                    ->andFilterWhere(['>=', 'date_ct', strtotime("-1 month")])
                    ->andFilterWhere(['<=', 'date_ct', strtotime("now")])
                    ->andFilterWhere(
                        ['is', 'api_login', new \yii\db\Expression('null')]
                    )
                    ->andFilterWhere(['<>', 'app.id', $this->id])
                    ->orderBy(['date_ct' => SORT_DESC])
                    ->all();
            }

            return $this->render('userTicket',
                [
                    'model' => $model,
                ]);
        }
    }