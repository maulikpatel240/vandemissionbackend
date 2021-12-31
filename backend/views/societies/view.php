<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use newerton\fancybox3\FancyBox;

/* @var $this yii\web\View */
/* @var $model backend\models\States */
$this->title = $model->name0->english;

echo FancyBox::widget();
$imagesrc = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/society/' . $model->logo, ['width' => '100', 'height' => '100']);
$imagehtml = Html::a($imagesrc, Yii::$app->urlManager->baseUrl . '/uploads/society/' . $model->logo, ['data-fancybox' => true]);
?>
<div class="societies-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'name',
                'value' => $model->name0->english,
            ],
            [
                'attribute' => 'logo',
                'value' => $imagehtml,
                'format' => 'raw',
            ],
            'headquarters',
            'status',
            'status_at',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
