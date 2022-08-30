<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 23.01.2019
     * Time: 15:36
     */

    use app\modules\admin\models\App;
    use app\modules\admin\models\AppSearch;
    use app\modules\admin\models\Comment;
    use app\modules\admin\models\Problem;
    use kartik\date\DatePicker;
    use kartik\export\ExportMenu;
    use kartik\widgets\Select2;
    use yii\base\Controller;
    use yii\grid\GridView;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $text = '';
    /**
     * @var  $dataProvider App;
     * @var  $searchModel  AppSearch ;
     */

    $_user = ArrayHelper::map(\app\modules\admin\models\Login::getLoginList(), 'id', 'username');
    $_podr = ArrayHelper::map(\app\modules\admin\models\Podr::getList(), 'id', 'name');

    $add_user = ['ss' => 'Сектор связи', 'sit' => 'СИТ', 'sap' => 'КИС'];
    $_user = $add_user + $_user;

    $id_user = isset($_GET['id_user']) ? $_GET['id_user'] : '';
    $id_podr = isset($_GET['id_podr']) ? $_GET['id_podr'] : '';
    $id_depart = isset($_GET['id_depart']) ? $_GET['id_depart'] : '';

    $id_class = isset($_GET['id_class']) ? $_GET['id_class'] : '';
    $id_object = isset($_GET['id_object']) ? $_GET['id_object'] : '';
    $id_problem = isset($_GET['id_problem']) ? $_GET['id_problem'] : '';
    $id_buh = isset($_GET['id_buh']) ? $_GET['id_buh'] : '';

    $id_username = isset($_GET['id_username ']) ? $_GET['id_username '] : '';
    $id_status = isset($_GET['id_status']) ? $_GET['id_status'] : '';
    $stupid = isset($_GET['stupid']) ? $_GET['stupid'] : '';

    $status = \app\modules\admin\models\Status::getStatusReport();

    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    $date_do = isset($_GET['date_do']) ? $_GET['date_do'] : date('Y-m-d');

    $date_cl_to = isset($_GET['date_cl_to']) ? $_GET['date_cl_to'] : null;
    $date_cl_do = isset($_GET['date_cl_do']) ? $_GET['date_cl_do'] : null;

    $pr_parent = ArrayHelper::map(Problem::getListAll(), 'id', 'parent_id');
    $pr_name = ArrayHelper::map(Problem::getListAll(), 'id', 'name');

    $pr_class = ArrayHelper::map(Problem::getProblemMain(), 'id', 'name');
    $pr_object = isset($id_class) ? ArrayHelper::map(Problem::getProblemMain($id_class), 'id', 'name') : null;
    $pr_problem = isset($id_object) ? ArrayHelper::map(Problem::getProblemMain($id_object), 'id', 'name') : null;
    $buh = \app\modules\admin\models\Buh::getList();

    $api_podr = \app\models\Depart::url($id_podr);
    if (isset($api_podr->Result))
        $api_depart = ArrayHelper::map($api_podr->Result, 'ID', 'subdivision');


    if ($id_object & $id_class) {
        $pr_filter = Html::dropDownList('id_problem', $id_problem, $pr_problem, ['class' => 'form-control', 'prompt' => '...']);
    } else {
        $pr_filter = Select2::widget([
            'name' => 'id_problem',
            'value' => $id_problem,
            'options' => ['placeholder' => '...'],
            'data' => array_unique(ArrayHelper::map(Problem::getProblemMainAll(), 'name', 'name')),
            'disabled' => false
        ]);
    }


?>

<style>
    #nav2, #nav2 li {
        margin: 0;
        padding: 0;
    }

    #nav2 {
        background: rgb(46, 95, 122); /* цвет фона */
    }

    #nav2 li {
        display: inline-block;
        text-align: center; /* текст горизонтально по центру */
    }

    #nav2 a {
        display: block; /* ссылка растягивается на весь пункт li */
        padding: 3px 15px;
        color: #fff; /* цвет текста */
        text-decoration: none; /* убрать нижнее подчёркивание у ссылок */
    }

    #nav2 a:hover {
        background: rgb(96, 145, 172); /* фон пунктов при наведении */
    }

    .select2-container--krajee .select2-selection {
        font-size: 10pt;
    }
</style>


