<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PagesCategories */
$array1 = [
    'id',
    'status',
    'status_at',
    'created_at',
    'updated_at'
];
$array2 = Yii::$app->SqlFunctions->viewListArray($model, 'products_categories');
$merge = array_merge($array1, $array2);
?>
<div class="pages-categories-view">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => $merge,
    ])
    ?>
</div>
