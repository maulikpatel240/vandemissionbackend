<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use common\widgets\dynamicform\DynamicFormWidget;
use common\widgets\AjaxForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\Langs */
/* @var $form yii\widgets\ActiveForm */
$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {  
        jQuery(this).html("No: " + (index + 1));
    });
});
jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("No: " + (index + 1));
    });
});
';
$this->registerJs($js);

$model->type = ($model->type) ? explode(',', $model->type) : '';
?>

<div class="langs-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>

    <?=
    $form->field($model, 'type')->widget(Select2::classname(), [
        'data' => Yii::$app->params['langs_type'],
        'options' => ['placeholder' => '--Select--', 'multiple'=>true],
        'pluginOptions' => [
            'allowClear' => true,
            'dropdownParent' => '#formmodal'
        ],
    ]);
    ?>
    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'lang_key')->textInput(['maxlength' => true])->label('Lang Key <p class="text-danger mb-0 text-sm">Note: Only allow small letter and no space required. Ex. welcome_page</p>') ?>
        <?= $form->field($model, 'english')->textArea(['row' => 2]) ?>
        <?= $form->field($model, 'gujarati')->textArea(['row' => 2]) ?>
        <?= $form->field($model, 'hindi')->textArea(['row' => 2]) ?>
    <?php } if ($model->isNewRecord) { ?>
        <?php
        DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => Yii::$app->params['LANG_DYNAMIC_FORM_LIMIT'], // the maximum times, an element can be cloned (default 999)
            'min' => Yii::$app->params['MIN_DYNAMIC_FORM'], // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $modelLangs[0],
            'countmodel' => count($modelLangs),
            'formId' => 'myform',
            'formFields' => [
                'lang_key',
                'english',
                'gujarati',
                'hindi',
            ],
        ]);
        ?>

        <div class="panel panel-default">
            <div class="panel-heading mb-2">

                <i class="far fa-list-alt"></i> Add Languages

                <div class="clearfix"></div>

            </div>
            <div class="panel-body container-items">
                <?php foreach ($modelLangs as $key => $value): ?>
                    <div class="item panel panel-default shadow p-3 mb-3">
                        <div class="panel-heading text-dark text-bold text-center border-bottom">
                            <span class="panel-title-address">No: <?= ($key + 1) ?></span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <?php
                            // necessary for update action.
//                        if (!$value->isNewRecord) {
//                            echo Html::activeHiddenInput($value, "[{$i}]id");
//                        }
                            ?>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= $form->field($value, "[$key]lang_key")->textInput(['maxlength' => true])->label('Lang Key <p class="text-danger mb-0 text-sm">Note: Only allow small letter and no space required. Ex. welcome_page</p>'); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?php echo $form->field($value, "[$key]english")->textarea(['rows' => 2]) ?>
                                    <?php //echo $form->field($value, "[$key]english")->textArea(['maxlength' => true]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?php echo $form->field($value, "[$key]gujarati")->textarea(['rows' => 2]) ?>
                                    <?php //echo $form->field($value, "[$key]gujarati")->textArea(['maxlength' => true]) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?php echo $form->field($value, "[$key]hindi")->textarea(['rows' => 2]) ?>
                                    <?php //echo $form->field($value, "[$key]hindi")->textArea(['maxlength' => true]) ?>
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

    <div class="form-group text-center">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Close', ['class' => 'btn btn-danger', 'data-bs-dismiss' => 'modal']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
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
