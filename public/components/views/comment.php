<?php
    /**
     * User: 01gig
     * Блок комментарий, добавляем коментарий на заявку
     * Добавить коментарий инженера могут только на свою завявку
     * Админы и диспетчер может добавлять коментарий ко всем заявкам
     */



    /**
     * This is the model class for table "app".


     * @var $comment \app\modules\admin\models\AppComment
     * @var $type
     *
     */

    use app\modules\admin\models\MyDate;
    use app\modules\admin\models\Login;
    use yii\helpers\Html;

    $id_user = Yii::$app->user->id;
?>

<?php if ($_GET) {
    if (($_GET['id']) and !isset($_GET['app'])) { ?>
        <div class="col-lg-12 col-md-12 col-xs-12 bg-sitdesk-block block-comment">

            <?php if(!$type): ?>
            <div class="row" style="padding-top: 10px">
                <div class="col-10">
                    <textarea type="text" id="textComment" rows="1"  class="form-control input-sm input app-input-comment"  placeholder="Комментарий"></textarea>
                </div>
                <div>
                    <?php if (!Yii::$app->user->can('DispQuest')): ?>

                    <div class="btn-group">
                        <button id="btnComment" value="<?= $_GET['id'] ?>" class="btn btn-success btn-sm">
                            <span class="fas fa-plus"></span>
                        </button>

                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm  dropdown-toggle" data-toggle="dropdown"> </button>

                            <ul class="dropdown-menu" id="olComment">
                                <?php foreach (\app\models\Sitdesk::CommList(Yii::$app->user->identity->comment_list) as $item) { ?>
                                    <li value="<?= $_GET['id'] ?>"><a class="dropdown-item"
                                                                      href="#"><?= trim($item) ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif ?>

                </div>
            </div>
            <?php endif; ?>

            <?php if($comment): ?>
                <div class="row">
                    <div class="col-12">
                        <table id="tableComment" class="table table-hover table-condensed table-sm pb-0 mb-2" style="font-size: 10pt">
                            <?php foreach ($comment as $item) { ?>
                                <tr id="comment<?= $item->id ?>">
                                    <td class="comment-main py-1 px-1 ">

                                    <?php if(!$type){ ?>
                                        <small style=" max-width: 200px;">
                                            <?php $checked = $item->user_visible == 1 ? 'checked' : '' ?>

                                            <label class="switch float-right" title='Включаем комментарий для пользователя'>
                                                <input type="checkbox" class="hd-checkbox" <?= $checked ?> class="success comment-view" id="<?= $item->id ?>">
                                            </label>

                                            <?php if ($id_user == $item->id_user): ?>
                                                <?= Html::a('<span class="fas fa-window-close text-danger "></span>', ['/site/comment', 'delete' => $item->id,], ['data' => ['confirm' => 'Удалить?']]); ?>
                                            <?php endif; ?>

                                            <?= MyDate::getDate($item->date) ?>.

                                            <?php if ($item->id_user != 50): ?>
                                                <strong><?= \app\models\Sitdesk::fio(Login::Fio($item->id_user)) ?> : </strong>
                                                <br>
                                            <?php endif; ?>
                                        </small>

                                        <?php }else{ ?>

                                            <small >
                                                <?php if ($id_user == $item->id_user or Yii::$app->user->id == 1): ?>
                                                    <?= Html::a('<span class="fas fa-window-close text-danger "></span>', ['/site/comment', 'delete' => $item->id,], ['data' => ['confirm' => 'Удалить?']]); ?>
                                                <?php endif; ?>

                                                <?= MyDate::getDate($item->date) ?>.
                                                <?php if ($item->id_user != 50): ?>
                                                    <strong><?= \app\models\Sitdesk::fio(Login::Fio($item->id_user)) ?>
                                                        : </strong>
                                                    <br>
                                                <?php endif; ?>
                                            </small>

                                        <?php } ?>

                                        <?= nl2br($item->comments->name) ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php }
} ?>