<div class="row  justify-content-md-center">
    <div class="col-12 ml-2 fs-10">
        <?php

            $column = [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'id',
                    'contentOptions' => ['style' => 'width:50px; white-space: normal;'],
                ],
                [
                    'attribute' => 'status',
                    'contentOptions' => ['style' => 'width:50px; white-space: normal;'],
                    'filter' => Html::dropDownList('id_status', $id_status, $status, ['class' => 'form-control', 'prompt' => '...']),
                    'value' => function ($model) {
                        return \app\modules\admin\models\Status::getStatus()[$model->status];
                    },
                ],
                [
                    'attribute' => 'stupid',
                    'contentOptions' => ['style' => 'width:50px; white-space: normal;'],
                    'filter' => Html::dropDownList('stupid', $stupid, [1 => "Лишняя заявка"], ['class' => 'form-control', 'prompt' => '...']),
                    'value' => function ($model) {
                        return $model->stupid == 1 ? 'Лишняя заявка' : '';
                    },
                ],
                [
                    'attribute' => 'podr.name',
                    'header' => 'Организация',
                    'contentOptions' => ['style' => 'width:100px; white-space: normal;'],
                    'filter' => Html::dropDownList('id_podr', $id_podr, $_podr, ['class' => 'form-control', 'prompt' => '...']),
                ],
                [
                    'attribute' => 'date_ct',
                    'contentOptions' => ['style' => 'width:150px; white-space: normal;'],
                    'filter' => DatePicker::widget([
                        'name' => 'date_to',
                        'value' => $date_to,
                        'type' => DatePicker::TYPE_RANGE,
                        'name2' => 'date_do',
                        'value2' => $date_do,
                        'options' => ['autocomplete' => 'off'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'class' => 'form-control form-control-sm',
                            'autocomplete' => 'off '
                        ],
                        'pluginEvents' => [
                            "changeDate" => "function(e) {  $('#refreshButton').click();  }",
                        ],
                    ]),
                    'value' => function ($model) {
                        return date('Y-m-d h:i:s', $model->date_ct);
                    },
//                    'filter' => Html::dropDownList('id_org', $id_org,$_org, ['class' => 'form-control form-control-sm', 'prompt' => '...']),
                ],
                [
                    'attribute' => 'appContent.date_cl',
                    'filter' => DatePicker::widget([
                        'name' => 'date_cl_to',
                        'value' => $date_cl_to,
                        'type' => DatePicker::TYPE_RANGE,
                        'name2' => 'date_cl_do',
                        'value2' => $date_cl_do,
                        'options' => ['autocomplete' => 'off'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'class' => 'form-control form-control-sm',
                        ],
                        'pluginEvents' => [
                            "changeDate" => "function(e) {  $('#refreshButton').click();  }",
                        ],
                    ]),
                    'contentOptions' => ['style' => 'width:150px; white-space: normal;'],
                    'value' => function ($model) {
                        return isset($model->appContent->date_cl) ? date('Y-m-d h:i:s', $model->appContent->date_cl) : null;
                    },
//                    'filter' => Html::dropDownList('id_org', $id_org,$_org, ['class' => 'form-control form-control-sm', 'prompt' => '...']),
                ],
                [
                    'attribute' => 'user.username',
                    'header' => 'ФИО исполнителя',
                    'contentOptions' => ['style' => 'width:250px; white-space: normal;'],
                    'filter' => Html::dropDownList('id_user', $id_user, $_user, ['class' => 'form-control', 'prompt' => '...']),
                ],
                [
                    'attribute' => 'appContent.dv',
                    'header' => 'С/з',
                    'contentOptions' => ['style' => 'width:250px; white-space: normal;'],
//                    'filter' => Html::dropDownList('id_user', $id_user, $_user, ['class' => 'form-control', 'prompt' => '...']),
                ],
                [
                    'attribute' => 'appContent.fio.name',
                    'header' => 'ФИО пользователя',
                    'contentOptions' => ['style' => 'width:250px; white-space: normal;'],

                    'filter' => Html::textInput('id_username', '', ['class' => 'form-control', 'prompt' => '...']),
                ],
                [
                    'attribute' => 'appContent.buh',
                    'header' => 'Система 1с',
                    'contentOptions' => ['style' => 'width:250px; white-space: normal;'],
                    'filter' => Html::dropDownList('id_buh', $id_buh, $buh, ['class' => 'form-control', 'prompt' => '...']),
                    'value' => function ($model) {
                        return isset($model->appContent->buhg->name) ? $model->appContent->buhg->name : null;
                    },
                ],
                [
                    'attribute' => 'problem.name',
                    'header' => 'Класс проблемы',
                    'contentOptions' => ['style' => 'width:250px; white-space: normal;'],
                    'filter' => Html::dropDownList('id_class', $id_class, $pr_class, ['class' => 'form-control', 'prompt' => '...']),
                    'value' => function ($model) {
                        return $model->id_class_name;
                    },
                ],

                [
                    'attribute' => 'problem.name',
                    'header' => 'Предмет проблемы',
                    'contentOptions' => ['style' => 'width:250px; white-space: normal;'],
                    'filter' => $id_class ? Html::dropDownList('id_object', $id_object, $pr_object, ['class' => 'form-control', 'prompt' => '...']) : null,
                    'value' => function ($model) {
                        return $model->id_object_name;
                    },
                ],

                [
                    'attribute' => 'problem.name',
                    'header' => 'Тип проблемы',
                    'contentOptions' => ['style' => 'width:250px; white-space: normal;'],

                    'filter' => $pr_filter,
//                    'filter' => Html::dropDownList('type', $type, $_type, ['class' => 'form-control form-control-sm', 'prompt' => '...']),
//                    'filter' => Login::getList(),
//                    'filterInputOptions' => ['class' => 'form-control form-control-sm']
                ],

                [
                    'attribute' => 'depart.name',
                    'header' => 'Отдел',
                    'contentOptions' => ['style' => 'width:400px; white-space: normal;'],
                    'filter' => $id_podr ? Html::dropDownList('id_depart', $id_depart, $api_depart, ['class' => 'form-control', 'prompt' => '...']) : null,
//                    'filter' => Html::dropDownList('fio', $fio, $_fio, ['class' => 'form-control form-control-sm', 'prompt' => '...']),
//                    'filter' => Login::getList(),
//                    'filterInputOptions' => ['class' => 'form-control form-control-sm']
                ],

                [
                    'attribute' => 'appContent.content',
                    'header' => 'Описание',
                ],
                [
                    'attribute' => 'appComment.text',
                    'header' => 'Комменатрии',
                    'value' => function ($model) {
                        $text = '';
                        foreach ($model->appComment as $comment){
                            $text .= date('Y-m-d H:i', $comment->date).PHP_EOL.$comment->text.PHP_EOL.PHP_EOL;
                        }
                        return $text;
                    },
                ],
            ];



            $gridColumns = [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'id',
                    'header' => 'Номер заявки',
                ],
                [
                    'attribute' => 'podr.name',
                    'header' => 'Организация',

                ],
                [
                    'attribute' => 'date_ct',
                    'header' => 'Дата создания',
                    'value' => function ($model) {
                        return date('Y-m-d H:i:s', $model->date_ct);
                    },
                ],
                [
                    'attribute' => 'appContent.date_cl',
                    'header' => 'Дата закрытия',
                    'value' => function ($model) {
                        return isset($model->appContent->date_cl) ? date('Y-m-d H:i:s', $model->appContent->date_cl) : '';
                    },
                ],
                [
                    'attribute' => 'user.username',
                    'header' => 'ФИО исполнителя',

                ],
                [
                    'attribute' => 'appContent.dv',
                    'header' => 'С/з',
                ],
                [
                    'attribute' => 'appContent.fio.name',
                    'header' => 'ФИО пользователя',
                ],
                [
                    'attribute' => 'problem.name',
                    'header' => 'Класс проблем',
                    'value' => function ($model) {
                        return $model->id_class_name;
                    },
                ],
                [
                    'attribute' => 'problem.name',
                    'header' => 'Предмет проблемы',
                    'value' => function ($model) {
                        return $model->id_object_name;
                    },
                ],
                [
                    'attribute' => 'problem.name',
                    'header' => 'Тип проблемы',

                ],
                [
                    'attribute' => 'depart.name',
                    'header' => 'Отдел',
                ],
                [
                    'attribute' => 'appContent.content',
                    'header' => 'Описание',
                ],
                [
                    'attribute' => 'appComment.text',
                    'header' => 'Комменатрии',
                    'value' => function ($model) {
                        $text = '';
                        foreach ($model->appComment as $comment){
                            if ($comment->text){
                                $text .= date('Y-m-d H:i', $comment->date).PHP_EOL.$comment->text.PHP_EOL;
                            }else{
                                $text .= date('Y-m-d H:i', $comment->date).PHP_EOL.Comment::findOne($comment->comment)->name.PHP_EOL;
                            }
                        }
                        return $text;
                    },
                ],
            ];




            $txt_podr = $id_podr ? ' / ' . $_podr[$id_podr] : '';
            $txt_user = $id_user ? ' / ' . $_user[$id_user] : '';
            $txt_class = $id_class ? ' / ' . $pr_class[$id_class] : '';
            $txt_object = $id_object ? ' / ' . $pr_object[$id_object] : '';
            $txt_problem = $id_problem ? ' / ' . $id_problem : '';
            $txt_problem = $id_depart ? ' / ' . $pr_problem[$id_depart] : '';
            $txt_status = $id_status ? ' / ' . $status[$id_status] : '';
            $txt_buh = $id_buh ? ' / ' . $buh[$id_buh] : '';

            $serach_text = ' / ' . $date_to . ' - ' . $date_do . ' ' . $txt_podr . ' ' . $txt_user . ' ' . $txt_class . ' ' . $txt_object . ' ' . $txt_problem . ' ' . $txt_status . ' ' . $txt_buh;





            echo '

