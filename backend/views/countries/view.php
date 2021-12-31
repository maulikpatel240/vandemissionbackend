<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Countries */
?>
<div class="countries-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'lang_key',
            'english:ntext',
            'gujarati:ntext',
            'hindi:ntext',
            'latitude',
            'longitude',
            'status',
            'status_at',
            'created_at',
            'updated_at'
        ],
    ]) ?>

</div>
