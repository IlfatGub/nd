<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\Html;

date_default_timezone_set('Asia/Yekaterinburg');


/**
 * @property int $id
 * @property int id_user
 * @property int $id_app
 * @property string comment
 * @property int date
 * @property int visible
 * @property int type
 * @property int api_login
 *
 *
 *
 * $type = 1 Документы по проекту
 * $type = 2 Документы по задаче
 * $type = 3 ТЗ по задаче
 * $type = 4 файлы к комменатрию
 */


class AppComment extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'appComment';
    }


    public function rules()
    {
        return [
            [['id_app', 'id_user', 'comment', 'date', 'visible', 'type', 'user_visible'], 'safe'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id_app' => 'Подр./Отдел',
            'user_ct' => 'Создатель',
            'date_cl' => 'Дата закрытия',
            'content' => 'описание',
            'id_fio' => 'ФИО',
            'ip' => 'ip',
            'phone' => 'телефон',
            'dv' => 'Посмотрен',
        ];
    }

    public function getFio()
    {
        return $this->hasOne(Fio::className(),['id'=>'id_fio']);
    }
    public function getUser()
    {
        return $this->hasOne(Login::className(),['id'=>'id_user']);
    }
    public function getComments()
    {
        return $this->hasOne(Comment::className(),['id'=>'comment']);
    }


    /**
     * @return false|int|void
     * Удаляем
     */
    public function delete(){
        $comment = self::findOne($this->id);
        $comment->visible = 1;
        $comment->save();

        return true;
    }


    //проверка на наличие записи по столбцу id_app
    public function existsIdApp(){
        return self::find()->where(['id_app' => $this->id_app])->exists();
    }

    public function delIdApp(){
        if ($this->existsIdApp()){
            self::deleteAll(['id_app' => $this->id_app]);
        }
        return true;
    }


    /*
     * Проверка заявки на коментарии
     */
    public static function appComment($id)
    {
        return AppComment::find()->where(['id_app' => $id, 'id_user' => Yii::$app->user->id])->exists();
    }
    /*
     * Вывод все комментарии заявки.
     * type - тип комменатрия
     * type = 1, коментрий по проекту
     */
    public static function commentList($id, $type = null){
        if ($type){
            $model = AppComment::find()
                ->where(['id_app' => $id])
                ->andWhere(['type' => 1])
                ->orderBy(['date' => SORT_DESC])
                ->joinWith(['comments','user' => function($q) {$q->select(['id', 'login', 'username']);}])
                ->andFilterWhere(['is', 'appComment.visible', new \yii\db\Expression('null')])
                ->all();
        }else{
            $model = AppComment::find()
                ->where(['id_app' => $id])->orderBy(['date' => SORT_DESC])
                ->andFilterWhere(['is', 'type', new \yii\db\Expression('null')])
                ->joinWith(['comments','user' => function($q) {$q->select(['id', 'login', 'username']);}])
                ->andFilterWhere(['is', 'appComment.visible', new \yii\db\Expression('null')])
                ->all();
        }
        return $model;
    }

    /**
     * @param $id
     * @return array|bool
     * Коментарии по АйДи заявки
     */
    public static function getCommentByApp($id, $type = null){
        $result = array();  $i = 0;

        $query = AppComment::find()->where(['id_app' => $id]);

        $count = $query->count();

        if($count > 0 ){


            $query = AppComment::find()->where(['id_app' => $id])
                ->joinWith(['user' => function($q){$q->select(['id','username']);}])
                ->joinWith(['comments'])
                ->orderBy(['date' => SORT_DESC]);

            if ($type != 1){
                $comment =  $query
                    ->andFilterWhere(['=', 'user_visible', 1])
                    ->all();
            }else{
                $comment =  $query
                    ->andFilterWhere(['=', 'appComment.type', 1])
                    ->all();
            }

            foreach($comment as $c){
                $result[$i]['id'] =  $c->id;
                $result[$i]['username'] =  $c->user->username;
                $result[$i]['comment'] =  $c->comments->name;
                $result[$i]['api_login'] =  $c->api_login;
                $result[$i]['date'] =  MyDate::getDate($c->date);
                $result[$i]['strtotime'] =  $c->date;
                $result[$i]['id_app'] =  $c->id_app;
                $i++;
            }
        }
        if($result){
            return [
                'status' => true,
                'count' => $count,
                'data' => $result
            ];
        }else{
            return [
                'status' => 0,
            ];
        }
    }

    /**
     * @param $id_app
     * Количество комментариев на заявке
     */
    public static function getCount($id_app){
        return AppComment::find()->where(['id_app' => $id_app])->count();
    }

    /*
     * вывод автора записи(коментарий/напоминиание)
     */
    public function getAuthor($date, $username, $login, $comment){
         if(isset($_SESSION['User']['settings_comment'])){
            echo "<td class='comment-main'>
                <small style='background: #E4E4E4; width:200px !important;'>
                    <div style='display: inline-block' class='comment-date'>".MyDate::getDate($date)."</div>
                    <strong class='comment-username'>".$username."</strong>
                </small>";
                echo nl2br($comment);
            echo "</td>";
         }else{
            echo "<td><small style='background: #E4E4E4;'>".MyDate::getDate($date)."."."<strong>".$login."</strong></small>".nl2br($comment)."</td>";
         }
    }

    /*
     * Добовляем новый коментарий
     */
    public function commentAdd(){

        $model = new AppComment();

        $model->id_app = $this->id_app;
        $model->comment = Comment::getId(Html::encode($this->comment));
        $model->id_user = Yii::$app->user->id;
        $model->date = MyDate::getTimestamp(date('Y-m-d H:i:s'));
        $model->type = $this->type ? $this->type : null;

        return $model->getSave('Запись добавлена');
    }

    public function getSave($message = null){
        $result = false;
        try {
            if ($this->save()) {
                $result = true;
            } else {
                $error = '';
                foreach ($this->errors as $key => $value) {
                    $error .= $value[0];
                }
                $result = false; $message = $error;
            }
        } catch (\Exception $ex) {
            $message = 'Ошибка записи.' . $ex->getMessage();
            $result = false;
        }

        return [$result, $message, $this];
    }
}
