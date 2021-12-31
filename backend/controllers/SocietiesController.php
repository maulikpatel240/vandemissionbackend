<?php

namespace backend\controllers;

use Yii;
use backend\models\Societies;
use backend\models\SocietiesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\BaseController;
use yii\web\UploadedFile;
/**
 * SocietiesController implements the CRUD actions for Societies model.
 */
class SocietiesController extends BaseController
{
    const ACTIVE = 'Active';
    const INACTIVE = 'Inactive';
    const DELETE = 'Delete';
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Societies models.
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
        $searchModel = new SocietiesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Countries model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
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
            $model = new Societies();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $model->logo = UploadedFile::getInstance($model, 'logo');
                if($model->logo){
                    $model->upload();
                }
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
            if ($model->load(Yii::$app->request->post())) {
                $model->logo = UploadedFile::getInstance($model, 'logo');
                if($model->logo){
                    $model->deleteImage($model->getOldAttribute('logo'));
                    $model->upload();
                }else{
                    $model->logo = $model->getOldAttribute('logo');
                }
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
     * Finds the Societies model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Societies the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Societies::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
