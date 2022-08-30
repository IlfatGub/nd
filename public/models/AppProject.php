<?php

    namespace app\models;

    use app\modules\admin\models\App;
    use app\modules\admin\models\AppAnalog;
    use app\modules\admin\models\AppComment;
    use app\modules\admin\models\AppContent;
    use app\modules\admin\models\Buh;
    use app\modules\admin\models\Fio;
    use app\modules\admin\models\History;
    use app\modules\admin\models\Login;
    use Yii;
    use yii\helpers\ArrayHelper;

    /**
     * This is the model class for table "work_group_ticket".
     *
     * @property int $id
     * @property int $user_ct
     * @property int $date_pl
     * @property string $date_cur
     * @property int $date_cl
     * @property string $date_ct
     * @property int $date_transfer_test
     * @property int $date_transfer_operation
     * @property int $base
     * @property int $type
     * @property int $visible
     * @property int $id_project
     * @property int $date_comm
     * @property int $parent_id
     * @property int $task_num
     *
     * @property int $user_init Инициатор
     * @property int $user_exec Исполнитель
     * @property int $user_cur  Куратор
     * @property int $project_num  Номер проекта
     *
     * @property int $status
     * @property int $badge
     *
     * @property string $name
     * @property string $tz  Техническое задание
     * @property string $description
     * @property string $act
     * @property string $user_cur_name
     * @property string $comment
     */
    class AppProject extends \yii\db\ActiveRecord
    {

        public $id_project;

        /**
         * {@inheritdoc}
         */
        public static function tableName()
        {
            return 'appProject';
        }

        /**
         * {@inheritdoc}
         */
        public function rules()
        {
            return [
                [['user_ct', 'base', 'name'], 'required'],
                [['user_ct', 'base', 'type', 'visible', 'id_project', 'status', 'user_cur', 'user_exec', 'parent_id', 'project_num', 'task_num', 'badge'], 'integer'],
                [['date_pl', 'date_cur', 'date_cl', 'date_ct', 'date_comm', 'date_transfer_test', 'date_transfer_operation'], 'safe'],
                [['name', 'description', 'act', 'user_init', 'tz', 'user_cur_name','comment'], 'string'],
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


        public function getApp()
        {
            return $this->hasOne(App::className(), ['id' => 'id_app']);
        }

        public function getUser()
        {
            return $this->hasOne(Login::className(), ['id' => 'user_exec']);
        }

        public function getComment()
        {
            return $this->hasMany(AppComment::className(), ['id_app' => 'id_app'])->orderBy(['date' => SORT_DESC]);
        }

        public function delIdApp()
        {
            if ($this->existsIdApp()) {
                return self::deleteAll(['id_app' => $this->id_app]);
            }
            return true;
        }

        public function existsIdApp()
        {
            return self::find()->where(['id_app' => $this->id_app])->exists();
        }


        public function existsName()
        {
            return self::find()->where(['name' => $this->name])->count();
        }

        public static function getList()
        {
            return ArrayHelper::map(self::find()->where(['visible' => null])->all(), 'id', 'name');
        }

        public static function getIdAppByParentId($id_app)
        {
            return self::findOne($id_app)->id_app;
        }




        public function getSave($result = 'Запись обновлена')
        {
            $status = false;
            $data = null;

            try {
                if ($this->save()) {
                    $status = true;
                    $data = $this;
                } else {
                    $error = '';
                    foreach ($this->errors as $key => $value) {
                        $error .= '<br>' . $key . ': ' . $value[0];
                    }
                    $result = $error;
                }
            } catch (\Exception $ex) {
                $result = 'Add Project. Error';
            }

            return [$status, $result, $data];
        }


        public function getProjectStatus()
        {
            return [
                '1' => 'В работе',
                '2' => 'Выполнено',
                '3' => 'Выполнено. Принято в эксплуатацию',
                '4' => 'Задача снята',
                '5' => 'Не разработано',
                '6' => 'Подготовка ТЗ',
                '7' => 'Тестирование',
                '8' => 'Новая',
            ];
        }


        public function getProjectStatus2()
        {
            return [
                'В работе' => '1',
                'Выполнено' => '2',
                'Выполнено. Принято в эксплуатацию' => '3',
                'Задача снята' => '4',
                'Не разработано' => '5',
                'Подготовка ТЗ' => '6',
                'Тестирование' => '7',
                'Новая' => '8',
            ];
        }

        public function getProjectStatusBadge()
        {
            return [
                '1' => '<span style="color: white;" class="badge bg-primary">В разработке</span>',
                '2' => '<span style="color: white;" class="badge bg-secondary">На тестрировании</span>',
                '3' => '<span style="color: white;" class="badge bg-success">Выполнено</span>'
            ];
        }


        public static function setProjectBase($base, $id_app)
        {
            $__prj = AppProject::findOne(['id_app' => $id_app]);
            $__prj->base = $base;
            $__prj->save();

            $_analog = AppAnalog::find()->where(['id_parent_app' => $id_app])->all();
            foreach ($_analog as $_item) {
                $__app = AppContent::findOne(['id_app' => $_item->id_app]);
                $__app->buh = $base;
                $__app->save();

                $__prj = AppProject::findOne(['id_app' => $_item->id_app]);
                $__prj->base = $base;
                $__prj->save();
            }
        }

        public static function setTicketBase($base, $id_app)
        {
            $__app = AppContent::findOne(['id_app' => $id_app]);
            $__app->buh = $base;
            $__app->save();
        }


        /**
         * @param $query AppProject;
         * @param $post_filter
         * @return mixed
         *
         * фильтр по задачам
         */
        public function filterProject($query, $post_filter)
        {

            $query->andWhere(['not', ['parent_id' => null]]);

            $operator = 'in';

            $file_in_like = ["user_cur_name"];

            foreach ($post_filter as $key => $items) {
                $val = [];
                if (is_array($items)){
                    foreach ($items as $item) {
                        //Статус
                        if ($key == 'status') {
                            $val[] = AppProject::getProjectStatus2()[$item];
//                        echo AppProject::getProjectStatus2()[$item];
//                        echo "<br>";
                        } elseif ($key == 'base') {
                            $val[] = Buh::findOne(['name' => $item])->id;
                        } elseif ($key == 'user_exec' or $key == 'user_cur') {
                            $val[] = Login::findOne(['username' => $item])->id;
//                        echo Login::findOne(['username' => $item])->id;
//                        echo "<br>";
                        } else {
                            $val[] = $item;
                        }
                    }
                }else{
                    $val[] = $items;
                }

//                    echo "<br>";
                if (in_array($key, $file_in_like))
                    $operator = 'like';

                if ($operator == 'in') {
                    $query->andWhere([$operator, $key, $val]);
                } elseif ($operator == 'like') {

                    foreach ($val as $_val) {
                        $query->andWhere([$operator, $key, $_val]);
                    }
                }
            }
            return $query;
        }

        //выводим проекты
        public function getParentProject($query)
        {
            return $query->andWhere(['parent_id' => null]);
        }

        //выводим задачи рабочей группы
        public function getWgtProject($query, $wg_ticket, $wg_project, $post_filter)
        {
            if ($wg_ticket)
                $query->orWhere(['id' => $wg_ticket]);
            if ($wg_project)
                $query->orWhere(['parent_id' => $wg_project]);

            $query = self::filterProject($query, $post_filter);

            return $query;
        }

        //выводим задачи у который подходит дата тестирования
        public function getDeadlineProject($query, $deadline, $post_filter)
        {
            //дата для поиска
            $date_to = date('Y-m-d');
            $date_do = date('Y-m-d', strtotime(date('Y-m-d') . '+' . $deadline . ' day'));

            $query->andWhere(['>=', 'date_transfer_test', $date_to]);
            $query->andWhere(['<=', 'date_transfer_test', $date_do]);

            $query = self::filterProject($query, $post_filter);

            return $query;
        }

        //выводим задачи исполнтелей
        public function getUserExecProject($query, $user, $post_filter)
        {

            $query->joinWith(['user']);
            $query->andWhere(['=', 'login.username', $user]);

            $query = self::filterProject($query, $post_filter);

            return $query;
        }


        public function addProject($id)
        {

            $text = 'Тестовая задача';

            $model = new AppProject();
            $analog = new AppAnalog();
            $project = AppProject::findOne(['id_app' => $id]);

            $app = App::findOne($id);
            $app_content = AppContent::findOne(['id_app' => $id]);

            //заявка
            $new_app = new App();
            $new_app->attributes = $app->attributes;
            $new_app->id_user = 68;
            $new_app->date_ct = strtotime('now');
            $new_app->status = History::STATUS_CONSIDERATION;
            $new_app->type = $new_app::TYPE_PROJECT_TICKET;
            $new_app->id_depart = $app->id_depart;
            $new_app->api_login = null;


            if ($new_app->save()) {
                //контент по заявке
                $new_content = new AppContent();
                $new_content->attributes = $app_content->attributes;
                $new_content->id_fio = Fio::getId('--- --- ---');
                $new_content->content = $text;
                $new_content->id_app = $new_app->id;
                $new_content->save();

                //записываем как аналогичная заявка
                $analog->id_app = $new_app->id;
                $analog->id_parent_app = $analog->getMainId($app->id);
                $analog->save();

                $pr_num = AppProject::find()->where(['not', ['parent_id' => null]])->orderBy(['task_num' => SORT_DESC])->one();

                //проект
                $model->name = $project->name;
                $model->project_num = $project->project_num;
                $model->base = $project->base;
                $model->task_num = $pr_num->task_num + 1;
                $model->date_comm = strtotime('now');
                $model->user_ct = $project->user_ct;
                $model->user_exec = 68;
                $model->user_cur = 56;
                $model->description = $text;
                $model->id_app = $new_app->id;
                $model->parent_id = $project->id;
                $model->date_ct = date('Y-m-d');
                $model->save();

                $new_app->id_project = $model->id;
                $new_app->save();
            }

            History::add($new_app, History::STATUS_ANALOG_CONS); // записываем как "На расммотрении"
            Sitdesk::appMail($new_app->id, $new_app->id_user);  //отправляем письмо

            return $model;
        }


    }
