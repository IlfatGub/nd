<?php

    use app\modules\admin\models\Help;

?>


<?php if (isset($app) and (Yii::$app->user->can('Admin') or Yii::$app->user->can('Disp'))) : ?>
    <div class="col-lg-12 col-md-12 col-xs-12 bg-sitdesk-block block-help my-2">

        <button type="button" id="app-help-clear" class="btn btn-sm btn-danger py-1 px-1 mb-1 app-help-clear">Очистить
        </button>

        <?php foreach ($help as $item) : ?>
            <?php if (Help::validateParent($item->id)) : ?>
                <?php $problem = $item->problem ?>
                <div class="btn-group">
                    <button type="button" id="<?= $problem ?>"
                            class="btn btn-sm btn-outline-info py-1 px-1 mb-1 app-help-disp "><?= Help::getNameById($item->id) ?></button>
                    <button type="button"
                            class="btn btn-sm btn-info py-1 px-1 mb-1 dropdown-toggle dropdown-toggle-split"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                        <?php foreach (Help::getHelpByParentId($item->id) as $item) : ?>
                            <a class="dropdown-item app-help-disp" id="<?= $item->problem ?>"
                               href="#"><?= $item->name ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <button type="button"
                        class="btn btn-sm btn-outline-primary py-1 px-1 mb-1 app-help-disp "><?= $item->name ?></button>
            <?php endif ?>


        <?php endforeach ?>
    </div>
<?php endif ?>