<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use common\widgets\AjaxForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\Role */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => 'myform']); ?>
<div class="card card-dark">
    <div class="card-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?=
        $form->field($model, 'panel')->widget(Select2::classname(), [
            'data' => ['Backend' => 'Backend', 'Frontend' => 'Frontend'],
            'hideSearch' => true,
            'options' => ['placeholder' => '--Select--'],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
        <?=
        $form->field($model, 'status')->widget(Select2::classname(), [
            'data' => ['Active' => 'Active', 'Inactive' => 'Inactive'],
            'hideSearch' => true,
            'options' => ['placeholder' => '--Select--'],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>   
    <div class="card-footer text-center">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Close', ['class' => 'btn btn-danger', 'data-bs-dismiss' => 'modal']) ?>
    </div>
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
<script>
//$("#role-permission .custom-checkbox #i0").click(function() {
//  $("input[name='Role[permission][]").prop("checked", $(this).prop("checked"));
//});
//
//$("input[name='Role[permission][]']").click(function() {
//  if (!$(this).prop("checked")) {
//    $("#role-permission .custom-checkbox #i0").prop("checked", false);
//  }
//});
</script>
