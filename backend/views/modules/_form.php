<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use kartik\depdrop\DepDrop;
use backend\models\Modules;
use kartik\select2\Select2;
use common\widgets\AjaxForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model backend\models\Modules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="modules-form">

    <?php $form = ActiveForm::begin(['id' => 'myform']); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'controller')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'functionality')->dropDownList(['crud' => 'Crud', 'singleview' => 'Singleview', 'other' => 'Other', 'none' => 'None',], ['prompt' => '']);

        echo $form->field($model, 'menu_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Modules::find()->where(['menu_id' => 0, 'parent_menu_id' => 0, 'parent_submenu_id' => 0, 'status' => 'Active'])->andWhere(['!=', 'title', ''])->asArray()->all(), 'id', 'type'),
            'options' => ['placeholder' => '--Select--'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

        echo $form->field($model, 'type')->widget(DepDrop::classname(), [
            //'data' => ['Menu','Submenu','Subsubmenu'],
            'options' => ['placeholder' => 'Select ...'],
            'type' => DepDrop::TYPE_SELECT2,
            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
            'pluginOptions' => [
                'depends' => ['modules-menu_id'],
                'url' => Url::to(['/modules/child-type']),
                'loadingText' => '',
            ]
        ]);

        echo $form->field($model, 'parent_menu_id')->widget(DepDrop::classname(), [
            // 'data' => ['Menu','Submenu','Subsubmenu'],
            'options' => ['placeholder' => 'Select ...'],
            'type' => DepDrop::TYPE_SELECT2,
            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
            'pluginOptions' => [
                'depends' => ['modules-menu_id', 'modules-type'],
                'url' => Url::to(['/modules/child-menu']),
                'loadingText' => '',
            ]
        ]);

        echo $form->field($model, 'parent_submenu_id')->widget(DepDrop::classname(), [
            // 'data' => ['Menu','Submenu','Subsubmenu'],
            'options' => ['placeholder' => 'Select ...'],
            'type' => DepDrop::TYPE_SELECT2,
            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
            'pluginOptions' => [
                'depends' => ['modules-menu_id', 'modules-type', 'modules-parent_menu_id'],
                'url' => Url::to(['/modules/child-submenu']),
                'loadingText' => '',
            ]
        ]);
    }
    ?>
    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
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
