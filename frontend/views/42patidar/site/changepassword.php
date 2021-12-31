<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'Change Password');
$moduletypePath = "";
$moduletypeUrl = "";
if ($user->type == 'Owner') {
    $moduletypePath = $_ownerPath;
    $moduletypeUrl = $_ownerUrl;
} elseif ($user->type == 'Staff') {
    $moduletypePath = $_staffPath;
    $moduletypeUrl = $_staffUrl;
}
$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Settings'), 'url' => [$moduletypePath . 'settings']],
    $this->title
];
?>
<div class="theme_loader_fix loader_modalform" style="display: none;">
    <div class="cell preloader5 loader-block divbox">
        <div class="circle-5 l"></div>
        <div class="circle-5 m"></div>
        <div class="circle-5 r"></div>
    </div>
</div>
<script>$('body').addClass('bl_changepassword');</script>
<nav aria-label="breadcrumb" class="top_breadcrumb">
    <?php
    echo Breadcrumbs::widget([
        'tag' => "ol",
        'options' => ['class' => 'breadcrumb'],
        'homeLink' => [
            'label' => Yii::t('app', 'Home'),
            'url' => $moduletypeUrl,
        ],
        'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
        'activeItemTemplate' => "<li class=\"breadcrumb-item active\">{link}</li>\n",
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]);
    ?>
</nav>
<main class="bl_main_bg">
    <div class="container-fluid">
        <?php $form = ActiveForm::begin(['action' => ['/'.$_module['unique_name'].'/site/ajax-change-password'],'id' => 'changepassword-form']); ?>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 m-auto">
                <div class="card card-white shadow">
                    <div class="card-header">
                        <h3 class="card-title"> <?= $this->title; ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="position-relative">
                            <?= $form->field($model, 'oldpass', ['template' => '{label}<div class="input-group mb-3">{input}<div class="input-group-append"><span class="input-group-text"><i toggle="#changepasswordform-oldpass" class="fa fa-fw fa-eye field-icon toggle-password"></i></span></div></div>{error}'])->passwordInput() ?>
                        </div>
                        <div class="position-relative">
                            <?= $form->field($model, 'newpass', ['template' => '{label}<div class="input-group mb-3">{input}<div class="input-group-append"><span class="input-group-text"><i toggle="#changepasswordform-newpass" class="fa fa-fw fa-eye field-icon toggle-password"></i></span></div></div>{error}'])->passwordInput() ?>
                        </div>

                        <div class="position-relative">
                            <?= $form->field($model, 'repeatnewpass', ['template' => '{label}<div class="input-group mb-3">{input}<div class="input-group-append"><span class="input-group-text"><i toggle="#changepasswordform-repeatnewpass" class="fa fa-fw fa-eye field-icon toggle-password"></i></span></div></div>{error}'])->passwordInput() ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?= Html::a(Yii::t('app', 'Cancel'), [$moduletypePath], ['class' => 'btn btn-theme waves-effect']) ?>
                        <?=
                        Html::submitButton(Yii::t('app', 'Save'), [
                            'class' => 'btn btn-theme waves-effect float-right'
                        ])
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</main>
<?php
$this->registerJs("$(document).ready(function () {
    $('.toggle-password').click(function() {
        $(this).toggleClass('fa-eye fa-eye-slash');
        var input = $($(this).attr('toggle'));
        if (input.attr('type') == 'password') {
            input.attr('type', 'text');
        } else {
            input.attr('type', 'password');
        }
    });
});");
?>
<script>
$(document).on("submit", "#changepassword-form", function () {
        var data;
        var data = new FormData(this);
        var url = $(this).attr('action');
        $.ajax({
            url: url, // Url to which the request is send
            type: "POST", // Type of request to be send, called as method
            data: data,
            contentType: false, // The content type used when sending data to the server.
            cache: false, // To unable request pages to be cached
            processData: false, // To send DOMDocument or non processed data file it is set to false
            dataType: 'json',
            beforeSend: function () {
                $('.loader_modalform').show();
            },
            complete: function () {
                $('.loader_modalform').hide();
            },
            success: function (response)   // A function to be called if request succeeds
            {
                if (response.status == 200) {
                     Swal.fire(
                        '',
                        response.message,
                        'success'
                        );
                } else {
                    Swal.fire(
                            '',
                            response.message,
                            'error'
                            );
                }
            }
        });
        return false;
    });
    </script>