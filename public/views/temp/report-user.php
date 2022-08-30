<?php
    /**
     * Created by PhpStorm.
     * User: 01gig
     * Date: 10.06.2022
     * Time: 9:45
     * @var $model \app\models\Temp;
     */
    ?>



<div class="row m-2 mx-5">
    <table class="table table-sm col-4 table-hover table-bordered fs-10">
        <?php foreach($model as $date => $items): ?>
            <?php if($date != 'all'): ?>
                <tr class="alert alert-info">
                    <td colspan="2"><?= $date ?></td>
                </tr>

                <?php foreach($items as $username => $count): ?>
                    <tr>
                        <td><?= $username ?></td>
                        <td><?= $count ?></td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <td colspan="2"> </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>


        <tr class="alert alert-warning">
            <td colspan="2">Всего </td>
        </tr>

        <?php foreach($model['all'] as $username => $count): ?>
            <tr>
                <td><?= $username ?></td>
                <td><?= $count ?></td>
            </tr>
        <?php endforeach; ?>

    </table>
</div>


