<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use backend\models\Model;
use backend\models\Permission;
use backend\models\PermissionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PermissionController implements the CRUD actions for Permission model.
 */
class PermissionController extends \backend\components\BaseController {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Permission models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        $pageSize = Yii::$app->params['PAGE_SIZE'];
        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    public function actionStatusupdate($id) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            $model->status = ($model->status == self::ACTIVE) ? self::INACTIVE : self::ACTIVE;
            $model->status_at = Yii::$app->BackFunctions->currentDateTime();
            return $model->save();
        }
    }

    public function actionApplystatus() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if (Yii::$app->request->isAjax) {
            if (isset($_POST['applyoption'])) {
                if ($_POST['applyoption'] == self::DELETE) {
                    if(empty(Yii::$app->BackFunctions->checkaccess('delete', Yii::$app->controller->id))){
                        throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
                    }
                    if (isset($_POST['keylist']) && $_POST['keylist']) {
                        foreach ($_POST['keylist'] as $id) {
                            $this->findModel($id)->delete();
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Creates a new Permission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        if (Yii::$app->request->isAjax) {
            $model = new Permission();
            
            $model->title = 'List';
            $model->name = 'list';
            $model->controller = '';
            $model->action = 'index';
            
            $model1 = new Permission();
            $model1->title = 'Create';
            $model1->name = 'create';
            $model1->controller = '';
            $model1->action = 'create';
            
            $model2 = new Permission();
            $model2->title = 'Update';
            $model2->name = 'update';
            $model2->controller = '';
            $model2->action = 'update';
            
            $model3 = new Permission();
            $model3->title = 'View';
            $model3->name = 'view';
            $model3->controller = '';
            $model3->action = 'view';
            
            $model4 = new Permission();
            $model4->title = 'Delete';
            $model4->name = 'delete';
            $model4->controller = '';
            $model4->action = 'delete';
            
            $model5 = new Permission();
            $model5->title = 'status';
            $model5->name = 'status';
            $model5->controller = '';
            $model5->action = '';
            
            $modelPermission = [$model,$model1,$model2,$model3,$model4];
            
            if ($model->load(Yii::$app->request->post())) {
                $postdata = Yii::$app->request->post();
                
                $oldIDs = ArrayHelper::map($modelPermission, 'id', 'id');
                $modelPermission = Model::createMultiple(Permission::classname());
                Model::loadMultiple($modelPermission, Yii::$app->request->post());
                if($modelPermission){
                    foreach ($modelPermission as $key => $value){
                        $modelNew = Permission::find()->where(['name'=>$value->name,'module_id'=>$model->module_id])->one();;
                        if(empty($modelNew)){
                            $modelNew = new Permission();
                        }
                        $modelNew->type = 'Backend';
                        $modelNew->module_id = $model->module_id;
                        $modelNew->title = $value->title;
                        $modelNew->name = $value->name;
                        $modelNew->controller = $value->controller;
                        $modelNew->action = $value->action;
                        $modelNew->save();
                    }
                }
                return true;
            }

            return $this->renderAjax('create', [
                        'model' => $model,
                        'modelPermission' => (empty($modelPermission)) ? [$model,$model1,$model2,$model3,$model4] : $modelPermission
            ]);
        }
    }

    /**
     * Updates an existing Permission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $model->id;
            }
            return $this->renderAjax('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Permission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Permission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Permission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Permission::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
