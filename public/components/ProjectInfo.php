<?php

    namespace  app\components;
    use app\models\AppProject;
    use app\modules\admin\models\App;
    use app\modules\admin\models\Recal;
    use yii\base\Widget;

    class ProjectInfo extends Widget{

        public $id;

        public function init () {
            parent::init();
            if($this->id === null){
                $this->id = 0;
            }
        }

        public function run()
        {
            $model = AppProject::findOne($this->id);

            return $this->render('project-info',
                [
                    'model' => $model,
                ]);
        }
    }