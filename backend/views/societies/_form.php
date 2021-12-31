<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;
use backend\models\Countries;
use common\widgets\AjaxForm;
use yii\web\JsExpression;
use kartik\typeahead\Typeahead;

/* @var $this yii\web\View */
/* @var $model backend\models\States */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="states-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>

    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <h4><?= $model->name0->english ?></h4>
            <?php
            $template = '<ul class="list-group list-unstyled"><li class="list-item-group"><i class="fas fa-map-marker-alt"></i> {{village}}</li>' .
                    '<li class="list-item-group" data-key="VM-{{id}}">{{other}}</li>' .
                    '</ul>';
            echo $form->field($model, 'headquarters')->widget(Typeahead::classname(), [
                //'data' => $data,
                'scrollable' => true,
                'options' => ['placeholder' => 'Find your location...', 'onkeypress' => "if (event.which == 13 || event.keyCode == 13) result()"],
                'pluginOptions' => ['minLength' => 3, 'highlight' => true],
                'dataset' => [
                    [
                        'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                        'display' => 'value',
                        //'prefetch' => Url::to(['requests/locationlist']),
                        //'limit' => 10,
                        'templates' => [
                            'notFound' => '<div class="text-danger" style="padding:0 8px">Unable to find repositories for selected query.</div>',
                            'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                        ],
                        'remote' => [
                            'url' => Url::to(['requests/locationlist']) . '?q=%QUERY',
                            'wildcard' => '%QUERY'
                        ]
                    ]
                ]
            ]);
            ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="file-upload">
                <?php
                $image_upload_wrap_div = ($model->logo) ? 'display: none;' : 'display: block;';
                $file_upload_content_div = ($model->logo) ? 'display: block;' : 'display: none;';
                $image_url = ($model->logo) ? Yii::$app->urlManager->baseUrl . '/uploads/society/' . $model->logo : '#';
                $image_name = ($model->logo) ? $model->logo : 'Uploaded Image';
                ?>
                <button class="file-upload-btn btn btn-outline-secondary" type="button" onclick="$('.file-upload-input').trigger('click')">Add Image</button>
                <div class="image-upload-wrap" style="<?= $image_upload_wrap_div; ?>">
                    <?= $form->field($model, 'logo')->fileInput(['class' => 'file-upload-input', 'accept' => 'image/*', 'onchange' => 'readURL(this);'])->label(false); ?>
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
        $('.file-upload-input').val('');
    }
    $('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
    });
</script>