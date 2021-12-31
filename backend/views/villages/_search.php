<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\VillagesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="villages-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'country_id') ?>

    <?= $form->field($model, 'state_id') ?>

    <?= $form->field($model, 'district_id') ?>

    <?= $form->field($model, 'subdistrict_id') ?>

    <?php // echo $form->field($model, 'block_id') ?>

    <?php // echo $form->field($model, 'english') ?>

    <?php // echo $form->field($model, 'pincode') ?>

    <?php // echo $form->field($model, 'officename') ?>

    <?php // echo $form->field($model, 'code') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'bounding_box') ?>

    <?php // echo $form->field($model, 'map') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'status_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'lang_key') ?>

    <?php // echo $form->field($model, 'gujarati') ?>

    <?php // echo $form->field($model, 'hindi') ?>

    <?php // echo $form->field($model, 'page') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