<div class="row">
    <div class="col-11">
        <ul id="nav2" class="mb-2 p-1"><li  class="mr-5" style="color: white; font-size: 12pt; font-weight: 600"> Выгрузить </li>' .
                ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'asDropdown' => false,
                    'autoWidth' => false,
                    'onRenderSheet' => function($sheet, $widget) {


                        $sheet->getStyle('A2:'.$sheet->getHighestColumn().$sheet->getHighestRow())
                            ->getAlignment()
                            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                        $sheet->getStyle('A2:'.$sheet->getHighestColumn().$sheet->getHighestRow())
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);



                        $sheet->getStyle('A2:'.$sheet->getHighestColumn().$sheet->getHighestRow())
                            ->getAlignment()->setWrapText(true);
                        $sheet->getStyle('A2:'.$sheet->getHighestColumn().$sheet->getHighestRow())
                            ->getFont()->setSize(8);

                        $sheet->getColumnDimension('A')->setWidth(3); // not working
                        $sheet->getColumnDimension('B')->setWidth(5); // not working
                        $sheet->getColumnDimension('C')->setWidth(5); // not working
                        $sheet->getColumnDimension('D')->setWidth(10); // not working
                        $sheet->getColumnDimension('E')->setWidth(10); // not working
                        $sheet->getColumnDimension('F')->setWidth(15); // not working
                        $sheet->getColumnDimension('G')->setWidth(10); // not working
                        $sheet->getColumnDimension('H')->setWidth(10); // not working
                        $sheet->getColumnDimension('I')->setWidth(10); // not working
                        $sheet->getColumnDimension('J')->setWidth(10); // not working

                        $sheet->getColumnDimension('K')->setWidth(10); // not working
                        $sheet->getColumnDimension('L')->setWidth(40); // not working
                        $sheet->getColumnDimension('M')->setWidth(40); // not working
                        $sheet->getColumnDimension('N')->setWidth(40); // not working


                        $sheet->getPageMargins()->setTop(0,5);
                        $sheet->getPageMargins()->setRight(0,5);
                        $sheet->getPageMargins()->setLeft(0,5);
                        $sheet->getPageMargins()->setBottom(0.5);
                        $sheet->getPageMargins()->setFooter(0.5);
                        $sheet->getPageMargins()->setHeader(0.5);
                        $sheet->getPageSetup()->setFitToWidth(1);
                        $sheet->getPageSetup()->setFitToHeight(0);
                        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                    }
                ]) .
                '<li  class="mr-5 float-right" style="color: white; font-size: 12pt; font-weight: 600"> ' . $serach_text . ' </li> </ul>
    </div>
    <div class="col-1">
        ' . HTML::a('Очистить поля', [\yii\helpers\Url::to(['/site/export'])], ['class' => 'btn btn-sm btn-danger']) . '
    </div>
