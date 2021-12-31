<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use newerton\fancybox3\FancyBox;
/* @var $this yii\web\View */
/* @var $model backend\models\Config */

$this->title = $model->name;
echo FancyBox::widget();

$value = $model->value;
if ($model->type == 'File') {
    $imagesrc = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/settings/' . $model->value, ['width' => '100', 'height' => '100']);
    $value = Html::a($imagesrc, Yii::$app->urlManager->baseUrl . '/uploads/settings/' . $model->value, ['data-fancybox' => true]);
}
?>
<div class="config-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'value',
                'value' => $value,
                'format' => 'raw',
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
