<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $model backend\models\Role */

\yii\web\YiiAsset::register($this);
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">  
                <div class="card card-dark">
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'name',
                                'panel',
                                'status'
                            ],
                        ])
                        ?>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</section>
