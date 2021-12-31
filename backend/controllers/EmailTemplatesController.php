<?php

namespace backend\controllers;

use Yii;
use backend\models\EmailTemplates;
use backend\models\EmailTemplatesSearch;
use backend\models\Templatefield;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\BaseController;
/**
 * EmailTemplatesController implements the CRUD actions for EmailTemplates model.
 */
class EmailTemplatesController extends BaseController
{
    const ACTIVE = 'Active';
    const INACTIVE = 'Inactive';
    const DELETE = 'Yes';
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
     * Lists all EmailTemplates models.
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
        $searchModel = new EmailTemplatesSearch();
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
        $model = new EmailTemplates();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->status_at = Yii::$app->BackFunctions->currentDateTime();
            $model->created_at = Yii::$app->BackFunctions->currentDateTime();
            $model->updated_at = Yii::$app->BackFunctions->currentDateTime();
            $model->save();
            $vField = [
                [
                    'field' => '{{logo}}',
                    'description' => 'Web site logo image required',
                    'is_default' => '1'
                ],
                [
                    'field' => '{{site_url}}',
                    'description' => 'Web site url example. https://example.com',
                    'is_default' => '1'
                ],
                [
                    'field' => '{{site_name}}',
                    'description' => 'Web site name required',
                    'is_default' => '1'
                ],
                [
                    'field' => '{{site_address}}',
                    'description' => 'Web site address or location',
                    'is_default' => '1'
                ],
                [
                    'field' => '{{copyright}}',
                    'description' => 'copyright text required',
                    'is_default' => '1'
                ],
                [
                    'field' => '{{name}}',
                    'description' => 'Receive email user name required',
                    'is_default' => '1'
                ],
            ];
            if($vField){
                foreach($vField as $row){
                    $fieldmodel = new Templatefield();
                    $fieldmodel->email_template_id = $model->id;
                    $fieldmodel->field = $row['field'];
                    $fieldmodel->description = $row['description'];
                    $fieldmodel->is_default = $row['is_default'];
                    $fieldmodel->created_at = date('Y-m-d H:i:s');
                    $fieldmodel->save();
                }
            }
            return $this->redirect(['index']);
        }
        return $this->render('create', [
                    'model' => $model
        ]);
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
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->updated_at = Yii::$app->BackFunctions->currentDateTime();
            $model->save();
            return $this->redirect(['index']);
        }
        
        return $this->render('update', [
                    'model' => $model
        ]);
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
     * Finds the EmailTemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailTemplates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmailTemplates::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionAddfield($id){
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $model = new Templatefield();
        $model->email_template_id = $id;
        $model->is_default = '0';
        $model->created_at = date('Y-m-d');
        if ($model->load(Yii::$app->request->post())) {
            $data = [];
            $Templatefield = Templatefield::find()->where(['field'=>$model->field,'email_template_id'=>$model->email_template_id])->one();
            if($Templatefield){
                $data['result'] = 2;
                $data['message'] = 'Field already exist. Please try another one.';
                $data['id'] = $Templatefield->id;
                $data['email_template_id'] = $Templatefield->email_template_id;
                $data['field'] = $Templatefield->field;
                $data['description'] = $Templatefield->description;
                $data['is_default'] = $Templatefield->is_default;
            }else{
                $model->save();
                $data['result'] = 1;
                $data['message'] = 'Successfully inserted';
                $data['id'] = $model->id;
                $data['email_template_id'] = $model->email_template_id;
                $data['field'] = $model->field;
                $data['description'] = $model->description;
                $data['is_default'] = $model->is_default;
            }
            return $this->asJson($data);
        }
        return $this->renderAjax('_addfield', [
                    'model' => $model,
                    'email_template_id' => $id,
        ]);        
    }
    public function actionFieldlist($id){
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $model = Templatefield::find()->where(['email_template_id'=>$id])->all();
        return $this->renderPartial('_fieldlist', [
                    'model' => $model,
                    'email_template_id' => $id,
        ]);
    }
    public function actionDeletefield($id){
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Templatefield::findOne($id)->delete();
        return true;
    }
    
}
