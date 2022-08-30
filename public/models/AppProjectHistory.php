<?php

    namespace app\models;

    use Yii;
    use yii\helpers\ArrayHelper;

    /**
     * This is the model class for table "work_group_ticket".
     *
     * @property int $id
     * @property int $user_ct
     * @property int $date_pl
     * @property int $date_cur
     * @property int $date_cl
     * @property int $base
     * @property int $type
     * @property int $visible
     * @property int $id_project
     * @property int $date_comm
     *
     * @property string $name
     * @property string $description
     * @property string $comment
     */
    class AppProjectHistory extends \yii\db\ActiveRecord
    {

        public $id_project;
        /**
         * {@inheritdoc}
         */
        public static function tableName()
        {
            return 'appProjectHistory';
        }

        /**
         * {@inheritdoc}
         */
        public function rules()
        {
            return [
                [['user_ct', 'base', 'name'], 'required'],
                [['user_ct', 'base','type','visible','id_project'], 'integer'],
                [['date_pl','date_cur','date_cl','date_comm'], 'safe'],
                [['name', 'description', 'comment'], 'string'],
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function attributeLabels()
        {
            return [
                'date_pl' => 'date_pl',
                'date_cur' => 'date_cur',
                'date_cl' => 'date_cl',
                'base' => 'base',
                'type' => 'type',
                'visible' => 'visible',
                'name' => 'name',
                'description' => 'description',
            ];
        }

    }
