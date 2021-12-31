<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use newerton\fancybox3\FancyBox;
/* @var $this yii\web\View */
/* @var $model backend\models\Subdistricts */

$this->title = $model->english;

echo FancyBox::widget();
$imagesrc = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/map/block/' . $model->map, ['width' => '100', 'height' => '100']);
$imagehtml = Html::a($imagesrc, Yii::$app->urlManager->baseUrl . '/uploads/map/block/' . $model->map, ['data-fancybox' => true]);
?>
<div class="blocks-view">
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
                'attribute' => 'district_id',
                'value' => $model->district->english,
            ],
            'english:ntext',
            'gujarati:ntext',
            'hindi:ntext',
            [
                'attribute' => 'map',
                'value' => $imagehtml,
                'format' => 'raw',
            ],
            'latitude',
            'longitude',
            'bounding_box:ntext',
            'code',
            'status',
            'status_at',
            'created_at',
            'updated_at'
        ],
    ])
    ?>
</div>
