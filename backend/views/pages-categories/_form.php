<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use common\widgets\AjaxForm;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model backend\models\PagesCategories */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pages-categories-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>

    <?= $form->field($model, 'name_string')->textInput(['maxlength' => true]) ?>
    
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