<?php

    namespace app\modules\admin\models;

    use app\models\Depart;
    use app\modules\admin\models\App;
    use Yii;
    use yii\base\Model;
    use yii\data\ActiveDataProvider;
    use yii\data\ArrayDataProvider;
    use yii\data\Pagination;
    use yii\helpers\ArrayHelper;

    /**
     * DirectorySearch represents the model behind the search form about `app\module\admin\models\Directory`.
     */
    class AppSearch extends App
    {

        public $date_to, $date_do, $date_cl_to, $date_cl_do;
        public $id_username;
        public $id_status;
        public $id_buh;
        public $stupid;
        /**
         * @inheritdoc
         */
        public function scenarios()
        {
            // bypass scenarios() implementation in the parent class
            return Model::scenarios();
        }


        public function rules()
        {
            return [
                [['id', 'id_wh'], 'integer'],
            ];
        }

        /**
         * Creates data provider instance with search query applied
         *
         * @param array $params
         *
         * @return ActiveDataProvider
         */
        public function search($params)
        {





            $date_cl_to = isset($_GET['date_cl_to']) ? $_GET['date_cl_to'] : null;
            $date_cl_do = isset($_GET['date_cl_do']) ? $_GET['date_cl_do'] : null;

            if (!$date_cl_to and !$date_cl_do){
                $this->date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
                $this->date_do = isset($_GET['date_do']) ? $_GET['date_do'] : null;
            }else{
                $_GET['date_to'] = null;
                $_GET['date_do'] = null;
            }


            $this->id_class = isset($_GET['id_class']) ? $_GET['id_class'] : null;
            $this->id_object = isset($_GET['id_object']) ? $_GET['id_object'] : null;
            $this->id_problem = isset($_GET['id_problem']) ? $_GET['id_problem'] : null;
            $this->id_username = isset($_GET['id_username']) ? $_GET['id_username'] : null;
            $this->id_status = isset($_GET['id_status']) ? $_GET['id_status'] : null;
            $this->id_buh = isset($_GET['id_buh']) ? $_GET['id_buh'] : null;
            $this->stupid = isset($_GET['stupid']) ? $_GET['stupid'] : null;


            $this->id_user = isset($_GET['id_user']) ? $_GET['id_user'] : null;
            $this->id_podr = isset($_GET['id_podr']) ? $_GET['id_podr'] : null;
            $this->id_depart = isset($_GET['id_depart']) ? $_GET['id_depart'] : null;


            $problem = null;
            $_depart = null;

            $query = App::find()
                ->joinWith(['podr', 'user', 'problem', 'appContent', 'appComment'])
                ->joinWith(['appContent' => function ($q) {
                    $q->joinwith(['fio', 'buhg']);
                }])
                ->andFilterWhere(['<>', 'app.id_user', 50]);
            ;


            $query->andFilterWhere([
                'app.id_podr' => $this->id_podr ,
            ]);


            if ($this->id_class and !$this->id_object and !$this->id_problem){
                $obj = ArrayHelper::map(Problem::getProblemMain($this->id_class), 'id', 'id');
                $problem = ArrayHelper::map(Problem::find()->where(['parent_id'  => $obj])->andWhere(['visible' => '1'])->andWhere(['type' => '1'])->all(), 'id', 'id');
                $query->andFilterWhere(['app.id_problem' => $problem]);
            }elseif ($this->id_class and $this->id_object and !$this->id_problem){
                $problem = ArrayHelper::map(Problem::getProblemMain($this->id_object), 'id', 'id');
                $query->andFilterWhere(['app.id_problem' => $problem]);
            }elseif ($this->id_class and $this->id_object and $this->id_problem){
                $query->andFilterWhere(['app.id_problem' => $this->id_problem]);
            }elseif (!$this->id_class and !$this->id_object and $this->id_problem){
                $pr = ArrayHelper::map(Problem::find()->andFilterWhere(['Like', 'name', mb_substr($this->id_problem, 1)])->all(), 'id', 'id');
                $query->andFilterWhere(['in', 'app.id_problem', $pr]);
            }


            if ($this->id_depart and $this->id_podr) {
                $depart = Depart::getDepartByOrg($this->id_podr, $this->id_depart, 1);
                $_depart = Depart::find()->where(['id_depart' => array_keys($depart)])->select(['id'])->column();
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<br>";
                print_r($depart);
                echo "<br>";
                echo "<br>";
                print_r(array_keys($depart));
                echo "<br>";
                echo "<br>";
                print_r($_depart);
            } elseif ($this->id_podr) {
                $depart = Depart::getDepartByOrg($this->id_podr, null, 1);
                $_depart = Depart::find()->where(['id_depart' => array_keys($depart)])->select(['id'])->column();
            }

            if (in_array($this->id_user, ['sit', 'sap', 'ss'])){
                switch ($this->id_user){
                    case 'sit'://НХРС
                        $query->andFilterWhere(['in', 'app.id_user', ArrayHelper::map(Login::find()->where(['depart' => 1])->all(), 'id', 'id')]);
                        break;
                    case 'sap'://ЗСМиК
                        $query->andFilterWhere(['in', 'app.id_user', ArrayHelper::map(Login::find()->where(['depart' => 3])->all(), 'id', 'id')]);
                        break;
                    case 'ss'://ЗСМиК
                        $query->andFilterWhere(['in', 'app.id_user', ArrayHelper::map(Login::find()->where(['depart' => 4])->all(), 'id', 'id')]);
                        break;
                }
            }else{
                $query->andFilterWhere(['app.id_user' => $this->id_user]);
            }

            isset($problem) ? $query->andFilterWhere(['app.id_problem' => $problem]) : null;
            isset($this->stupid) ? $query->andFilterWhere(['app.stupid' => $this->stupid]) : null;
            isset($_depart) ? $query->andFilterWhere(['app.id_depart' =>$_depart]) : null;
            isset($this->id_buh) ? $query->andFilterWhere(['appContent.buh' => $this->id_buh]) : null;
            isset($id_username) ? $query->andFilterWhere(['like', 'appContent.fio.name' , $id_username]) : null;

            if (isset($this->id_status)){
                if ($this->id_status == Status::STATUS_ACTIVE_APP){
                    $query->andFilterWhere(['in', 'status' , Status::getStatusActive()]);
                }else{
                    $query->andFilterWhere(['=', 'status' , $this->id_status]);
                }
            }

            if (!$date_cl_to and !$date_cl_do){
                $query->andFilterWhere(['>=', 'date_ct', MyDate::getTimestamp($this->date_to. ' 00:00:00')])
                    ->andFilterWhere(['<=', 'date_ct',   MyDate::getTimestamp($this->date_do. ' 23:59:59')]);
            }else{
                $query->andFilterWhere(['>=', 'appContent.date_cl', MyDate::getTimestamp($date_cl_to. ' 00:00:00')])
                    ->andFilterWhere(['<=', 'appContent.date_cl',   MyDate::getTimestamp($date_cl_do. ' 23:59:59')]);
            }
				
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 5000,
                ],
            ]);

            return $dataProvider;
        }
    }
