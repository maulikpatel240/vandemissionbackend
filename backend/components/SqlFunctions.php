<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;
use backend\models\PagesCategories;
use backend\models\GroupsCategories;
use backend\models\BlogsCategories;
use backend\models\JobCategories;
use backend\models\ProductsCategories;
use backend\models\Societies;

class SqlFunctions extends Component {

    public function init() {
        parent::init();
    }

    public function sqlTable($modelNew) {
        $typeArray = ($modelNew->type) ? explode(',', $modelNew->type) : [];
        if (in_array('pages_categories', $typeArray)) {
            $Categories = PagesCategories::find()->where(['name' => $modelNew->id])->one();
            if (empty($Categories)) {
                $Categories = new PagesCategories();
            }
            $Categories->name = $modelNew->id;
            $Categories->status_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->created_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->updated_at = Yii::$app->BackFunctions->currentDateTime();
            if (!$Categories->save()) {
                echo '<pre>';
                print_r($Categories->getErrors());
                echo '</pre>';
                exit;
            }
        }
        
        if (in_array('groups_categories', $typeArray)) {
            $Categories = GroupsCategories::find()->where(['name' => $modelNew->id])->one();
            if (empty($Categories)) {
                $Categories = new GroupsCategories();
            }
            $Categories->name = $modelNew->id;
            $Categories->status_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->created_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->updated_at = Yii::$app->BackFunctions->currentDateTime();
            if (!$Categories->save()) {
                echo '<pre>';
                print_r($Categories->getErrors());
                echo '</pre>';
                exit;
            }
        }
        
        if (in_array('blogs_categories', $typeArray)) {
            $Categories = BlogsCategories::find()->where(['name' => $modelNew->id])->one();
            if (empty($Categories)) {
                $Categories = new BlogsCategories();
            }
            $Categories->name = $modelNew->id;
            $Categories->status_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->created_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->updated_at = Yii::$app->BackFunctions->currentDateTime();
            if (!$Categories->save()) {
                echo '<pre>';
                print_r($Categories->getErrors());
                echo '</pre>';
                exit;
            }
        }
        
        if (in_array('job_categories', $typeArray)) {
            $Categories = JobCategories::find()->where(['name' => $modelNew->id])->one();
            if (empty($Categories)) {
                $Categories = new JobCategories();
            }
            $Categories->name = $modelNew->id;
            $Categories->status_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->created_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->updated_at = Yii::$app->BackFunctions->currentDateTime();
            if (!$Categories->save()) {
                echo '<pre>';
                print_r($Categories->getErrors());
                echo '</pre>';
                exit;
            }
        }
        
        if (in_array('products_categories', $typeArray)) {
            $Categories = ProductsCategories::find()->where(['name' => $modelNew->id])->one();
            if (empty($Categories)) {
                $Categories = new ProductsCategories();
            }
            $Categories->name = $modelNew->id;
            $Categories->status_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->created_at = Yii::$app->BackFunctions->currentDateTime();
            $Categories->updated_at = Yii::$app->BackFunctions->currentDateTime();
            if (!$Categories->save()) {
                echo '<pre>';
                print_r($Categories->getErrors());
                echo '</pre>';
                exit;
            }
        }
        
        if (in_array('societies', $typeArray)) {
            $Societies = Societies::find()->where(['name' => $modelNew->id])->one();
            if (empty($Societies)) {
                $Societies = new Societies();
            }
            $Societies->name = $modelNew->id;
            $Societies->status_at = Yii::$app->BackFunctions->currentDateTime();
            $Societies->created_at = Yii::$app->BackFunctions->currentDateTime();
            $Societies->updated_at = Yii::$app->BackFunctions->currentDateTime();
            if (!$Societies->save()) {
                echo '<pre>';
                print_r($Societies->getErrors());
                echo '</pre>';
                exit;
            }
        }
    }

    public function viewListArray($model, $table_name) {
        if ($model && $table_name) {
            $table_name_array = ['pages_categories', 'groups_categories', 'blogs_categories', 'job_categories', 'products_categories']; 
            if (in_array($table_name, $table_name_array)) {
                $array = [
                    [
                        'attribute' => 'name',
                        'label' => 'English',
                        'format' => 'raw',
                        'value' => ucfirst($model->name0->english),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => 'Gujarati',
                        'format' => 'raw',
                        'value' => ucfirst($model->name0->gujarati),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => 'Hindi',
                        'format' => 'raw',
                        'value' => ucfirst($model->name0->hindi),
                    ]
                ];
                return $array;
            }
        }
        return;
    }

}
