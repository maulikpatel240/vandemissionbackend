<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use common\widgets\AjaxForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\Countries */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="countries-form">
    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?= $form->field($model, 'english')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'gujarati')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'hindi')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
        </div> 
    </div>

    <div class="text-center">
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