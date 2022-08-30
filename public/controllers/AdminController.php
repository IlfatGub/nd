<?php

namespace app\controllers;

//session_start();

use app\models\Sitdesk;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\AppContent;
use app\modules\admin\models\Call;
use app\modules\admin\models\Fio;
use app\modules\admin\models\History;
use Codeception\Step\Comment;
use Yii;
use yii\debug\models\search\Log;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\modules\admin\models\Login;
use app\models\User;
use app\modules\admin\models\App;
use app\modules\admin\models\MyDate;
use app\modules\admin\models\Recal;
use yii\helpers\ArrayHelper;

//date_default_timezone_set('Asia/Yekaterinburg');

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionProblem()
    {
        return $this->renderAjax('problem');

    }

    public function actionLogin()
    {

        return $this->renderAjax('login');
    }

    public function actionPodr()
    {
        return $this->renderAjax('podr');

    }



}


