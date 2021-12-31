<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ModulesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="modules-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'functionality') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'main_id') ?>

    <?= $form->field($model, 'parent_menu_id') ?>

    <?php // echo $form->field($model, 'parent_submenu_id') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'icon') ?>

    <?php // echo $form->field($model, 'menu_position') ?>

    <?php // echo $form->field($model, 'submenu_position') ?>

    <?php // echo $form->field($model, 'display') ?>

    <?php // echo $form->field($model, 'hiddden') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
