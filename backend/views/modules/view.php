<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Modules;
/* @var $this yii\web\View */
/* @var $model backend\models\Modules */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Modules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">  
                <div class="card card-dark">
                    <div class="card-header">
                        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
                    </div>
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'functionality',
                                'url',
                                'type',
                                [                    
                                    'label' => 'Module type',
                                    'value' => function ($model, $widget){
                                        $menu = Modules::find()->select(['title'])->where(['id' => $model->menu_id,'parent_menu_id' => 0, 'parent_submenu_id' => 0])->andWhere(['!=', 'title', ''])->one();
                                        return ($menu)?$menu->title:"-";
                                    }
                                ],
                                [                    
                                    'label' => 'Menu',
                                    'value' => function ($model, $widget){
                                        $menu = Modules::find()->select(['title'])->where(['id' => $model->parent_menu_id, 'parent_submenu_id' => 0])->andWhere(['!=', 'title', ''])->one();
                                        return ($menu)?$menu->title:"-";
                                    }
                                ], 
                                [                    
                                    'label' => 'SubMenu',
                                    'value' => function ($model, $widget){
                                        $menu = Modules::find()->select(['title'])->where(['id' => $model->parent_submenu_id])->andWhere(['!=', 'title', ''])->one();
                                        return ($menu)?$menu->title:"-";
                                    }
                                ],         
                            ],
                        ])
                        ?>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</section>
