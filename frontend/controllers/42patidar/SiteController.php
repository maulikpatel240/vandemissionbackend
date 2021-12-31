<?php
namespace frontend\controllers\roc;


use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\ChangePasswordForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $_module_id = 2;
    public $_baseUrl = "/";
    public $_basePath = "/";
    public $_lang = "en";
    public $_langID = 1;
    public $_module = array();
    public $_moduleUrl = "/";
    public $_modulePath = "/";
    public $_staffUrl = "/";
    public $_staffPath = "/";
    public $_beforestaffUrl = "/";
    public $_beforestaffPath = "/";
    public $_ownerUrl = "/";
    public $_ownerPath = "/";
    public $_beforeownerUrl = "/";
    public $_beforeownerPath = "/";
    public $_user = array();
    public $_profiledataMsg = "";
    public $_profiledata = array();
    /**
     * {@inheritdoc}
     */
    
    public function init() {
        parent::init();
        $this->_baseUrl = Url::base(true).'/';
        $this->_basePath = Url::base().'/';
        $this->_lang = Yii::$app->FrontFunctions->defaultlanguage();
        $this->_langID = Yii::$app->FrontFunctions->defaultlanguage(true);
        $this->_module = Yii::$app->FrontFunctions->AppModules($this->_module_id);
        $this->_moduleUrl = $this->_baseUrl.$this->_module['unique_name'].'/';
        $this->_modulePath = $this->_basePath.$this->_module['unique_name'].'/';
        $this->_staffUrl = $this->_baseUrl.$this->_module['unique_name'].'/staff/';
        $this->_staffPath = '/'.$this->_module['unique_name'].'/staff/';
        $this->_beforestaffUrl = $this->_baseUrl.$this->_module['unique_name'].'/before-staff/';
        $this->_beforestaffPath = '/'.$this->_module['unique_name'].'/before-staff/';
        $this->_ownerUrl = $this->_baseUrl.$this->_module['unique_name'].'/owner/';
        $this->_ownerPath = '/'.$this->_module['unique_name'].'/owner/';
        $this->_beforeownerUrl = $this->_baseUrl.$this->_module['unique_name'].'/before-owner/';
        $this->_beforeownerPath = '/'.$this->_module['unique_name'].'/before-owner/';
        
        $message = "";
        if (Yii::$app->user->identity && isset(Yii::$app->user->identity->message)) {
            $message = Yii::$app->user->identity->message;
        }
        if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity)) {
            $this->_user = Yii::$app->user->identity;
            $profileoutput = Yii::$app->ApiCallFunctions->GetProfileApi();
            $this->_profiledataMsg = $profileoutput['message'];
            if ($profileoutput['status'] == 200) {
                $this->_profiledata = $profileoutput['data'];
            }
        }
        if (empty($this->_user)) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect($this->_basePath);
        }
    }
    public function beforeAction($action) {
//        if ($this->action->id == 'slotapply') {
//            $this->enableCsrfValidation = false;
//        }
//        if ($this->action->id != 'error' ) {
//            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
//        }
        return parent::beforeAction($action);
    }

    
//    public function actionIndex() {
//        $this->layout = 'beforelogin';
//        $renderdata = array(
//            "_lang" => $this->_lang,
//            "_langID" => $this->_langID,
//            "_baseUrl" => $this->_baseUrl,
//            "_basePath" => $this->_basePath,
//            "_module" => $this->_module,
//            "_moduleUrl" => $this->_moduleUrl,
//            "_modulePath" => $this->_modulePath,
//            "_staffUrl" => $this->_staffUrl,
//            "_staffPath" => $this->_staffPath,
//            "_beforestaffUrl" => $this->_beforestaffUrl,
//            "_beforestaffPath" => $this->_beforestaffPath,
//            "_ownerUrl" => $this->_ownerUrl,
//            "_ownerPath" => $this->_ownerPath,
//            "_beforeownerUrl" => $this->_beforeownerUrl,
//            "_beforeownerPath" => $this->_beforeownerPath,
//        );
//        return $this->render('index',$renderdata);
//    }
    
    public function actionSessionpopup() {
        $user = $this->_user;
        if ($user) {
            $session = Yii::$app->session;
            $miniute = Yii::$app->params['popuptimeminiute'];
            $session['popup'] = [
                'popuptime' => time(),
                'lifetime' => time() + ($miniute * 60),
            ];
            echo json_encode($session['popup']);
        }
    }
    public function actionSwitchchange() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $id = "";
        if (isset($_REQUEST['id']) && $_REQUEST['id']) {
            $id = $_REQUEST['id'];
        }
        $status = "";
        if (isset($_REQUEST['status']) && $_REQUEST['status']) {
            $status = $_REQUEST['status'];
        }
        $checkvalue = "";
        if (isset($_REQUEST['checkvalue']) && $_REQUEST['checkvalue']) {
            $checkvalue = $_REQUEST['checkvalue'];
        }
        $restapiData = array();
        if($id == "review_alert"){
            $restapiData['type'] = "User";
            if ($checkvalue == 'Yes') {
                $restapiData['User[review_alert]'] = 'No';
            }else{
                $restapiData['User[review_alert]'] = 'Yes';
            }
        }elseif($id == "increase_hourlyrates_alert"){
            $restapiData['type'] = "User";
            if ($checkvalue == 'Yes') {
                $restapiData['User[increase_hourlyrates_alert]'] = 'No';
            }else{
                $restapiData['User[increase_hourlyrates_alert]'] = 'Yes';
            }
        }elseif($id == "newslot_onoff"){
            $restapiData['type'] = "Staff";
            if ($checkvalue == 'On') {
                $restapiData['StaffUser[newslot_onoff]'] = 'Off';
            }else{
                $restapiData['StaffUser[newslot_onoff]'] = 'On';
            }
        }
        $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
        $output['message'] = $output['message'];
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $output;
        }
    }
    
    public function actionChangePassword()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect($this->_basePath);
        }
        $this->layout = 'afterlogin';
        $model = new ChangePasswordForm();
        return $this->render('changepassword',[
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_staffUrl" => $this->_staffUrl,
            "_staffPath" => $this->_staffPath,
            "_beforestaffUrl" => $this->_beforestaffUrl,
            "_beforestaffPath" => $this->_beforestaffPath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $this->_user,
            'model'=>$model
        ]);

    }
    
    public function actionAjaxChangePassword() {
        if (Yii::$app->user->isGuest) {
            return false;
        } 
        $user = $this->_user;
        $model = new ChangePasswordForm();
        if($model->load(Yii::$app->request->post())){
            $postdata = Yii::$app->request->post();
            
            $restapiData = array();
            $restapiData['currentpassword'] = $model->oldpass;
            $restapiData['password'] = $model->newpass;
            $output = Yii::$app->ApiCallFunctions->ChangePassword($restapiData);
            $message ="";
            if(isset($output['message']) && $output['message']){
                $message = $output['message'];
            }
             if($output['status'] == 200){
                Yii::$app->session->setFlash('success', $message);
            }else{
                Yii::$app->session->setFlash('error', $message);
            }
            //return $this->redirect([$this->_moduleTypePath.'promocode']);
            $output['message'] = $message;
        } else {
            $output['message'] = Yii::t('app', 'something went wrong');
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $output;
        }
    }
    

}
