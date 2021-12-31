<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $model backend\models\Role */

\yii\web\YiiAsset::register($this);
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">  
                <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'group'=>true,
                        'label'=>'SECTION 1: Identification Information',
                        'rowOptions'=>['class'=>'table-info']
                    ],
                    [
                        'columns' => [
                            [
                                'attribute'=>'type',
                                'valueColOptions'=>['style'=>'width:30%']
                            ],
                            [
                                'attribute'=>'code',
                                'valueColOptions'=>['style'=>'width:30%']
                            ],
                        ],
                    ],
                    [
                        'columns' => [
                            [
                                'attribute'=>'subject',
                                'valueColOptions'=>['style'=>'width:30%']
                            ],
                            [
                                'attribute'=>'status',
                                'valueColOptions'=>['style'=>'width:30%']
                            ],
                        ],
                    ],
                    [
                        'group'=>true,
                        'label'=>'SECTION 2: Template',
                        'rowOptions'=>['class'=>'table-info'],
                        //'groupOptions'=>['class'=>'text-center']
                    ]

                ],
                'mode'=>DetailView::MODE_VIEW,
                'bordered' => true,
                'striped' => false,
                'condensed' => false,
                'responsive' => true,
                'hover' => false,
                'hAlign'=> 'left',
                'vAlign'=> 'middle',
                'fadeDelay'=> '30',
            ])
            ?>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="border pt-4">
                    <?=$model->html;?>
                </div>
            </div>
        </div>
    </div>
</section>

