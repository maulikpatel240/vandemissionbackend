<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\editors\Summernote;
use yii\bootstrap5\Modal;
use yii\helpers\Url;

$id = (!$model->isNewRecord)?$model->id:"";
/* @var $this yii\web\View */
/* @var $model backend\models\EmailTemplates */
/* @var $form yii\widgets\ActiveForm */
if ($model->isNewRecord) {
    $model->html =  Yii::$app->view->renderFile('@app/views/email-templates/_htmltemplate.php');
}
?>

<div class="email-templates-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?= $form->field($model, 'type')->dropDownList(['Backend' => 'Backend', 'Website' => 'Website', 'App' => 'App', 'Common' => 'Common'], ['prompt' => '']) ?>
            
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'code')->textInput(['maxlength' => true])->label('code <p class="font-weight-normal mb-0">(Capital letter and no space required  ex: WELCOME_PAGE)</p>') ?>
            
            
            <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Default Field</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{logo}}</td>
                            <td>Web site logo image</td>
                        </tr>
                        <tr>
                            <td>{{site_url}}</td>
                            <td>Web site url example. https://example.com</td>
                        </tr>
                        <tr>
                            <td>{{site_name}}</td>
                            <td>Web site Name </td>
                        </tr>
                        <tr>
                            <td>{{site_address}}</td>
                            <td>Web site address or location</td>
                        </tr>
                        <tr>
                            <td>{{copyright_text}}</td>
                            <td>Web site copyright text message</td>
                        </tr>
                        <tr>
                            <td>{{name}}</td>
                            <td>Receive mesage name</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 m-auto">  
            <?=
            $form->field($model, 'html')->widget(Summernote::class, [
                'styleWithSpan' => true,
                    // other widget settings
            ])
            ?>
        </div>
    </div>
    <?php if (!$model->isNewRecord) { ?>
        <div class="row">
            <div class="col-md-12" id="fieldlist" data-url="<?= Url::to(['email-templates/fieldlist'],true) ?>">
            </div>
        </div>
    <?php } ?>
    <div class="form-group text-center">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?php if (!$model->isNewRecord) { ?>
        <?= Html::a('Add New Field', FALSE, ['value' => Url::to(['email-templates/addfield', 'id' => $model->id]), 'title' => 'Add field', 'class' => 'btn btn-info showmodelfield']) ?>
        <?php } ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
Modal::begin([
    'title' => 'Add field name',
    'id' => 'modelAddfield',
    'size' => 'modal-md',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE],
    'closeButton' => ['id' => 'close-button'],
]);
echo \Yii::$app->view->renderFile('@app/views/temp/loader.php', ['theme_loader' => 'div', 'loader' => 'loader_div', 'display' => 'none']);
echo "<div id='modalContent'></div>";
Modal::end();
?>
<script>
    $(document).on('click', '.showmodelfield', function () {
        $('#modelAddfield').find('#modalContent').html('');
        document.getElementById('modelAddfield-label').innerHTML = $(this).attr('title');
        $.ajax({
            url: $(this).attr('value'),
            beforeSend: function () {
                $('#modelAddfield').modal('show')
                $('#modelAddfield').find('#modalContent').html('');
                $('.loader_div').show();
            },
            success: function (response) {
                $('#modelAddfield').find('#modalContent').html(response);
            },
            complete: function () {
                $('.loader_div').hide();
            }
        });
    });
    $(document).ready(function () {
        fieldlist('<?=$id?>');
    });
    function fieldlist(id) {
        var url = $('#fieldlist').attr('data-url');
        $('#fieldlist').load(url+'?id='+id, function () {
            $('.loader_div').hide();
        });
    }
</script>
