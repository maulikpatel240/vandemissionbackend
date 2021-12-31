<?php

namespace backend\controllers;

use Yii;
use backend\models\Users;
use backend\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\BaseController;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\Url;
/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends BaseController
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
     * Lists all Users models.
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
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single Districts model.
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
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();   
        // process ajax delete
        if (Yii::$app->request->isAjax && isset($post['kvdelete'])) {
            echo Json::encode([
                'success' => true,
                'messages' => [
                    'kv-detail-info' => 'The book # 1000 was successfully deleted. ' . 
                        Html::a('<i class="fas fa-hand-right"></i>  Click here', 
                            ['/site/detail-view-demo'], ['class' => 'btn btn-sm btn-info']) . ' to proceed.'
                ]
            ]);
            return;
        }
        // return messages on update of record
        if ($model->load($post) && $model->save()) {
            Yii::$app->session->setFlash('kv-detail-success', 'Success Message');
           // Yii::$app->session->setFlash('kv-detail-warning', 'Warning Message');
        }
        return $this->render('view', [
            'model' => $model,
        ]);
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
            $model = new Users();
            //$model->scenario = 'admin';
            if ($model->load(Yii::$app->request->post())) {
                $model->username = $model->first_name.'_'.$model->last_name;
                $model->aadhaar_card_number = Yii::$app->BackFunctions->currentDateTime();
                $model->status_at = Yii::$app->BackFunctions->currentDateTime();
                $model->created_at = Yii::$app->BackFunctions->currentDateTime();
                $model->updated_at = Yii::$app->BackFunctions->currentDateTime();
                $model->save();
                $model->avatar = UploadedFile::getInstance($model, 'avatar');
                if($model->avatar){
                    $model->upload();
                }
                $model->username = $model->first_name.'_'.$model->last_name.'_'.$model->id;
                $model->authkey = Yii::$app->security->generateRandomString();
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
                if($model->getOldAttribute('email') != $model->email){
                    Yii::$app->BackFunctions->sendmail($model->email);
                }
                if($model->getOldAttribute('phone_number') != $model->phone_number){
                    Yii::$app->BackFunctions->sendotp($model->phone_number);
                }
                $model->avatar = UploadedFile::getInstance($model, 'avatar');
                if($model->avatar){
                    $model->deleteImage($model->getOldAttribute('avatar'));
                    $model->upload();
                }else{
                    $model->avatar = $model->getOldAttribute('avatar');
                }
                
                $model->aadhaar_card_photo = UploadedFile::getInstance($model, 'aadhaar_card_photo');
                if($model->aadhaar_card_photo){
                    $model->deleteAadhaar($model->getOldAttribute('aadhaar_card_photo'));
                    $model->uploadAadhaar();
                }else{
                    $model->aadhaar_card_photo = $model->getOldAttribute('aadhaar_card_photo');
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
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionEditable($id) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
//        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
//            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
//        }
        $model = $this->findModel($id);
        $request = Yii::$app->request; 
        $column = $request->post('column','');
        // return messages on update of record
        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('kv-detail-success', 'Success Message');
           // Yii::$app->session->setFlash('kv-detail-warning', 'Warning Message');
            return $this->redirect(['users/view','id'=>$id]);
        }
        return $this->render('view', [
            'model' => $model,
            'column' => $column,
        ]);
    }
    public function actionPopupmodal($id) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        
        $request = Yii::$app->request; 
        $column = $request->post('column','');
        
        $model = $this->findModel($id);
        $model->scenario = 'popupmodal';
        if ($model->load($request->post())) {
            $output = [];
            $output['result'] = 0;
            $output['message'] = 'Something went wrong';
            $output['column'] = '';
            if ($model->phone_number_verify_otp) {
                if($model->phone_number_verify_otp == $model->sms_code){
                    $model->phone_number_verify =  '1';
                    $model->sms_code =  '';
                    if ($model->save()) {
                        $output['result'] = 1;
                        $output['message'] = 'Success';
                    }
                }else{
                    $output['message'] = 'You have entered wrong OTP.';
                }
                $output['column'] = 'phone_number_verify';
            }elseif ($model->email_verify_code) {
                if($model->email_verify_code == $model->email_code){
                    $model->email_verify =  '1';
                    $model->email_code =  '';
                    if ($model->save()) {
                        $output['result'] = 1;
                        $output['message'] = 'Success';
                    }
                }else{
                    $output['message'] = 'You have entered wrong Code.';
                }
                $output['column'] = 'email_verify';
            }
            $output['url'] = Url::to(['users/view','id'=>$id],true);
            return $this->asJson($output);
            
        }
        return $this->renderAjax('popupmodal', [
            'model' => $model,
            'column' => $column,
        ]);
    }
    
    public function actionResendcode($id) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if(empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))){
            throw new \yii\web\HttpException('403',Yii::$app->params['permission_message']);
        }
        $request = Yii::$app->request; 
        $column = $request->get('c', '');
        $model = $this->findModel($id);
        if($column == 'email_verify'){
            $model->email_code = ''.mt_rand(100000, 999999).'';
            $model->email_verify = '0';
            if(!$model->save()){
                echo '<pre>'; print_r($model);echo '</pre>';exit;
            }
            $field = array();
            $field['{{code}}'] = $model->email_code;
            Yii::$app->BackFunctions->sendmail($model->email, 'VERIFY_EMAIL_ADDRESS', $field);
        }else if($column == 'phone_number_verify'){
            $model->sms_code = ''.mt_rand(100000, 999999).'';
            $model->phone_number_verify = '0';
            if(!$model->save()){
                echo '<pre>'; print_r($model);echo '</pre>';exit;
            }
            $field = array();
            $field['{{code}}'] = $model->sms_code;
            Yii::$app->BackFunctions->sendotp($model->phone_number, 'VERIFY_PHONE_NUMBER', $field);
        }
        
        
    }
    
}
