<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use common\widgets\dynamicform\DynamicFormWidget;
use kartik\select2\Select2;
use backend\models\Modules;
use common\widgets\AjaxForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\Role */
/* @var $form yii\widgets\ActiveForm */
$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {  

    });
});
jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {

    });
});
';
$this->registerJs($js);
?>
<?php $form = ActiveForm::begin(['id' => 'myform']); ?>

<?=
$form->field($model, 'module_id')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(Modules::find()->where(['status' => 'Active'])->andWhere(['!=', 'functionality', 'none'])->andWhere(['!=', 'title', ''])->asArray()->all(), 'id', 'title'),
    'options' => ['placeholder' => '--Select--'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);
?>
<?php if (!$model->isNewRecord) { ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'controller')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>
<?php } else { ?>
    <?php
    DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => Yii::$app->params['MODULE_DYNAMIC_FORM_LIMIT'], // the maximum times, an element can be cloned (default 999)
        'min' => Yii::$app->params['MIN_DYNAMIC_FORM'], // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $modelPermission[0],
        'countmodel' => count($modelPermission),
        'formId' => 'myform',
        'formFields' => [
            'title',
            'name',
            'controller',
            'action',
        ],
    ]);
    ?>

    <div class="panel panel-default">
        <div class="panel-heading mb-2">

            <i class="far fa-list-alt"></i> Modules Permission

            <div class="clearfix"></div>

        </div>
        <div class="panel-body container-items">
            <?php foreach ($modelPermission as $key => $value): ?>
                <div class="item panel panel-default shadow p-3 mb-3">

                    <div class="panel-body">
                        <?php
                        // necessary for update action.
//                        if (!$value->isNewRecord) {
//                            echo Html::activeHiddenInput($value, "[{$i}]id");
//                        }
                        ?>
                        <div class="row">
                            <div class="col-sm-3">
                                <?= $form->field($value, "[$key]title")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($value, "[$key]name")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($value, "[$key]controller")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($value, "[$key]action")->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="float-end">
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="panel-footer mb-2">
            <button type="button" class="float-end add-item btn btn-success btn-xs"><i class="fa fa-plus"></i> Add</button>

            <div class="clearfix"></div>

        </div>
    </div>
    <?php DynamicFormWidget::end(); ?>
<?php } ?>
<div class="text-center">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    <?= Html::button('Close', ['class' => 'btn btn-danger', 'data-bs-dismiss' => 'modal']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
AjaxForm::widget([
    'id' => 'myform',
    'enableAjaxSubmit' => true,
    'ajaxSubmitOptions' => [
        'beforeSend' => new JsExpression('function() {
                $(\'.loader_div\').show();
            }'),
        'success' => new JsExpression('function(response) {
                $.pjax.reload({container: "#gridtable-pjax"});
            }'),
        'complete' => new JsExpression('function() {
                $(\'.loader_div\').hide();
                $(\'#formmodal\').modal(\'hide\');
            }')
    ],
]);
?>
