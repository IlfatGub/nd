<?php

use app\components\CommentWidget;
use app\components\template\StatusTable;
use app\components\TopFormWidget;
use app\models\AppProject;
use app\models\Sitdesk;
use app\modules\admin\models\App;
use app\modules\admin\models\AppComment;
use app\modules\admin\models\AppSearchHistory;
use app\modules\admin\models\Buh;
use app\modules\admin\models\Status;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\typeahead\TypeaheadBasic;
use app\modules\admin\models\Fio;
use app\modules\admin\models\Login;
use app\modules\admin\models\Problem;
use app\modules\admin\models\Priority;
use app\modules\admin\models\Podr;
use yii\helpers\Url;
use app\components\AssistsWidget;
use app\components\RecalWidget;
use kartik\widgets\Select2;
use kartik\file\FileInput;
use yii\bootstrap4\Modal;

/**
 *
 * Отделы, заполняеться через 1с
 *
 * @var $model App
 *
 */

$block_color = 'bg-sitdesk-block';

date_default_timezone_set('Asia/Yekaterinburg');
if (isset($_GET['app'])) {
	$model->isNewRecord = $_GET['app'] == 1 ? 1 : 0;
	$model->id_podr = '';
	$model->id_problem = '';
	$model->id_user = '';
}


$id_user = Yii::$app->user->id;

//    Получаем учетку из support
//    if (Yii::$app->user->id == 1){
//        echo Sitdesk::getMailFromSupport($model->api_login);
//    }


$login_list = Login::getList();

if (Yii::$app->user->can('Admin') or Yii::$app->user->can('AdminProject') or Yii::$app->user->can('TicketRedactor') or Yii::$app->user->can('Disp')) {
	$login_list = Login::getList();
} else {
	$login_list = [$model->id_user => Login::findOne($model->id_user)->username];
}

if (!array_key_exists($model->id_user, $login_list)) {
	$login_list[$model->id_user] = Login::findOne($model->id_user)->username;
}




?>

<?php if (Yii::$app->user->can('Admin') or Yii::$app->user->can('Disp')) {
	$disabled = false;
} else {
	$disabled = false;
} ?>


<?php $app = isset($_GET['app']) ? $_GET['app'] : null; ?>
<?php $type = $model->type; ?>

