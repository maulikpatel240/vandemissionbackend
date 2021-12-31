<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use newerton\fancybox3\FancyBox;

/* @var $this yii\web\View */
/* @var $model backend\models\Districts */

$this->title = $model->id;

echo FancyBox::widget();
$imagesrc = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/map/district/' . $model->map, ['width' => '100', 'height' => '100']);
$imagehtml = Html::a($imagesrc, Yii::$app->urlManager->baseUrl . '/uploads/map/district/' . $model->map, ['data-fancybox' => true]);
?>
<div class="districts-view">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'country_id',
                'value' => $model->country->english,
            ],
            [
                'attribute' => 'state_id',
                'value' => $model->state->english,
            ],
            [
                'attribute' => 'map',
                'value' => $imagehtml,
                'format' => 'raw',
            ],
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
    ])
    ?>
</div>
