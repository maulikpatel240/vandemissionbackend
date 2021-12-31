<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use backend\models\Countries;
use backend\models\States;
use common\widgets\AjaxForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\Districts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="districts-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>
    <?php /*
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?=
            $form->field($model, 'country_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Countries::find()->where(['status' => 'Active'])->asArray()->all(), 'id', 'english'),
                'options' => ['placeholder' => '--Select--'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?=
            $form->field($model, 'state_id')->widget(DepDrop::classname(), [
                'data' => ArrayHelper::map(States::find()->where(['status' => 'Active'])->asArray()->all(), 'id', 'english'),
                'options' => ['placeholder' => 'Select ...'],
                'type' => DepDrop::TYPE_SELECT2,
                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                'pluginOptions' => [
                    'depends' => ['districts-country_id'],
                    'url' => Url::to(['/requests/states']),
                    'loadingText' => '',
                ]
            ]);
            ?>
        </div> 
    </div> 
     */
    ?>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?= $form->field($model, 'english')->textInput(['maxlength' => true, 'readonly' => true]) ?>
            <?= $form->field($model, 'gujarati')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'hindi')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'bounding_box')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="file-upload">
                <?php
                $image_upload_wrap_div = ($model->map) ? 'display: none;' : 'display: block;';
                $file_upload_content_div = ($model->map) ? 'display: block;' : 'display: none;';
                $image_url = ($model->map) ? Yii::$app->urlManager->baseUrl . '/uploads/map/district/' . $model->map : '#';
                $image_name = ($model->map) ? $model->map : 'Uploaded Image';
                ?>
                <button class="file-upload-btn btn btn-outline-secondary" type="button" onclick="$('.file-upload-input').trigger('click')">Add Map Image</button>
                <div class="image-upload-wrap" style="<?= $image_upload_wrap_div; ?>">
                    <?= $form->field($model, 'map')->fileInput(['class' => 'file-upload-input', 'accept' => 'image/*', 'onchange' => 'readURL(this);'])->label(false); ?>
                    <div class="drag-text">
                        <h3>Drag and drop a file or select add Image</h3>
                    </div>
                </div>
                <div class="file-upload-content" style="<?= $file_upload_content_div; ?>">
                    <img class="file-upload-image" src="<?= $image_url ?>" alt="your image" />
                    <div class="image-title-wrap">
                        <button type="button" onclick="removeUpload()" class="remove-image btn btn-danger">Remove <span class="image-title"><?= $image_name ?></span></button>
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
            $('.field-states-map').find('.invalid-feedback').html('');
            $('.field-states-map').find('.invalid-feedback').css('display', 'none');
        } else {
            var invalid_error = 'Only files with these extensions are allowed: <?= implode(',', Yii::$app->params['image_extention']); ?>';
            $('.field-states-map').find('.invalid-feedback').html(invalid_error);
            $('.field-states-map').find('.invalid-feedback').css('display', 'block');
            //alert(invalid_error);
            var $el = $('#states-map');
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
</script>