</div> ';

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-sm table-bordered table-hover table-striped fs-8'
                ],
                'columns' => $column
            ]);
        ?>
    </div>
</div>

<!---->
<!---->
<!---->
<?php
//
//    $i = 0;
//
//    $spreadsheet = new Spreadsheet();
//    $sheet = $spreadsheet->getActiveSheet();
//
//    $sheet->getColumnDimension('A')->setAutoSize(true);
//    $sheet->getColumnDimension('B')->setAutoSize(true);
//    $sheet->getColumnDimension('C')->setAutoSize(true);
//    $sheet->getColumnDimension('D')->setAutoSize(true);
//    $sheet->getColumnDimension('E')->setAutoSize(true);
//    $sheet->getColumnDimension('F')->setAutoSize(true);
//    $sheet->getColumnDimension('G')->setAutoSize(true);
//    $sheet->getColumnDimension('H')->setAutoSize(true);
//    $sheet->getColumnDimension('I')->setWidth(70);
//    $sheet->getColumnDimension('J')->setWidth(70);
//
//    $sheet->setAutoFilter('A1:J1');
//
//    $sheet->getStyle('I')->getAlignment()->setWrapText(true);
//    $sheet->getStyle('J')->getAlignment()->setWrapText(true);
//
//    $sheet
//        ->setCellValue('A'.$i, 'Название')
//        ->setCellValue('B'.$i, 'Инициатор')
//        ->setCellValue('C'.$i, 'Дата Планирования')
//        ->setCellValue('D'.$i, 'Куратор')
//        ->setCellValue('E'.$i, 'Дата Текущая')
//        ->setCellValue('F'.$i, 'Исполнитель')
//        ->setCellValue('G'.$i, 'База')
//        ->setCellValue('H'.$i, 'Статус')
//        ->setCellValue('I'.$i, 'Описание')
//        ->setCellValue('J'.$i, 'Коменнарий')
//    ;
//    $i++;
//
//
//    foreach ($dataProvider->getModels() as $item) {
//        $sheet
//            ->setCellValue('A'.$i, $item->id)
//            ->setCellValue('B'.$i, \app\modules\admin\models\Status::getStatus()[$model->status])
//            ->setCellValue('C'.$i, date('Y-m-d', $item->date_pl))
//            ->setCellValue('D'.$i, $hd->getFio($login[$item->user_cur]))
//            ->setCellValue('E'.$i, date('Y-m-d', $item->date_cur))
//            ->setCellValue('F'.$i, $hd->getFio($login[$item->user_exec]))
//            ->setCellValue('G'.$i, $base[$item->base])
//            ->setCellValue('H'.$i, $status[$item->status])
//            ->setCellValue('I'.$i, $item->description)
//            ->setCellValue('J'.$i, $item->comment)
//        ;
//        $i++;
//    }
//
//    --$i;
//
//    $styleArray = array(
//        'borders' => array(
//            'outline' => array(
//                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
//                'color' => array('argb' => '333333'),
//            ),
//        ),
//    );
//
//    $styleArray2= array(
//        'borders' => [
//            'allBorders' => [
//                'borderStyle' => Border::BORDER_DOTTED,
//                'color' => [
//                    'rgb' => '333333'
//                ]
//            ],
//        ],
//    );
//
//    $sheet->getStyle('A1:J1')->applyFromArray([
//        'font' => [
////            'name' => 'Arial',
//            'bold' => true,
//            'italic' => false,
//            'strikethrough' => false,
//        ],
//        'borders' => [
//            'allBorders' => [
//                'borderStyle' => Border::BORDER_MEDIUM,
//                'color' => [
//                    'rgb' => '333333'
//                ]
//            ],
//        ],
//        'alignment' => [
////            'horizontal' => Alignment::HORIZONTAL_CENTER,
////            'vertical' => Alignment::VERTICAL_CENTER,
//            'wrapText' => true,
//        ]
//    ]);
//
//
//    $sheet->getStyle('A2:H'.$i)->applyFromArray([
//        'alignment' => [
//            'horizontal' => Alignment::HORIZONTAL_CENTER,
//            'vertical' => Alignment::VERTICAL_CENTER,
//            'wrapText' => true,
//        ],
//
//    ]);
//
//    $sheet->getStyle('A2:J'.$i)->applyFromArray($styleArray2);
//    $sheet->getStyle('A1:J'.$i)->applyFromArray($styleArray);
//
//    $writer = new Xlsx($spreadsheet);
//    $writer->save('helloworld1.xlsx');
//
//    Controller::disableProfiler();
//    Yii::$app->response->sendFile('helloworld.xlsx');
//
//
//?>
<!---->
<!---->

<!---->
<?php
//
//
//        Yii::$app->response->sendFile('helloworld.xlsx');
//
//
//
//    $url = "helloworld.xlsx";
//    $file_name = basename($url);
//
//
//// Используем функцию file_get_contents () для получения файла
//// из url и используем функцию file_put_contents () для
//// сохранить файл, используя базовое имя
//
//    if(file_put_contents( $file_name,file_get_contents($url))) {
//
//        echo "File downloaded successfully";
//
//    }
//
//    else {
//
//        echo "File downloading failed.";
//
//    }
//
//    ?>