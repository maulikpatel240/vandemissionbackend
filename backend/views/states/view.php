<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use newerton\fancybox3\FancyBox;

/* @var $this yii\web\View */
/* @var $model backend\models\States */
$this->title = $model->english;

echo FancyBox::widget();
$imagesrc = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/map/state/' . $model->map, ['width' => '100', 'height' => '100']);
$imagehtml = Html::a($imagesrc, Yii::$app->urlManager->baseUrl . '/uploads/map/state/' . $model->map, ['data-fancybox' => true]);
?>
<div class="states-view">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'country_id',
                'value' => $model->country->english,
            ],
            'lang_key',
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
            'bounding_box',
            'status',
            'status_at',
            'created_at',
            'updated_at'
        ],
    ])
    ?>
</div>
