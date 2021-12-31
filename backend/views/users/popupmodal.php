<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use yii\widgets\MaskedInput;
use common\widgets\AjaxForm;
use yii\web\JsExpression;
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <?php
            if ($column == "phone_number_verify") {
                echo $form->field($model, 'phone_number_verify_otp')->textInput(['maxlength' => true]);
            } elseif ($column == "email_verify") {
                echo $form->field($model, 'email_verify_code')->textInput(['maxlength' => true]);
            }
            ?>
            <p class="text-danger text-sm m-0">Enter the 6-digit verification code that you received and click Verify.</p>
            <p class="text-right text-sm">click here <a href="<?=Url::to(['users/resendcode','id'=>$model->id,'c'=>$column],true)?>" onclick="resendcode(this); return false;" class="">Resend code</a></p>
        </div>
    </div>
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
                if(response.result == 1 && response.url){
                    window.location.href = response.url;
                }else{
                    if(response.column == "phone_number_verify"){
                        $(".field-users-phone_number_verify_otp").find(".invalid-feedback").html(response.message);
                        $(".field-users-phone_number_verify_otp").find(".invalid-feedback").css("display","block");
                    }else if(response.column == "email_verify"){
                        $(".field-users-email_verify_code").find(".invalid-feedback").html(response.message);
                        $(".field-users-email_verify_code").find(".invalid-feedback").css("display","block");
                    }
                }
            }'),
        'complete' => new JsExpression('function() {
                $(\'.loader_div\').hide();
                $(\'#formmodal\').modal(\'hide\');
            }')
    ],
]);
?>
<script>
    $(document).ready(function () {
        datePicker(datepickerId = "users-birthday", format = "YYYY-MM-DD", locale = "en", Multidate = "", value = "", maxDate = "", minDate = "");
    });
    function resendcode(e){
        $.post({
            url: $(e).attr('href'),
            success: function (response) {
                
            },
        });
    }
</script>
