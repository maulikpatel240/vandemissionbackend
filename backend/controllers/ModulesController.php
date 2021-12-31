<?php

namespace backend\controllers;

use Yii;
use backend\models\Modules;
use backend\models\ModulesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ModulesController implements the CRUD actions for Modules model.
 */
class ModulesController extends \backend\components\BaseController {

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
            throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
        }

        $pageSize = Yii::$app->params['PAGE_SIZE'];
        $searchModel = new ModulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStatusupdate($id) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
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
                        throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
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
                        throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
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
                        throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
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
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
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
            throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
        }
        if (Yii::$app->request->isAjax) {
            $model = new Modules();
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
            throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
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
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',"You don't have permission to access on this role.");
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
        if (($model = Modules::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionChildType() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            $list = array('Menu' => 'Menu', 'Submenu' => 'Submenu', 'Subsubmenu' => 'Subsubmenu');
            $selected = null;
            if ($id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $type) {
                    $out[] = ['id' => $type, 'name' => $type];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionChildMenu() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $menu_id = empty($_POST['depdrop_parents'][0]) ? null : $_POST['depdrop_parents'][0];
            $type = empty($_POST['depdrop_parents'][1]) ? null : $_POST['depdrop_parents'][1];
            $parent_menu_id = empty($_POST['depdrop_parents'][2]) ? null : $_POST['depdrop_parents'][2];
            $list = [];
            if ($type == 'Submenu' && $menu_id && empty($parent_menu_id)) {
                $list = ArrayHelper::map(Modules::find()->where(['menu_id' => $menu_id, 'parent_menu_id' => 0, 'parent_submenu_id' => 0])->andWhere(['!=', 'title', ''])->asArray()->all(), 'id', 'title');
            } elseif ($type == 'Subsubmenu' && $menu_id && ($parent_menu_id == 0 || empty($parent_menu_id))) {
                $list = ArrayHelper::map(Modules::find()->where(['menu_id' => $menu_id, 'parent_menu_id' => 0, 'parent_submenu_id' => 0])->andWhere(['!=', 'title', ''])->asArray()->all(), 'id', 'title');
            }

            $selected = null;
            if ($menu_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $key => $value) {
                    $out[] = ['id' => $key, 'name' => $value];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionChildSubmenu() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $menu_id = empty($_POST['depdrop_parents'][0]) ? null : $_POST['depdrop_parents'][0];
            $type = empty($_POST['depdrop_parents'][1]) ? null : $_POST['depdrop_parents'][1];
            $parent_menu_id = empty($_POST['depdrop_parents'][2]) ? null : $_POST['depdrop_parents'][2];
            $list = [];
            if ($type == 'Subsubmenu' && ($parent_menu_id != 0 || !empty($parent_menu_id))) {
                $list = ArrayHelper::map(Modules::find()->where(['menu_id' => $menu_id, 'parent_menu_id' => $parent_menu_id, 'parent_submenu_id' => 0])->andWhere(['!=', 'title', ''])->asArray()->all(), 'id', 'title');
            }

            $selected = null;
            if ($menu_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $key => $value) {
                    $out[] = ['id' => $key, 'name' => $value];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

}
