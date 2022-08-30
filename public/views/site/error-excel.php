<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 17.12.2021
     * Time: 15:01
     */









    ?>



<div class="row justify-content-md-center">
    <div class="col-11">
        <table class="table table-bordered table-striped table-sm fs-14">
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Имя файла</th>
                    <th>Расположение</th>
                    <th>Описание</th>
                    <th>Подразделение</th>
                </tr>
            </thead>

            <?php foreach($model as $item): ?>
                <tr>
                    <td><?= $item->t1 ?></td>
                    <td><?= $item->t4 ?></td>
                    <td><?= $item->t5 ?></td>
                    <td><?= $item->t6 ?></td>
                    <td><?= $item->t7 ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>
