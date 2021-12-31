<?php

namespace backend\controllers;

use Yii;
use backend\models\Permission;
use backend\models\Role;
use backend\models\RoleSearch;
use backend\models\RoleAccess;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\BaseController;
/**
 * RoleController implements the CRUD actions for Role model.
 */
class RoleController extends BaseController {

    const ACTIVE = 'Active';
    const INACTIVE = 'Inactive';
    const DELETE = 'Yes';

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
     * Lists all Role models.
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
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStatusupdate($id) {
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
        if (Yii::$app->request->isAjax) {
            if (isset($_POST['applyoption'])) {
                if ($_POST['applyoption'] == self::ACTIVE) {
                    if(empty(Yii::$app->BackFunctions->checkaccess('statusupdate', Yii::$app->controller->id))){
                        throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
                    }
                    if (isset($_POST['keylist']) && $_POST['keylist']) {
                        foreach ($_POST['keylist'] as $id) {
                            $model = $this->findModel($id);
                            $model->status = self::ACTIVE;
                            $model->status_at = Yii::$app->BackFunctions->currentDateTime();
                            $model->save();
                        }
                    }
                } elseif ($_POST['applyoption'] == self::INACTIVE) {
                    if(empty(Yii::$app->BackFunctions->checkaccess('statusupdate', Yii::$app->controller->id))){
                        throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
                    }
                    if (isset($_POST['keylist']) && $_POST['keylist']) {
                        foreach ($_POST['keylist'] as $id) {
                            $model = $this->findModel($id);
                            $model->status = self::INACTIVE;
                            $model->status_at = Yii::$app->BackFunctions->currentDateTime();
                            $model->save();
                        }
                    }
                } elseif ($_POST['applyoption'] == self::DELETE) {
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
     * Displays a single Role model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                        'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Role model.
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
            $model = new Role();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $model->status_at = Yii::$app->BackFunctions->currentDateTime();
                $model->created_at = Yii::$app->BackFunctions->currentDateTime();
                $model->updated_at = Yii::$app->BackFunctions->currentDateTime();
                $model->save();
                return $model->id;
            }
            return $this->renderAjax('create', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Role model.
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
                $model->updated_at = Yii::$app->BackFunctions->currentDateTime();
                $model->save();
                return $model->id;
            }

            return $this->renderAjax('update', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Role model.
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
     * Finds the Role model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Role the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Role::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAccess($id) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        $rolemodel = $this->findModel($id);
        //$permissionmodel = Permission::find()->all();
        $permissionmodel = \Yii::$app->db->createCommand("SELECT CASE WHEN ROW_NUMBER() OVER(PARTITION BY module_id ORDER BY action) = 1 
            THEN (select title as module_name from modules WHERE id = module_id) ELSE NULL END AS 'module_name_unique'
            , id
            , module_id
            , name
            , controller
            , action
        FROM permission
        ORDER BY module_id,controller, action")->queryall();
        $model = new RoleAccess();
        $model->role_id = $id;
        if ($model->load(Yii::$app->request->post())) {
            if($model->permission_id){
                foreach($model->permission_id as $key => $value){
                    $checkPermissionId = Permission::find()->where(['id'=>$value])->count();
                    if($checkPermissionId){
                        $modelAll = RoleAccess::find()->where(['role_id' => $id, 'permission_id'=>$value])->one();
                        if (empty($modelAll)) {
                            $modelAll = new RoleAccess();
                            $modelAll->role_id = $id;
                        }
                        $modelAll->permission_id = $value;
                        $modelAll->access = $model->access[$key];    
                        $modelAll->created_at  = date("Y-m-d H:i:s"); 
                        $modelAll->save();
                    }
                }
            }
            Yii::$app->session->setFlash('success', Yii::$app->BackFunctions->message('update'));
            return $this->redirect(['access', 'id' => $model->role_id]);
        }
        return $this->render('access', [
                    'model' => $model,
                    'rolemodel' => $rolemodel,
                    'permissionmodel' => $permissionmodel,
        ]);
    }

}
