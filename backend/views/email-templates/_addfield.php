<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use common\widgets\AjaxForm;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model backend\models\Role */

?>
<div class="add-field-form">
    <?php $form = ActiveForm::begin(['id'=>'addfield_form']); ?>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 m-auto">  
            <?= $form->field($model, 'field')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'description')->textarea(['row' => 6]) ?>
        </div>
    </div>
    <div class="form-group text-center">
        <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
        <?= Html::button('Close', ['class' => 'btn btn-danger', 'data-bs-dismiss' => 'modal']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
AjaxForm::widget([
    'id' => 'addfield_form',
    'enableAjaxSubmit' => true,
    'ajaxSubmitOptions' => [
        'beforeSend' => new JsExpression('function() {
                $(\'.loader_div\').show();
            }'),
        'success' => new JsExpression('function(response) {
                if(response.result == 1){
                    fieldlist(\''.$email_template_id.'\');
                    $(\'#modelAddfield\').modal(\'hide\');
                }else if(response.result == 2){
                    $(".field-templatefield-field").find(".invalid-feedback").html(response.message);
                    $(".field-templatefield-field").find(".invalid-feedback").css("display", "block");
                } 
            }'),
        'complete' => new JsExpression('function() {
                $(\'.loader_div\').hide();
            }')
    ],
]);
 ?>