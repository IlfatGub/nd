<?php
namespace app\modules\admin\controllers;
session_start();



/**
 * This is the model class for table "app".
 *
 * @property integer $id
 * @property integer $id_app
 * @property integer $comment
 * @property integer $id_user
 * @property integer $date
 * @property integer $user_visible
 * @property integer $visible
 * @property integer $type
 */

use app\components\CommentWidget;
use app\models\Sitdesk;
use app\modules\admin\models\AppAnalog;
use app\modules\admin\models\Comment;
use app\modules\admin\models\MyDate;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\App;
use app\modules\admin\models\History;
use app\modules\admin\models\Recal;
use app\modules\admin\models\Call;

date_default_timezone_set('Asia/Yekaterinburg');

class CommentController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    public function rules()
    {
        return [
            [['date', 'id_app', 'comment', 'id_user'], 'required'],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new LoginSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    //добавляем комментарий
    public function actionAjax($id, $text, $type = null){
        $model = new AppComment();
        $model->id_app = $id;
        $model->comment = $text;
        $model->type = $type;
        $res = $model->commentAdd();
        if ($res['result'] == true){
            $res['data'] = CommentWidget::widget(['id' => $id, 'type' => $type]);
        }

        return json_encode($res);
    }


    public function actionCreate($id, $comment = null, $search = null, $type = null)
    {
        $model = new AppComment();

        if(isset($comment)){ $_POST['App']['comment'] = $comment; }

        if($_POST['App']['comment']){

            Comment::commentAdd($id, $_POST['App']['comment']);

            if(mb_strtolower($_POST['App']['comment'], "UTF-8") == 'выполнено'){
                if((Yii::$app->user->identity->close) and (Yii::$app->user->id == App::findOne($id)->id_user) ){
                    $app = new App();
                    $app->id = $id;
                    $app->status = 3;
                    $app->setStatus();
//                    App::set($id, 3);
                    return $this->redirect(['/site/index']);
                }
                return $this->redirect(['/site/index', 'id' => $_GET['id'], '#' => 'hs_'.$_GET['id']]);
            }
        }

        return $this->redirect(['/site/index', 'id' => $_GET['id'], '#' => 'hs_'.$_GET['id']]);
    }

    public function actionAdd($text, $id){
        if(trim($text)){
            $app =  App::findOne($id);
            $app_id_user = $app->id_user;

            $model = new AppComment();
            $model->id_app = $id;
            $model->comment = Comment::getId($text);
            $model->text = $text;
            $model->id_user = isset(Yii::$app->user->id)? Yii::$app->user->id : $app_id_user ;
            $model->date = MyDate::getTimestamp(date('Y-m-d H:i:s'));
            $model->save();

            if(mb_strtolower($text, "UTF-8") == 'выполнено'){

                if((Yii::$app->user->identity->close) and (Yii::$app->user->id == $app_id_user) and  $app->status != 12){

                    return $this->redirect([Url::toRoute(['/site/status', 'id' => $id, 'status' => 3])]);
//
                }
            }

            $comment =  AppComment::findOne([
                'comment' => Comment::getId($text),
                'id_user' => isset(Yii::$app->user->id)? Yii::$app->user->id : $app_id_user]);


            $_count = AppComment::find()->where(['id_app' => $id])->count();
            $count = AppComment::find()->where(['id_app' => $id])->count() >= 1 ? true : false;
            $data = "<small style='max-width: 200px;'><label class=\"switch float-right\" title='Включаем комментарий для пользователя'>
                                        <input type=\"checkbox\" class=\"hd-checkbox\" class=\"success comment-view\" id =\"$id\">
                                    </label>".MyDate::getDate($comment->date).". <strong>".Sitdesk::fio($comment->user->username).": </strong></small><br>";

//            if ($_count == 1){
//                return $this->redirect(['/site/index', 'id' => $id]);
                return $this->redirect(Yii::$app->request->referrer);
//            }

            return json_encode(array($data, $comment->id, $count));
        }
    }

    /**
     * Updates an existing Executor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Executor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($delete)
    {
        echo "asdasd"; die();
        $comment = AppComment::findOne($delete);
        $comment->visible = 1;
        $comment->save();

        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Finds the Executor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Executor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Login::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}



