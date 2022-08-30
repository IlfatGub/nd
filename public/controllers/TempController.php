<?php

    namespace app\controllers;

    use app\models\Sitdesk;
    use app\models\Temp;
    use Yii;

    class TempController extends BehaviorController
    {

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


        public function actionAddUri($uri = null)
        {
            $temp = new Temp();
            $temp->t1 = $uri;
            $temp->type = $temp::TYPE_API_URI;
            $temp->setTemp();

            return \app\components\temp\TempView::widget(['type' => 2]);
        }


        public function actionDelete($id)
        {
            $temp = new Temp();
            $temp->id = $id;
            $temp->del();
        }

        public function actionReport()
        {
            $res = [];

            $all_count = 0;

            try {
                $date1 = new \DateTime("2022-05-01");
                $date2 = new \DateTime(date('Y-m-d'));
                $interval = $date1->diff($date2);
                $days = $interval->days;echo "<br>";

                for ($i = 0; $i <= $days; $i++) {
                    $date = date('Y-m-d', strtotime('now -' . $i . ' day'));
                    $date_to = strtotime(date('Y-m-d 00:00:00', strtotime('now -' . $i . ' day')));
                    $date_do = strtotime(date('Y-m-d 23:23:59', strtotime('now -' . $i . ' day')));
                    $all_count = 0;

                    $file_type = Temp::find()->select(['COUNT(*) as t1', 't7', 't4'])
                        ->andWhere(['>=', 'date', $date_to])
                        ->andWhere(['<=', 'date', $date_do])
                        ->andWhere(['type' => 4])->groupBy(['t7', 't4'])->asArray()->all();

                    $all = Temp::find()->select(['COUNT(*) as t1', 't7'])
                        ->andWhere(['>=', 'date', $date_to])
                        ->andWhere(['<=', 'date', $date_do])
                        ->andWhere(['type' => 4])->groupBy(['t7'])->asArray()->all();


                    $all_share = Temp::find()->select(['COUNT(*) as t1', 't7'])
                        ->andWhere(['>=', 'date', $date_to])
                        ->andWhere(['<=', 'date', $date_do])
                        ->andWhere(['type' => 4, 't13' => 1])->groupBy(['t7'])->asArray()->all();

                    $status = Temp::find()->select(['COUNT(*) as t1', 't7', 't9'])
                        ->andWhere(['>=', 'date', $date_to])
                        ->andWhere(['<=', 'date', $date_do])
                        ->andWhere(['type' => 4])->groupBy(['t7', 't9'])->asArray()->all();

                    foreach ($all as $item) {
                        $res[$date][$item['t7']]['Всего по подразделению'] = $item['t1'];
                        $res[$date]['all']['Всего по подразделению'] += $item['t1'];

                    }

                    foreach ($status as $item) {
                        $_st = $item['t9'] == 1 ? 'Согласовано' : '';
                        $_st = $item['t9'] == 2 ? 'Не согласовано' : $_st;
                        $_st = !isset($item['t9']) ? 'На рассмотрении' : $_st;

                        $res[$date][$item['t7']]['Статус'][$_st] = $item['t1'];
                        $res[$date]['all'][$_st] += $item['t1'];
                    }

                    foreach ($file_type as $item) {
                        if ($item['t4']) {

                            $_type = $item['t4'] == 1 ? "Локальные(ПК). Рабочие файлы" : '';
                            $_type = $item['t4'] == 2 ? "Файловый сервер. Файл до 2022 года" : $_type;
                            $_type = $item['t4'] == 3 ? "Файловый сервер. Файл 2022 года" : $_type;
                            $_type = $item['t4'] == 4 ? "Локальные(ПК). Личные файлы" : $_type;

                            $res[$date][$item['t7']]['Расположение файла'][$_type] = $item['t1'];
                            $res[$date]['all'][$_type] += $item['t1'];

                        }
                    }

                    foreach ($all_share as $item) {
                        $res[$date][$item['t7']]['Всего обработано'] = $item['t1'];
                        $res[$date]['all']['Всего обработано'] += $item['t1'];

                    }
                }

            } catch (\Exception $ex) {
                echo "<pre>";
                print_r($ex);
                echo "</pre>";
            }

            return $this->render('report', [
                'model' => $res,
            ]);
        }



        public function actionReportUser()
        {
            $res = [];

            $all_count = 0;

            try {
                $date1 = new \DateTime("2022-04-28");
                $date2 = new \DateTime(date('Y-m-d'));
                $interval = $date1->diff($date2);
                $days = $interval->days;echo "<br>";

                for ($i = 0; $i <= $days; $i++) {
                    $date = date('Y-m-d', strtotime('now -' . $i . ' day'));
                    $date_to = strtotime(date('Y-m-d 00:00:00', strtotime('now -' . $i . ' day')));
                    $date_do = strtotime(date('Y-m-d 23:23:59', strtotime('now -' . $i . ' day')));
                    $all_count = 0;

                    $file_type = Temp::find()->select(['COUNT(*) as t1', 't12'])
                        ->andWhere(['>=', 'date_upd', $date_to])
                        ->andWhere(['<=', 'date_upd', $date_do])
                        ->andWhere(['t10' => 1])
                        ->andWhere(['type' => 4])->groupBy(['t12'])->asArray()->all();


                    foreach ($file_type as $item) {
                        $res[$date][$item['t12']] = $item['t1'];
                        $res['all'][$item['t12']] += $item['t1'];
                    }
//


//                    foreach ($status as $item) {
//                        $_st = $item['t9'] == 1 ? 'Согласовано' : '';
//                        $_st = $item['t9'] == 2 ? 'Не согласовано' : $_st;
//                        $_st = !isset($item['t9']) ? 'На рассмотрении' : $_st;
//
//                        $res[$date][$item['t7']]['Статус'][$_st] = $item['t1'];
//                        $res[$date]['all'][$_st] += $item['t1'];
//                    }
//
//                    foreach ($file_type as $item) {
//                        if ($item['t4']) {
//
//                            $_type = $item['t4'] == 1 ? "Локальные(ПК). Рабочие файлы" : '';
//                            $_type = $item['t4'] == 2 ? "Файловый сервер. Файл до 2022 года" : $_type;
//                            $_type = $item['t4'] == 3 ? "Файловый сервер. Файл 2022 года" : $_type;
//                            $_type = $item['t4'] == 4 ? "Локальные(ПК). Личные файлы" : $_type;
//
//                            $res[$date][$item['t7']]['Расположение файла'][$_type] = $item['t1'];
//                            $res[$date]['all'][$_type] += $item['t1'];
//
//                        }
//                    }
//
//                    foreach ($all_share as $item) {
//                        $res[$date][$item['t7']]['Всего обработано'] = $item['t1'];
//                        $res[$date]['all']['Всего обработано'] += $item['t1'];
//
//                    }
                }


            } catch (\Exception $ex) {
                echo "<pre>";
                print_r($ex);
                echo "</pre>";
            }

            return $this->render('report-user', [
                'model' => $res,
            ]);
        }


        public function actionSendMail()
        {
            $sit = new Sitdesk();

            $responsibles = Temp::find()->select(['t8'])
                ->where(['type' => 4])
                ->andwhere(['is', 't10', null])
                ->andWhere(['is', 't11', null])
                ->distinct()->column();


            foreach ($responsibles as $responsible) {
                if (strlen($responsible) > 3) {
                    $url = $sit::SUPPORT_GET_MAIL . '/?username=' . $responsible;

                    $user_mail = json_decode($sit->curl($url));
                    if ($user_mail->status) {

                        $files = Temp::find()->select(['id'])
                            ->where(['type' => 4])
                            ->andWhere(['is', 't11', null])
                            ->andWhere(['t8' => $responsible])
                            ->distinct()->column();

                        Temp::updateAll(['t11' => 1], ['in', 'id', $files]);

                        $message = sprintf('Новые файлы на согласовании: %s', count($files));
                        $message .= '<br><br>Ссылка для согласования:';
                        $message .= '<br>http://support.zsmik.com/filedata';
                        $setSubject = 'Helpdesk. Оповещение';

                        Yii::$app->mailer->compose()
                            ->setFrom('ticket@nhrs.ru')
                            ->setTo($user_mail->data)
                            ->setCc(["ticket@nhrs.ru"])
                            ->setSubject($setSubject)
                            ->setHtmlBody($message)
                            ->send();

                    }
                }
            }

        }
    }


