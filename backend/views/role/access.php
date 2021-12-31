<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use kartik\switchinput\SwitchInput;
use yii\widgets\Breadcrumbs;
use backend\models\RoleAccess;

/* @var $this yii\web\View */
/* @var $model backend\models\Role */

$this->title = Yii::t('app', 'Access');
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="col-sm-6">
                <?php
                $this->params['breadcrumbs'] = array();
                $this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
                $this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => false];
                echo Breadcrumbs::widget([
                    'tag' => 'ol',
                    'options' => ['class' => 'breadcrumb float-sm-end'],
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'itemTemplate' => '<li class="breadcrumb-item">{link}</li>', // template for all links
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);
                ?>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">  
                <?php $form = ActiveForm::begin(['id' => 'roleForm']); ?>
                <div class="card card-dark">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="float-end">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" id="permissionid" class="custom-control-input "  value="1" aria-invalid="false">
                                        <label class="custom-control-label" for="permissionid">Select all</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="float-start">
                                    <h4 class="title"><?= Yii::t('yii', 'Role') . ' : ' . $rolemodel->name ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            if ($permissionmodel) {
                                $i = 0;
                                foreach ($permissionmodel as $key => $value) {
                                    $RoleAccess = RoleAccess::find()->where(['role_id' => $rolemodel->id, 'permission_id' => $value['id']])->one();
                                    if ($RoleAccess) {
                                        $model->access = [$i => $RoleAccess->access];
                                    }
                                    if ($value['module_name_unique']) {
                                        ?>
                                        <div class="col-sm-12 col-md-12 col-lg-12 bg-light p-2 mb-3">
                                            <div class="float-start"><h5 class="font-weight-bold"><?= Yii::t('yii', 'Access Control') . ' : ' . $value['module_name_unique'] ?></h5></div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="col-sm-12 col-md-3 col-lg-3">
                                        <div class="d-none">
                                            <?= $form->field($model, 'permission_id[' . $i . ']')->hiddenInput(['value' => $value['id']])->label(false) ?>
                                        </div>
                                        <?= $form->field($model, 'access[' . $i . ']')->checkBox(['label' => '', 'id' => 'roleaccess-access-' . $i,'class'=>"custom-control-input checkbox"])->label($value['name']); ?>
                                    </div>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                        </div> 
                    </div> 
                    <div class="card-footer text-center">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Cancel', ['role/index'], ['class' => 'btn btn-danger']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</section>
<script>
    $("#permissionid").click(function () {
        $(".checkbox").prop("checked", $(this).prop("checked"));
    });
    var total_checkbox = $(".checkbox").length;
    $(".checkbox").click(function () {
        if (!$(this).prop("checked")) {
            $("#permissionid").prop("checked", false);
        }else{
            var selected = [];
            $(".checkbox:checked").each(function() {
                selected.push($(this).val());
            });
            if(total_checkbox == selected.length){
                $("#permissionid").prop("checked", true);
            }
        }
    });
</script>
