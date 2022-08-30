<?php
    /**
     * 01gig
     */

    namespace app\components\temp;

    use app\models\Temp;
    use yii\base\Widget;


    /**
     * @property int $type Тип записи
     *
     * $type = 1. Ответ от 1С.  Action site/domain-translation
     * $type = 2. Api uri, для 1С.  Action site/domain-translation
     */

    class TempView extends Widget
    {

        public $type;

        public function init () {
            parent::init();
            if($this->type === null){$this->type = 0; }
        }

        public function run()
        {

            $query = Temp::find()->where(['type' => $this->type])->orderBy(['date' => SORT_DESC]);

            if ($this->type == 1){
                $query->limit(40);
            }
            $model = $query->all();

            return $this->render('tempView',
                [
                    'model' => $model,
                    'type' => $this->type
                ]);
        }
    }