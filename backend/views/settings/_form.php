<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use common\widgets\AjaxForm;
use yii\web\JsExpression;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model backend\models\Config */
/* @var $form yii\widgets\ActiveForm */
$typedata = [
    'Text' => 'Text',
    'Textarea' => 'Textarea',
    'File' => 'File',
    'Date' => 'Date',
    'Time' => 'Time',
    'Datetime' => 'Datetime',
    'Radio' => 'Radio',
    'Checkbox' => 'Checkbox',
    'Select' => 'Select'
];
$id = ($model->isNewRecord) ? '' : $model->id;
$ajaxurl = Url::to(['/settings/create'], true);
$description = [];
if($id){
    $ajaxurl = Url::to(['/settings/update', 'id' => $id], true);
    $description = ($model->description) ? json_decode($model->description, true) : '';
}
$description_placeholder = '';
$model->type = $type;
?>
<div class="config-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']);?>

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <?=
            $form->field($model, 'type')->widget(Select2::classname(), [
                'data' => $typedata,
                'options' => ['placeholder' => '--Select--'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents' => [
                    "change" => 'function(e) {typeofvalue($(this).val());}',
                ]
            ]);
            ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?php
            $value = '';
            if($type == 'Text'){
                $value = $form->field($model, 'value')->textInput();
            }
            if($type == 'Textarea'){
                $value = $form->field($model, 'value')->textArea();
            }
            if($type == 'File'){
                $image_upload_wrap_div = ($model->value) ? 'display: none;' : 'display: block;';
                $file_upload_content_div = ($model->value) ? 'display: block;' : 'display: none;';
                $image_url = ($model->value) ? Yii::$app->urlManager->baseUrl . '/uploads/settings/' . $model->value : '#';
                $image_name = ($model->value) ? $model->value : 'Uploaded Image';
                $value_file = $form->field($model, 'value')->fileInput(['class' => 'file-upload-input', 'accept' => 'image/*', 'onchange' => 'readURL(this);'])->label(false);
                $value = '<div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="file-upload">
                                <button class="file-upload-btn btn btn-outline-secondary" type="button" onclick="$(\'.file-upload-input\').trigger(\'click\')">Add Image</button>
                                <div class="image-upload-wrap" style="' . $image_upload_wrap_div . '">
                                    ' . $value_file . '
                                    <div class="drag-text">
                                        <h3>Drag and drop a file or select add Image</h3>
                                    </div>
                                </div>
                                <div class="file-upload-content" style="' . $file_upload_content_div . '">
                                    <img class="file-upload-image" src="' . $image_url . '" alt="your image" />
                                    <div class="image-title-wrap">
                                        <button type="button" onclick="removeUpload()" class="remove-image btn btn-danger">Remove <span class="image-title">' . $image_name . '</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>';
            }
            if($type == 'Date'){
                $value = $form->field($model, 'value')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => 'Enter date ...'],
                            'pluginOptions' => [
                                'autoclose'=>true
                            ]
                        ]);
            }
            if($type == 'Time'){
                $value = $form->field($model, 'value')->widget(TimePicker::classname(), []);
            }
            if($type == 'Datetime'){
                $value = $form->field($model, 'value')->widget(DateTimePicker::classname(), [
                                'options' => ['placeholder' => 'Enter event time ...'],
                                'pluginOptions' => [
                                        'autoclose' => true
                                ]
                        ]);
            }
            if($type == 'Radio'){
                $description_placeholder = '{"a1":"b1","a2":"b2"}';
                $value = $form->field($model, 'value')->radioList($description, array('labelOptions' => array('style' => 'display:inline'), 'separator' => '  '));
            }
            if($type == 'Checkbox'){
                $description_placeholder = '{"a1":"b1","a2":"b2"}';
                $value = $form->field($model, 'value')->checkboxList($description);
            }
            if($type == 'Select'){
                $description_placeholder = '{"a1":"b1","a2":"b2"}';
                $value = $form->field($model, 'value')->widget(Select2::classname(), [
                        'data' => $description,
                        'options' => ['placeholder' => '--Select--'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]);
            }
            echo $value;
            ?>
            <?= $form->field($model, 'gujarati')->textInput(['placeholder' => ''])->label() ?>
            <?= $form->field($model, 'hindi')->textInput(['placeholder' => ''])->label() ?>
            <?= $form->field($model, 'description')->textArea(['placeholder' => ''])->label('Description <span class="me-2">'.$description_placeholder.'</span>') ?>
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
    function readURL(input) {
        var extension = input.files[0].name.substr((input.files[0].name.lastIndexOf('.') + 1));
        var img_extention = '<?= json_encode(Yii::$app->params['image_extention']) ?>';
        var jsonArray = JSON.parse(img_extention);

        if (jQuery.inArray(extension, jsonArray) != '-1') {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.image-upload-wrap').hide();

                    $('.file-upload-image').attr('src', e.target.result);
                    $('.file-upload-content').show();

                    $('.image-title').html(input.files[0].name);
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                removeUpload();
            }
            $('.field-states-image').find('.invalid-feedback').html('');
            $('.field-states-image').find('.invalid-feedback').css('display', 'none');
        } else {
            var invalid_error = 'Only files with these extensions are allowed: <?= implode(',', Yii::$app->params['image_extention']); ?>';
            $('.field-states-image').find('.invalid-feedback').html(invalid_error);
            $('.field-states-image').find('.invalid-feedback').css('display', 'block');
            //alert(invalid_error);
            var $el = $('#states-image');
            $el.wrap('<form>').closest('form').get(0).reset();
            $el.unwrap();
        }

    }

    function removeUpload() {
        $('.file-upload-input').replaceWith($('.file-upload-input').clone());
        $('.file-upload-content').hide();
        $('.image-upload-wrap').show();
    }
    $('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
    });

    function typeofvalue(e) {
        //$(".setting_type").html(response);
        var data = {
            '<?= yii::$app->request->csrfParam ?>': '<?= yii::$app->request->csrfToken ?>',
            type:e
        }
        $.ajax({
            url: "<?= $ajaxurl; ?>",
            method: 'post',
            data: data,
            success: function (response) {
                $('#formmodal').find('#modalContent').html(response);
            }});
    }
</script>