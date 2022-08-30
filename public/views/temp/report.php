<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 05.05.2022
     * Time: 10:35
     *
     * @var $model \app\models\Temp;
     */

    ?>


<style>
    .vertical-text{
        writing-mode: vertical-lr;
        width: 50px;
        text-align: right;
        vertical-align: center;
    }
    .td{
        width: 50px;
        text-align: right;
        vertical-align: center;
    }
    table thead{
        position: sticky;
        top:45px;
        background: silver;
    }
</style>

<div class="row m-2 mx-5">

    <?php foreach($model as $date => $items): ?>

    <table class="table table-sm table-bordered table-hover fs-10" style="line-height: normal">

        <thead>
        <tr class="alert-primary">
            <td colspan="10" style="font-weight: bold">Дата: <?= $date ?></td>
        </tr>
        <tr>
            <td rowspan="2">Подразделение</td>
            <td class="alert-info vertical-text" rowspan="2">Всего</td>
            <td class="alert-success vertical-text" rowspan="2">Обработано</td>
            <td colspan="3" class="alert-warning">Статус</td>
            <td colspan="4" class="alert-danger">Расположение файлов</td>
        </tr>
        <tr>
            <td class="alert-warning vertical-text" >На рассмотрении</td>
            <td class="alert-warning vertical-text" >Согласовано</td>
            <td class="alert-warning vertical-text" >Не согласовано</td>
            <td class="alert-danger vertical-text" >ПК. Раб. файлы</td>
            <td class="alert-danger vertical-text" >ПК. Лич. файлы</td>
            <td class="alert-danger vertical-text" >FS. Файл 2022</td>
            <td class="alert-danger vertical-text" >FS. Файл до 2022</td>
        </tr>

        </thead>



        <?php if($items): ?>
            <?php foreach($items as $depart => $item): ?>
                <?php if($depart == 'all'): ?>

                <?php else: ?>
                    <tr>
                        <td><?= $depart ? $depart :  ' Нет данных ' ?></td>
                        <td class=" td alert-info" style="text-align: center"><?= isset($item['Всего по подразделению']) ? $item['Всего по подразделению'] : '' ?></td>
                        <td class=" td alert-success" style="text-align: center"><?=isset( $item['Всего обработано']) ?  $item['Всего обработано'] : '' ?></td>
                        <td class=" td alert-warning" style="text-align: center"><?= isset($item['Статус']['На рассмотрении']) ? $item['Статус']['На рассмотрении'] : '-' ?></td>
                        <td class=" td alert-warning" style="text-align: center"><?= isset($item['Статус']['Согласовано']) ? $item['Статус']['Согласовано'] : '-' ?></td>
                        <td class=" td alert-warning" style="text-align: center"><?= isset($item['Статус']['Не согласовано']) ? $item['Статус']['Не согласовано'] : '-' ?></td>
                        <td class=" td alert-danger" style="text-align: center"><?= isset($item['Расположение файла']['Локальные(ПК). Рабочие файлы'])? $item['Расположение файла']['Локальные(ПК). Рабочие файлы'] : '-' ?></td>
                        <td class=" td alert-danger" style="text-align: center"><?= isset($item['Расположение файла']['Локальные(ПК). Личные файлы']) ? $item['Расположение файла']['Локальные(ПК). Личные файлы'] : '-' ?></td>
                        <td class=" td alert-danger" style="text-align: center"><?= isset($item['Расположение файла']['Файловый сервер. Файл 2022 года']) ? $item['Расположение файла']['Файловый сервер. Файл 2022 года'] : '-' ?></td>
                        <td class=" td alert-danger" style="text-align: center"><?= isset($item['Расположение файла']['Файловый сервер. Файл до 2022 года']) ? $item['Расположение файла']['Файловый сервер. Файл до 2022 года'] : '-' ?></td>
                    </tr>
                <?php endif; ?>

            <?php endforeach; ?>

            <tr>
                <td><?= 'Всего' ?></td>
                <td class="alert-info" style="text-align: center"><?= isset($items['all']['Всего по подразделению']) ? $items['all']['Всего по подразделению'] : ''  ?></td>
                <td class="alert-success" style="text-align: center"><?= isset($items['all']['Всего обработано']) ? $items['all']['Всего обработано'] : ''  ?></td>
                <td class="alert-warning" style="text-align: center"><?= isset($items['all']['На рассмотрении']) ? $items['all']['На рассмотрении'] : '-' ?></td>
                <td class="alert-warning" style="text-align: center"><?= isset($items['all']['Согласовано']) ? $items['all']['Согласовано'] : '-' ?></td>
                <td class="alert-warning" style="text-align: center"><?= isset($items['all']['Не согласовано']) ? $items['all']['Не согласовано'] : '-' ?></td>
                <td class="alert-danger" style="text-align: center"><?= isset($items['all']['Локальные(ПК). Рабочие файлы'])? $items['all']['Локальные(ПК). Рабочие файлы'] : '-' ?></td>
                <td class="alert-danger" style="text-align: center"><?= isset($items['all']['Локальные(ПК). Личные файлы']) ? $items['all']['Локальные(ПК). Личные файлы'] : '-' ?></td>
                <td class="alert-danger" style="text-align: center"><?= isset($items['all']['Файловый сервер. Файл 2022 года']) ? $items['all']['Файловый сервер. Файл 2022 года'] : '-' ?></td>
                <td class="alert-danger" style="text-align: center"><?= isset($items['all']['Файловый сервер. Файл до 2022 года']) ? $items['all']['Файловый сервер. Файл до 2022 года'] : '-' ?></td>
            </tr>
        <?php endif; ?>


    </table>
    <?php endforeach; ?>

</div>

