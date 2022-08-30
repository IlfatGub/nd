<?php

    namespace app\models;

    /**
     *
     *
     * @property int $type Тип записи
     * @property int $date Дата
     *
     * @property string $t1
     * @property string $t2
     * @property string $t3
     * @property string $t4
     * @property string $t5
     * @property string $t6
     * @property string $t7
     *
     *
     * $type = 1. Ответ от 1С.  Action site/domain-translation
     *
     * $type = 2. Api uri, для 1С.  Action site/domain-translation
     *
     * $type = 3. Файлы по excell
     *
     *
     */

    class Temp extends \yii\db\ActiveRecord
    {


        const TYPE_DOMAIN_TRANSLATION = 1;
        const TYPE_API_URI = 2;
        const TYPE_Excell = 3;


        public static function tableName()
        {
            return 'temp';
        }

        public function rules()
        {
            return [
                [['t1', 't2', 't3', 't4', 't5', 't6', 't7'], 'string'],
                [['date', 'type'], 'integer'],
            ];
        }

        public function attributeLabels()
        {
            return [
            ];
        }

        public function setTemp(){
            $this->date = strtotime(date('Y-m-d H:i:s'));
            $this->save();
        }

        public function del(){
            if ($this->existsById()){
                $this::findOne($this->id)->delete();
            }
        }

        public function existsById(){
            return self::find()->where(['id' => $this->id])->exists();
        }

    }
