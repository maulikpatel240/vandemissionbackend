<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\widgets\MaskedInput;
use common\widgets\AjaxForm;
use yii\web\JsExpression;
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>
    <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <?=
            $form->field($model, 'phone_number')->widget(MaskedInput::classname(), [
                'mask' => '999-999-9999',
                'clientOptions' => [
                    'removeMaskOnSubmit' => true,
                ]
            ])
            ?>
        </div>      
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <?= $form->field($model, 'gender')->dropDownList(['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other',], ['prompt' => '']) ?>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <?= $form->field($model, 'birthday')->textInput(['maxlength' => true, 'class' => "form-control datetimepicker-input", 'data-target' => '#users-birthday', 'data-toggle' => 'datetimepicker']) ?>
        </div> 
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <?= $form->field($model, 'aadhaar_card_number')->textInput(['maxlength' => true]) ?>
        </div>    
    </div> 
    <div class="row">
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
            <div class="file-upload">
                <?php
                $image_upload_wrap_div = ($model->avatar) ? 'display: none;' : 'display: block;';
                $file_upload_content_div = ($model->avatar) ? 'display: block;' : 'display: none;';
                $image_url = ($model->avatar) ? Yii::$app->urlManager->baseUrl . '/uploads/user/avatar/' . $model->avatar : '#';
                $image_name = ($model->avatar) ? $model->avatar : 'Uploaded Image';
                ?>
                <button class="file-upload-btn btn btn-outline-secondary" type="button" onclick="$('.file-upload-input-avatar').trigger('click')">Add Image</button>
                <div class="image-upload-wrap image-upload-wrap-avatar" style="<?= $image_upload_wrap_div; ?>">
                    <?= $form->field($model, 'avatar')->fileInput(['class' => 'file-upload-input file-upload-input-avatar', 'accept' => 'image/*', 'onchange' => 'readURLavatar(this);'])->label(false); ?>
                    <div class="drag-text">
                        <h3>Drag and drop a file or select add Image</h3>
                    </div>
                </div>
                <div class="file-upload-content file-upload-content-avatar" style="<?= $file_upload_content_div; ?>">
                    <img class="file-upload-image file-upload-image-avatar" src="<?= $image_url ?>" alt="your image" />
                    <div class="image-title-wrap">
                        <button type="button" onclick="removeUploadavatar()" class="remove-image btn btn-danger">Remove <span class="image-title image-title-avatar"><?= $image_name ?></span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
            <div class="file-upload">
                <?php
                $image_upload_wrap_div = ($model->aadhaar_card_photo) ? 'display: none;' : 'display: block;';
                $file_upload_content_div = ($model->aadhaar_card_photo) ? 'display: block;' : 'display: none;';
                $image_url = ($model->aadhaar_card_photo) ? Yii::$app->urlManager->baseUrl . '/uploads/user/aadhaar/' . $model->aadhaar_card_photo : '#';
                $image_name = ($model->aadhaar_card_photo) ? $model->aadhaar_card_photo : 'Uploaded aadhaar card photo';
                ?>
                <button class="file-upload-btn btn btn-outline-secondary" type="button" onclick="$('.file-upload-input-aadhaar_card_photo').trigger('click')">Add aadhaar card photo</button>
                <div class="image-upload-wrap image-upload-wrap-aadhaar_card_photo" style="<?= $image_upload_wrap_div; ?>">
                    <?= $form->field($model, 'aadhaar_card_photo')->fileInput(['class' => 'file-upload-input file-upload-input-aadhaar_card_photo', 'accept' => 'image/*', 'onchange' => 'readURLaadhaar_card_photo(this);'])->label(false); ?>
                    <div class="drag-text">
                        <h3>Drag and drop a file or select add Image</h3>
                    </div>
                </div>
                <div class="file-upload-content file-upload-content-aadhaar_card_photo" style="<?= $file_upload_content_div; ?>">
                    <img class="file-upload-image file-upload-image-aadhaar_card_photo" src="<?= $image_url ?>" alt="your image" />
                    <div class="image-title-wrap">
                        <button type="button" onclick="removeUploadaadhaar_card_photo()" class="remove-image btn btn-danger">Remove <span class="image-title image-title-aadhaar_card_photo"><?= $image_name ?></span></button>
                    </div>
                </div>
            </div>
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
    $(document).ready(function () {
        datePicker(datepickerId = "users-birthday", format = "YYYY-MM-DD", locale = "en", Multidate = "", value = "", maxDate = "", minDate = "");
    });
    function readURLavatar(input) {
        var extension = input.files[0].name.substr((input.files[0].name.lastIndexOf('.') + 1));
        var img_extention = '<?= json_encode(Yii::$app->params['image_extention']) ?>';
        var jsonArray = JSON.parse(img_extention);

        if (jQuery.inArray(extension, jsonArray) != '-1') {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.image-upload-wrap-avatar').hide();

                    $('.file-upload-image-avatar').attr('src', e.target.result);
                    $('.file-upload-content-avatar').show();

                    $('.image-title-avatar').html(input.files[0].name);
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                removeUpload();
            }
            $('.field-users-avatar').find('.invalid-feedback').html('');
            $('.field-users-avatar').find('.invalid-feedback').css('display', 'none');
        } else {
            var invalid_error = 'Only files with these extensions are allowed: <?= implode(',', Yii::$app->params['image_extention']); ?>';
            $('.field-users-avatar').find('.invalid-feedback').html(invalid_error);
            $('.field-users-avatar').find('.invalid-feedback').css('display', 'block');
            //alert(invalid_error);
            var $el = $('#users-avatar');
            $el.wrap('<form>').closest('form').get(0).reset();
            $el.unwrap();
        }

    }

    function removeUploadavatar() {
        $('.file-upload-input-avatar').replaceWith($('.file-upload-input-avatar').clone());
        $('.file-upload-content-avatar').hide();
        $('.image-upload-wrap-avatar').show();
    }
    $('.image-upload-wrap-avatar').bind('dragover', function () {
        $('.image-upload-wrap-avatar').addClass('image-dropping');
    });
    $('.image-upload-wrap-avatar').bind('dragleave', function () {
        $('.image-upload-wrap-avatar').removeClass('image-dropping');
    });
    
    function readURLaadhaar_card_photo(input) {
        var extension = input.files[0].name.substr((input.files[0].name.lastIndexOf('.') + 1));
        var img_extention = '<?= json_encode(Yii::$app->params['image_extention']) ?>';
        var jsonArray = JSON.parse(img_extention);

        if (jQuery.inArray(extension, jsonArray) != '-1') {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.image-upload-wrap-aadhaar_card_photo').hide();

                    $('.file-upload-image-aadhaar_card_photo').attr('src', e.target.result);
                    $('.file-upload-content-aadhaar_card_photo').show();

                    $('.image-title-aadhaar_card_photo').html(input.files[0].name);
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                removeUpload();
            }
            $('.field-users-aadhaar_card_photo').find('.invalid-feedback').html('');
            $('.field-users-aadhaar_card_photo').find('.invalid-feedback').css('display', 'none');
        } else {
            var invalid_error = 'Only files with these extensions are allowed: <?= implode(',', Yii::$app->params['image_extention']); ?>';
            $('.field-users-aadhaar_card_photo').find('.invalid-feedback').html(invalid_error);
            $('.field-users-aadhaar_card_photo').find('.invalid-feedback').css('display', 'block');
            //alert(invalid_error);
            var $el = $('#users-aadhaar_card_photo');
            $el.wrap('<form>').closest('form').get(0).reset();
            $el.unwrap();
        }

    }

    function removeUploadaadhaar_card_photo() {
        $('.file-upload-input-aadhaar_card_photo').replaceWith($('.file-upload-input-aadhaar_card_photo').clone());
        $('.file-upload-content-aadhaar_card_photo').hide();
        $('.image-upload-wrap-aadhaar_card_photo').show();
    }
    $('.image-upload-wrap-aadhaar_card_photo').bind('dragover', function () {
        $('.image-upload-wrap-aadhaar_card_photo').addClass('image-dropping');
    });
    $('.image-upload-wrap-aadhaar_card_photo').bind('dragleave', function () {
        $('.image-upload-wrap-aadhaar_card_photo').removeClass('image-dropping');
    });
</script>
