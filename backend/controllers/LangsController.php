<?php

namespace backend\controllers;

use Yii;
use backend\models\Langs;
use backend\models\LangsSearch;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use backend\models\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\BaseController;

/**
 * LangsController implements the CRUD actions for Langs model.
 */
class LangsController extends BaseController {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
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
     * Lists all Langs models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        if (empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))) {
            throw new \yii\web\HttpException('403', Yii::$app->params['permission_message']);
        }
        $pageSize = Yii::$app->params['PAGE_SIZE'];
        $searchModel = new LangsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Langs model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        if (empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))) {
            throw new \yii\web\HttpException('403', Yii::$app->params['permission_message']);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                        'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Langs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if (empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))) {
            throw new \yii\web\HttpException('403', Yii::$app->params['permission_message']);
        }
        if (Yii::$app->request->isAjax) {
            $model = new Langs();
            $modelLangs = [$model];
            if ($model->load(Yii::$app->request->post())) {
                $postData = Yii::$app->request->post('Langs', '');
                $oldIDs = ArrayHelper::map($modelLangs, 'id', 'id');
                $modelLangs = Model::createMultiple(Langs::classname());
                Model::loadMultiple($modelLangs, Yii::$app->request->post());
                if ($modelLangs) {
                    foreach ($modelLangs as $key => $value) {
                        $modelNew = Langs::find()->where(['lang_key' => $value->lang_key])->one();
                        if (empty($modelNew)) {
                            $modelNew = new Langs();
                        }
                        $modelNew->type = implode(',', $postData['type']);
                        $modelNew->lang_key = str_replace(' ', '_', strtolower($value->lang_key));
                        $modelNew->english = $value->english;
                        $modelNew->gujarati = $value->gujarati;
                        $modelNew->hindi = $value->hindi;
                        if (!$modelNew->save()) {
                            echo '<pre>';
                            print_r($modelNew->getErrors());
                            echo '</pre>';
                            exit;
                        }
                        Yii::$app->SqlFunctions->sqlTable($modelNew);
                    }
                }
                return true;
            }
            return $this->renderAjax('create', [
                        'model' => $model,
                        'modelLangs' => (empty($modelLangs)) ? [$model] : $modelLangs
            ]);
        }
    }

    /**
     * Updates an existing Langs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if (empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))) {
            throw new \yii\web\HttpException('403', Yii::$app->params['permission_message']);
        }
        if (Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post())) {
                $postData = Yii::$app->request->post('Langs', '');
                $model->type = implode(',', $postData['type']);
                $model->lang_key = str_replace(' ', '_', strtolower($model->lang_key));
                $model->save();
                Yii::$app->SqlFunctions->sqlTable($model);
                return $model->id;
            }
            return $this->renderAjax('update', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Langs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        if (empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))) {
            throw new \yii\web\HttpException('403', Yii::$app->params['permission_message']);
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Langs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Langs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Langs::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