<div style="padding-left:30px;">

	<!--        Общая информация по заявке -->
	<div class="inline-block" style="vertical-align: top">
		<div class="block-sitdesk-info inline-block">

			<div id="sitdesk-notify" class="sitdesk-notify">
				<?php \app\models\AppNotify::getNotifyActive() ?>
			</div>

			<div class="col-xl-12 col-sm-12 col-xs-12 block-app <?= $block_color ?>">

				<!--        TopForm -->
				<?= TopFormWidget::widget() ?>
				<!--        TopForm  -->

				<?php $form = ActiveForm::begin([
					'options' => ['enctype' => 'multipart/form-data'],
					'id' => 'login-form',
					'fieldConfig' => [
						'options' => ['class' => 'col-lg-121'],
						'template' => '<div >{input}</div> <span class="text-danger"> <small>{error}</small> </span>',
						'labelOptions' => ['class' => 'col-sm-2 control-label'],
					],
				]);
				$classInput = "form-control form-control-sm mb-2 app-input select2-results__options";
				$classLabel = "main-input mb-1";
				if (!isset($_GET['app'])) {
					$model->content = isset($model->appContent->content) ? $model->appContent->content : null;
					$model->fio = isset($model->appContent->fio->name) ? $model->appContent->fio->name : null;
					$model->ip = isset($model->appContent->ip) ? $model->appContent->ip : null;
					$model->phone = isset($model->appContent->phone) ? $model->appContent->phone : null;
					$model->type = isset($model->appContent->dv) ? $model->appContent->dv : null;
					$model->buh = isset($model->appContent->buh) ? $model->appContent->buh : null;
					$model->note = isset($model->appContent->note) ? $model->appContent->note : null;
				} else {
					$model->ip = '10.224.';
					$model->id_priority = 2;
				} ?>


				<?php if ($type <> $model::TYPE_PROJECT and $type <> $model::TYPE_PROJECT_TICKET) { ?>


					<div class="row">
						<div class="col-6 ">
							<label class="<?= $classLabel ?>"> <?= Html::button('ФИО', ['value' => Url::to(['logs']), 'id' => 'modalFio', 'class' => ' api-link btn btn-sm py-0 px-0 btn-link input_modal']) ?> </label>
							<label class="<?= $classLabel ?>"> <?= Html::a('AD', [Url::to(['adm/ldap', 'fio' => $model->fio])], ['class' => 'sitdesk-ad-uri btn btn-sm py-0 px-0']) ?> </label>
							<span class="btn btn-sm btncopy fas fa-copy myCopy" title="Копировать" data-clipboard-text="<?= $model->fio ?>"></span>
							<?php
							echo $form->field($model, 'fio')->widget(TypeaheadBasic::classname(), [
								'data' => ArrayHelper::map(Fio::getList(), 'id', 'name'),

								'pluginOptions' => ['highlight' => true, 'minLength' => 0],
								'options' => ['class' => $classInput, 'tabindex' => '1', 'autocomplete' => "off", 'onkeydown' => "keyUp(event)", 'onkeyup' => "keyUp(event)"],
								'dataset' => [
									'limit' => 20,
								],
								'scrollable' => true,
							])->label(false);
							?>
						</div>
						<div class="col-3">
							<label class="<?= $classLabel ?>"><?= Html::button('Ip', ['value' => Url::to(['logs']), 'id' => 'modalIp', 'class' => 'api-link btn btn-link btn-sm py-0 px-0  input_modal']) ?></label>
							<span class="btn btn-sm btncopy fas fa-copy myCopy" title="Копировать" data-clipboard-text="<?= $model->ip ?>"></span>
							<?= $form->field($model, 'ip')->textInput(['maxlength' => true, 'tabindex' => '2', 'class' => $classInput, 'onkeydown' => "keyUp(event)", 'onkeyup' => "keyUp(event)"])->label(false) ?>
						</div>
						<div class="col-3">
							<label class="<?= $classLabel ?>"><?= Html::button('Тел', ['value' => Url::to(['phone']), 'id' => 'modalPhone', 'class' => 'api-link btn btn-link btn-sm py-0 px-0  input_modal']) ?></label>
							<?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'tabindex' => '3', 'class' => $classInput, 'onkeydown' => "keyUp(event)", 'onkeyup' => "keyUp(event)",])->label(false) ?>
						</div>
					</div>

					<?php if (isset($model->depart->name)) : ?>
						<small id="emailHelp" class="text-center mb-3 form-text text-muted"><?= $model->depart->name ?></small>
					<?php endif; ?>


					<?php if ((Yii::$app->user->id == 1111 or Yii::$app->user->id == 202020) and !isset($_GET['app'])) : ?>

					<?php else : ?>
						<div class="row">
							<div class="col-6">
								<label class="<?= $classLabel ?>"> Подразделение </label>
								<?= $form->field($model, 'id_podr')->dropDownList(ArrayHelper::map(Podr::getList(), 'id', 'name'), ['tabindex' => '4', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать подразделение -'])->label(false) ?>
							</div>
							<div class="col-6">
								<label class="<?= $classLabel ?>"> Приоритет </label>
								<?= $form->field($model, 'id_priority')->dropDownList(Priority::getList(), ['tabindex' => '5', 'options' => ['2' => ['Selected' => true]], 'class' => $classInput, 'disabled' => $disabled])->label(false) ?>
							</div>
						</div>
						<div class="row">
							<div class="col-6">
								<label class="<?= $classLabel ?>"> Тип проблемы </label>
								<?php if (isset($_GET['app'])) : ?>
									<?= $form->field($model, 'id_class')->dropDownList(ArrayHelper::map(Problem::getProblemMain(), 'id', 'name'), ['tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled, 'prompt' => '- Выбрать проблему -'])->label(false) ?>
									<?= $form->field($model, 'id_object')->dropDownList([''], ['tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled])->label(false) ?>
									<?= $form->field($model, 'id_problem')->dropDownList([''], ['tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled])->label(false) ?>
								<?php else : ?>

									<?= $form->field($model, 'id_class')->dropDownList(ArrayHelper::map(Problem::getProblemMain(), 'id', 'name'), ['tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled, 'prompt' => '- Выбрать проблему -'])->label(false) ?>
									<?= $form->field($model, 'id_object')->dropDownList(ArrayHelper::map(Problem::getProblemMain($model->id_class), 'id', 'name'), ['tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled])->label(false) ?>
									<?= $form->field($model, 'id_problem')->dropDownList(ArrayHelper::map(Problem::getProblemMain($model->id_object), 'id', 'name'), ['tabindex' => '6', 'class' => $classInput . ' buh', 'disabled' => $disabled])->label(false) ?>
								<?php endif; ?>
							</div>

							<div class="col-6">
								<label class="<?= $classLabel ?>"> Исполнитель:
									<small class="text-primary"><?= Sitdesk::fio(Login::Fio($model->id_user), 1) ?></small>
								</label>

								<?= $form->field($model, 'id_user')->dropDownList($login_list, ['tabindex' => '7', 'class' => $classInput, 'disabled' => false, 'prompt' => '- Выбрать исполнителя -'])->label(false) ?>
								<div id="app-back-div" class="text-muted">
									<small id="app-back-info" class="text-muted"> После выполнения. Заявка вернеться
										обратно к вам
									</small>
									<?= $form->field($model, 'back')->checkbox(['class' => ''])->label(false) ?>
								</div>
								<?php if (isset($_GET['app1s'])) { ?>
									<label class="<?= $classLabel ?>"> Исполнитель </label>
								<?php
									echo $form->field($model, 'id_user')->widget(Select2::classname(), [
										'data' => ArrayHelper::map(Login::getLoginList(), 'id', 'username'),
										'options' => ['placeholder' => '- Выбрать исполнителя -', 'multiple' => true, 'class' => 'asd', 'id' => 'app-id_user'],
										'size' => 'sm',
										'pluginOptions' => [
											'multiple' => true,
											'maximumInputLength' => 20,
											'font-size' => '10pt'
										],
									])->label(false);
								}
								?>

								<label class="switch hd-stupid-block px-2" id="hd-stupid-main" title='Включаем комментарий для пользователя'>
									<?php $checked = $model->stupid == 1 ? 'checked' : '' ?>
									<input type="checkbox" class="hd-stupid" <?= $checked ?> class="success comment-view" id="<?= $model->id ?>">
									<label class="<?= $classLabel ?>">Лишняя заявка</label>
								</label>

								<?php if (in_array(Yii::$app->user->id, [49, 1]) and $model->id_user == 50) : ?>
									<label class=" hd-stupid-block switch px-2" id="hd-no-exec-main" title='Включаем комментарий для пользователя'>
										<?php $checked = $model->no_exec == 1 ? 'checked' : '' ?>
										<input type="checkbox" class="hd-no-exec" <?= $checked ?> class="success comment-view" id="<?= $model->id ?>">
										<label class="<?= $classLabel ?>">Заявка не выполнена</label>
									</label>
								<?php endif; ?>

							</div>
						</div>

						<div class="row">
							<div class="col-lg-12">
								<label class="<?= $classLabel ?>"> Примечание </label>
								<?= $form->field($model, 'note')->textInput(['tabindex' => '8', 'maxlength' => true, 'class' => $classInput, 'placeholder' => 'Примечание'])->label(false);; ?>
							</div>
						</div>

					<?php endif; ?>

					<?php if (isset($_GET['app'])) {
						$model->type = null;
						$model->buh = null;
						$style = '';
						$styleBuh = 'none';
					} else {
						$style = $model->type ? 'block' : '';
						$styleBuh = $model->buh ? 'block' : 'none';
					} ?>
					<div class="row" id="htb1" style="display: <?= $style ?>;">
						<div class="col-lg-12">
							<label class="<?= $classLabel ?>"> Служебка </label>
							<span class="btn btn-sm1 btncopy fas fa-copy myCopy " title="Копировать" data-clipboard-text="<?= $model->type ?>"></span>
							<?= $form->field($model, 'type')->textInput(['tabindex' => '10', 'maxlength' => true, 'class' => $classInput])->label(false) ?>
						</div>
					</div>

					<div class="row" id="htb2" style="display: <?= $styleBuh ?>;">
						<div class="col-lg-12">
							<label class="<?= $classLabel ?>"> Система 1С </label>
							<?= $form->field($model, 'buh')->dropDownList(Buh::getList(), ['tabindex' => '4', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать систему 1С -'])->label(false) ?>
						</div>
					</div>

				<?php } else { ?>
					<div class="row">
						<div class="col-sm-6">
							<label class="<?= $classLabel ?>"> Исполнитель:
								<small class="text-primary"><?= Sitdesk::fio(Login::Fio($model->id_user), 1) ?></small>
							</label>

							<?= $form->field($model, 'id_user')->dropDownList($login_list, ['tabindex' => '7', 'class' => $classInput, 'disabled' => false, 'prompt' => '- Выбрать исполнителя -'])->label(false) ?>
						</div>

						<div class="col-sm-6">
							<label class="<?= $classLabel ?>"> Система 1С </label>
							<?= $form->field($model, 'buh')->dropDownList(Buh::getList(), ['tabindex' => '4', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать систему 1С -'])->label(false) ?>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label class="<?= $classLabel ?>"> Проект </label>
							<?= $form->field($model, 'id_project')->dropDownList(AppProject::getList(), ['tabindex' => '4', 'class' => $classInput, 'disabled' => $disabled, 'prompt' => '- Выбрать проект -'])->label(false) ?>
						</div>
					</div>

				<?php } ?>
				<!--   Статус 14 - Согласование. Для удобства убираем все кнопки при соглаосвании. Добавляем кнопки для согласованиии -->
				<div class="row justify-content-md-center mb-2">
					<div class="col-lg-12">

						<?= $form->field($model, 'content')->textarea(['tabindex' => '9', 'rows' => $model->contentRows($model->getContent()), 'class' => $classInput . ' app-input-description content-size app-content', 'placeholder' => 'Описание'])->label(false); ?>

						<!--         Вывод найденных ФИО  -->
						<?php if (!isset($_GET['app'])) : ?>

							<?= \app\components\TagWidget::widget(['content' => $model->content]) ?>
							<?= \app\components\DocumentWidget::widget(['id_app' => $model->id]) ?>
						<?php endif ?>

						<?php if ($model->status != Status::STATUS_AGREED) { ?>

							<?php if (Yii::$app->user->can('Admin') or Yii::$app->user->can('Disp') or Yii::$app->user->can('User')) { ?>
								<div class="inline-block">
									<!--                                    --><?php //echo $form->field($model, 'documentFiles[]')->widget(FileInput::classname(), [
																								//                                        'language' => 'ru',
																								//                                        'options' => ['multiple' => true, 'class' => 'col-12'],
																								//                                        'pluginOptions' => [
																								//                                            'showCaption' => false,
																								//                                            'showRemove' => false,
																								//                                            'showUpload' => false,
																								//                                            'showCancel' => false,
																								//                                            'browseClass' => 'btn btn-info btn-sm fas fa-folder-open',
																								//                                            'mainClass' => '12',
																								//                                            'maxFileSize'=>2800000
																								//                                        ]
																								//                                    ])->label(false);
																								//                                    
																								?>

									<?= $form->field($model, 'documentFiles[]')->fileInput(['multiple' => true])->label(false);
									?>
								</div>

							<?php } ?>

							<?php if (!isset($_GET['app'])) : ?>
								<?php if (!Yii::$app->user->can('DispQuest')) : ?>
									<div class="inline-block">
										<div><?= Html::button('', ['class' => 'btn btn-info  btn-sm fas fa-users', 'id' => 'ajaxFiocase', 'value' => $model->id, 'title' => 'Поиск ФИО']) ?></div>
									</div>

									<?php if (Yii::$app->user->can('Admin')) : ?>
										<div class="inline-block">
											<div><?= Html::button('', ['class' => 'btn btn-info  btn-sm fas fa-user-cog', 'id' => 'ajaxExpandFiocase', 'value' => $model->id, 'title' => 'Расширенный поиск ФИО']) ?></div>
										</div>
									<?php endif ?>

									<div id="ajaxFioacaseContent">

									</div>
								<?php endif ?>
							<?php endif ?>
							<!--        Вывод найденных ФИО  -->

						<?php } else { ?>
							<?php if (!Yii::$app->user->can('DispQuest')) : ?>
								<div class="row mt-3">
									<div class="col-6">
										<a href="<?= Url::to(['agreed', 'id' => $model->id, 's' => 1]) ?>" class="col-12 btn btn-sm btn-success"> Согласовать </a>
									</div>
									<div class="col-6">
										<a href="<?= Url::to(['agreed', 'id' => $model->id, 's' => 2]) ?>" class="col-12 px-3 btn btn-sm btn-danger"> Отклонить </a>
									</div>
								</div>
							<?php endif; ?>
						<?php } ?>
					</div>
				</div>


				<div class="row justify-content-md-center">
					<div id="" class="form-group  col-lg-4 col-md-4 col-xs-4 py-0 mb-2 ">
						<?php if (!Yii::$app->user->can('DispQuest')) : ?>

							<?php if ($type !== 3) : ?>
								<?php if ($model->status == 12) : ?>
									<?= Html::submitButton('В работу', ['class' => ' btn btn-sm col-12 btn-primary input']) ?>
								<?php else : ?>
									<?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-sm btn-success col-12 input' : ' btn btn-sm col-12 btn-primary input']) ?>
								<?php endif; ?>
							<?php else : ?>
								<?php if (Yii::$app->user->id == 1 or Yii::$app->user->id == 49 or Yii::$app->user->id == 5 or Yii::$app->user->id == 48 or Yii::$app->user->id == 61) : ?>
									<?= Html::a('<span class="btn btn-sm col-12 btn-warning" title="Справочная"> Завести на себя </span>', ['/site/ticket-yourself', 'id' => $model->id]) ?>
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<?php ActiveForm::end(); ?>
			</div>


			<!--        Помощ диспетчеру -->
			<?= \app\components\HelpWidget::widget() ?>
			<!--         Помощ диспетчеру -->


			<!--        Аналогичные заявки  -->
			<?php if ($model->type != 6) : ?>
				<?= \app\components\AnalogAppWidget::widget(['id' => Sitdesk::getIdByDv($model->type, $model->id)[0]->id_app]) ?>

				<?php if (!$model->id_project) : ?>
					<?= \app\components\AnalogTicket::widget(['id' => $model->id]) ?>
				<?php endif; ?>
			<?php endif; ?>
			<!--        Аналогичные заявки  -->

			<!--        Комментарии-->
			<?= CommentWidget::widget() ?>
			<!--        Комментарии-->

			<br>
			<!--        Общая информация по заявке -->


		</div>
	</div>


	<div class="sitdesk-right-column">
		<div>
			<div class="search-block mb-2">

				<input type="text" id="app-search" class="form-control col-4" placeholder="Поиск по тел. спр...">

				<div id="app-search-content" class="pt-2"></div>



				<?php if ($model->id_project and Yii::$app->user->id == 1) : ?>
					<?= \app\components\ProjectInfo::widget(['id' => $model->id_project]) ?>
				<?php endif; ?>

				<?php if (in_array($id_user, [1, 4, 49, 5, 18, 20, 43, 32, 31, 48, 57, 70])) : ?>
					<div id="sitdesk-user-employment">
						<?php
						$_user_sit = Login::getFullStatusByDepart(Login::USER_SIT);
						$_user_sap = Login::getFullStatusByDepart(Login::USER_SAP);
						$_user_ss = Login::getFullStatusByDepart(Login::USER_SS);

						echo StatusTable::widget(['model' => $_user_sit['stat'], 'max' => $_user_sit['max']]);
						echo StatusTable::widget(['model' => $_user_sap['stat'], 'max' => $_user_sap['max']]);
						echo StatusTable::widget(['model' => $_user_ss['stat'], 'max' => $_user_ss['max']]);

						?>
					</div>
				<?php endif; ?>

				<!--     Вывод всех активных заявок   -->
				<?php echo \app\components\AdditionalWidget::widget() ?>

				<?php echo \app\components\UserTicketWidget::widget(['username' => $model->fio, 'id' => $model->id]) ?>
				<!--     Вывод всех активных заявок   -->

			</div>
		</div>


	</div>


	<div id="sidebar-right" class="sidebar-right">
	</div>

</div>

<?php
	unset($model);
?>

<div class="body-content">
	<p>
		<?php
		Modal::begin([
			'options' => [
				'id' => 'modal',
				'tabindex' => false, // important for Select2 to work properly
			],
			'size' => '',
		]);
		echo "<div id='modalContent'>  </div>";
		Modal::end();
		?>
	</p>
</div>