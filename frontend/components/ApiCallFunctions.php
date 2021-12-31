<?php

namespace frontend\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;

class ApiCallFunctions extends Component {
    public $_user="";
    public $_lang="en";
    public $_langID=1;
    
    public function init()
    {
        parent::init();
        $this->_user = Yii::$app->user->identity;
        $this->_lang = Yii::$app->FrontFunctions->defaultlanguage();
        $this->_langID = Yii::$app->FrontFunctions->defaultlanguage(true);
    }
    function httpstatuscodes($code=""){
        $http_status_codes = array(
                100 => Yii::t('app', "Continue"),
                101 => Yii::t('app', "Switching Protocols"),
                102 => Yii::t('app', "Processing"),
                200 => Yii::t('app', "OK"),
                201 => Yii::t('app', "Created"),
                202 => Yii::t('app', "Accepted"),
                203 => Yii::t('app', "Non-Authoritative Information"),
                204 => Yii::t('app', "No Content"),
                205 => Yii::t('app', "Reset Content"),
                206 => Yii::t('app', "Partial Content"),
                207 => Yii::t('app', "Multi-Status"),
                300 => Yii::t('app', "Multiple Choices"),
                301 => Yii::t('app', "Moved Permanently"),
                302 => Yii::t('app', "Found"),
                303 => Yii::t('app', "See Other"),
                304 => Yii::t('app', "Not Modified"),
                305 => Yii::t('app', "Use Proxy"),
                306 => Yii::t('app', "(Unused)"),
                307 => Yii::t('app', "Temporary Redirect"),
                308 => Yii::t('app', "Permanent Redirect"),
                400 => Yii::t('app', "Bad Request"),
                401 => Yii::t('app', "Unauthorized"),
                402 => Yii::t('app', "Payment Required"),
                403 => Yii::t('app', "Forbidden"),
                404 => Yii::t('app', "Not Found"),
                405 => Yii::t('app', "Method Not Allowed"),
                406 => Yii::t('app', "Not Acceptable"),
                407 => Yii::t('app', "Proxy Authentication Required"),
                408 => Yii::t('app', "Request Timeout"),
                409 => Yii::t('app', "Conflict"),
                410 => Yii::t('app', "Gone"),
                411 => Yii::t('app', "Length Required"),
                412 => Yii::t('app', "Precondition Failed"),
                413 => Yii::t('app', "Request Entity Too Large"),
                414 => Yii::t('app', "Request-URI Too Long"),
                415 => Yii::t('app', "Unsupported Media Type"),
                416 => Yii::t('app', "Requested Range Not Satisfiable"),
                417 => Yii::t('app', "Expectation Failed"),
                418 => Yii::t('app', "I'm a teapot"),
                419 => Yii::t('app', "Authentication Timeout"),
                420 => Yii::t('app', "Enhance Your Calm"),
                422 => Yii::t('app', "Unprocessable Entity"),
                423 => Yii::t('app', "Locked"),
                424 => Yii::t('app', "Failed Dependency"),
                424 => Yii::t('app', "Method Failure"),
                425 => Yii::t('app', "Unordered Collection"),
                426 => Yii::t('app', "Upgrade Required"),
                428 => Yii::t('app', "Precondition Required"),
                429 => Yii::t('app', "Too Many Requests"),
                431 => Yii::t('app', "Request Header Fields Too Large"),
                444 => Yii::t('app', "No Response"),
                449 => Yii::t('app', "Retry With"),
                450 => Yii::t('app', "Blocked by Windows Parental Controls"),
                451 => Yii::t('app', "Unavailable For Legal Reasons"),
                494 => Yii::t('app', "Request Header Too Large"),
                495 => Yii::t('app', "Cert Error"),
                496 => Yii::t('app', "No Cert"),
                497 => Yii::t('app', "HTTP to HTTPS"),
                499 => Yii::t('app', "Client Closed Request"),
                500 => Yii::t('app', "Internal Server Error"),
                501 => Yii::t('app', "Not Implemented"),
                502 => Yii::t('app', "Bad Gateway"),
                503 => Yii::t('app', "Service Unavailable"),
                504 => Yii::t('app', "Gateway Timeout"),
                505 => Yii::t('app', "HTTP Version Not Supported"),
                506 => Yii::t('app', "Variant Also Negotiates"),
                507 => Yii::t('app', "Insufficient Storage"),
                508 => Yii::t('app', "Loop Detected"),
                509 => Yii::t('app', "Bandwidth Limit Exceeded"),
                510 => Yii::t('app', "Not Extended"),
                511 => Yii::t('app', "Network Authentication Required"),
                598 => Yii::t('app', "Network read timeout error"),
                599 => Yii::t('app', "Network connect timeout error")
            );
        $status = "";
        if($code){
            $status = ["status"=>$code,"message"=>$http_status_codes[$code]];
        }
        return $status;
    }
    function response($output = ""){
        $output = json_decode(json_encode($output),true);
        $data = array();
        $slotmodificationdata = array();
        $favourite = array();
        $block = array();
        $message = "";
        $code = 404;
        $result = 0;
        if($output && isset($output['message'])){
            $message = $output['message'];
        }
        if($output && isset($output['result']) && $output['result'] == 1){
            $code = 200;
            $result = $output['result'];
            if($output && isset($output['data'])){
                if($output['data']){
                    $data = $output['data'];
                }
            }
            if($output && isset($output['slotmodificationdata'])){
                if($output['slotmodificationdata']){
                    $slotmodificationdata = $output['slotmodificationdata'];
                }
            }
            if($output && isset($output['favourite'])){
                if($output['favourite']){
                    $favourite = $output['favourite'];
                }
            }
            if($output && isset($output['block'])){
                if($output['block']){
                    $block = $output['block'];
                }
            }
        }
        $response = $this->httpstatuscodes($code);
        $response['result'] = $result;
        if($message){
            $response['message'] = $message;
        }
        if($data){
            $response['data'] = $data;
        }
        if($slotmodificationdata){
            $response['slotmodificationdata'] = $slotmodificationdata;
        }
        if($favourite){
            $response['favourite'] = $favourite;
        }
        if($block){
            $response['block'] = $block;
        }
        return $response;
    }
    /*************
     * 
     * Common Api Function all category
     * 
     */
    public function CheckVersion($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
            $restapiData['devicetype'] = 'Web';
            $restapiData['type'] = $user->type;
            $restapiData['app_version'] = '1';
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/check-version';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function Login($restapiData=array(),$method="post"){
        $restapiData['loginwith'] = 'Normal';
        $restapiData['devicetype'] = "Webapp";
        $restapiData['devicetoken'] = "123456";
        $restapiData['app_version'] = 1;
        $restapiData['device_model'] = "Desktop";
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/login';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ForgotPassword($restapiData=array(),$method="post"){
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/forgot-password';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function AppLanguagesApi($restapiData=array(),$method="get"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/app-languages';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function AppModulesApi($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/app-modules';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function AppCategoryApi($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/app-category';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        //print_r($output);exit;
        return $this->response($output);
    }
    public function AppStaticpageApi($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/app-staticpage';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function getAppFaq($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/getappfaq';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function GetProfileApi($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }       
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-profile';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function ChangePassword($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }    
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/change-password';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function UserLanguages($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-languages';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function UserTypeOfEstablishmentApi($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-type-of-establishment';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function AppProvinceApi($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/app-province';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function beforeauthAppProvinceApi($restapiData=array(),$method="post"){
        
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/app-province';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function OwnerGetHomeDetails($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
            $restapiData['devicetype'] = 'Webapp';
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownergethomedetails';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function OwnerGetPreviousSlotHistory($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownergetpreviousslothistory';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function ownerGetSlotsList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownergetslotslist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function getSingleSlotsDetails($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/getsingleslotsdetails';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ownerGetAppliedSlotsList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownergetappliedslotslist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function ownerCancelSlot($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownercancelslot';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ownerConfirmSlot($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownerconfirmslot';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ownerRemoveFromAppied($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownerremovefromappied';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ownerSlotCreate($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/add-slot';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ownerSlotUpdate($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/update-slot';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function getNotificationList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/getnotificationlist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function updateProfile($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/update-profile';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function updateProfileModule($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/update-profile-module';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function userDepartmentsSpecialties($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-departments-specialties';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function userCertificateList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-certificate-list';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function ownerAddStripeCard($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/owneraddstripecard';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ownerGetAllStripeCardList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/ownergetallstripecardlist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ownerDeleteStripeCard($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/ownerdeletestripecard';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function deactiveAccount($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/deactiveaccount';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function adddeleteowner($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/add-delete-owner';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    
    //Staff api
    public function StaffGetHomeDetails($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
            $restapiData['devicetype'] = 'Webapp';
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/staffgethomedetails';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function StaffGetAvailableSlotsList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/staffgetavailableslotslist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    
    public function StaffGetSlotsList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/staffgetslotslist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function StaffApplySlot($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/staffapplyslot';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function StaffCancelAppliedSlot($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/staffcancelappiedslot';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    
    public function UpdateDocuments($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/update-documents';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function StaffGetPreviousSlotHistory($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/staffgetpreviousslothistory';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function StaffGetAvailability($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/getavailability';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function StaffAddAvailability($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/addavailability';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function StaffUpdateRemoveAvailability($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/updateremoveschedule';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function EmailCheck($restapiData=array(),$method="post"){
        //$restapiData['User[module_id]'] = 5;
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/email-check';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function SignupStepOneStaff($restapiData=array(),$method="post"){
        //$restapiData['User[module_id]'] = 5;
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/signup-step1';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function SignupStepTwoStaff($restapiData=array(),$method="post"){ 
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/signup-step2';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function SignupStepThreeStaff($restapiData=array(),$method="post"){ 
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/signup-step3';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function SignupStepFourStaff($restapiData=array(),$method="post"){ 
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/signup-step4';
//        echo "<pre>";print_r($restapiData);
//        print_r($restapiUrl);exit;
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function SignupStepFiveStaff($restapiData=array(),$method="post"){ 
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/signup-step5';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function SignupStepSixStaff($restapiData=array(),$method="post"){ 
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/signup-step6';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function SignupStepSevenStaff($restapiData=array(),$method="post"){ 
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/signup-step7';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function UserQuestions($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-questions';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function SignupStepOneOwner($restapiData=array(),$method="post"){
        //$restapiData['User[module_id]'] = 5;
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'beforeauth/signup-step1';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function SignupStepTwoOwner($restapiData=array(),$method="post"){ 
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/signup-step2';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }

    public function Getrecentchatlist($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/getrecentchatlist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }

    public function Getadminchatlist($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/getadminchatlist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);

        return $this->response($output);
    }

    public function Adminchatsendmessage($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/adminchatsendmessage';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);

        return $this->response($output);
    }

    public function Getstaffownerlist($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/getstaffownerlist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);

        return $this->response($output);
    }

    public function Getstaffownerchatlist($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/getstaffownerchatlist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);

        return $this->response($output);
    }

    public function Generalchatsendmessage($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/generalchatsendmessage';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }

    public function Generalchatsendnotification($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }

        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/generalchatsendnotification';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $output;
    }

    public function SendChathelp($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }

        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'chatauth/chathelp';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    
    public function OwnerGetPaymentSlotList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }

        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownergetpaymentslotlist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function OwnerGetStaffList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }

        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/ownergetstafflist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function OwnerGetFavouriteBlockStaffList($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }

        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/ownergetfavouriteblockstafflist';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function OwnerAddFavouriteBlockStaff($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/owneraddfavouriteblockstaff';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function OwnerDeleteFavouriteBlockStaff($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/ownerdeletefavouriteblockstaff';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    public function OwnerGiveStaffSlotReview($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/ownergivestaffslotreview';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        
        return $this->response($output);
    }
    /*****
     * 
     * Nursing
     * 
     */
    
    
    
    /*****
     * 
     * Dental
     * 
     */
    public function userSoftwares($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-software';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function UserSkill($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-skill';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function UserWorkExperience($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-work-experience';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function userExpertise($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-expertise';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function userParking($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-parking';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function userTypeofradiographs($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-typeofradiographs';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function userTypeofultrasonics($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-typeofultrasonics';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function userChartingSystem($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
            $restapiData['language_id'] = $this->_langID;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-charting-system';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function submitreview($restapiData=array(),$method="post"){ 
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/submitreview';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function StaffGetAvailableSlotFilter($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/staffgetavailableslotfilter';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function CancelSlotRequest($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/cancelslotrequest';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function ChangeSlotCancelRequestStatus($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/changeslotcancelrequeststatus';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function SlotModificationRequestSend($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/slotmodificationrequestsend';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function GetModifiedSlotDetails($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/getmodifiedslotdetails';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function AcceptRejectModifiedSlotRequest($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/acceptrejectmodifiedslotrequest';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function CancelModifiedSlotRequest($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/cancelmodifiedslotrequest';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function GetModifiedSlotHistory($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/getmodifiedslothistory';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function Increasehourlyrates($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'slotauth/increasehourlyrates';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    public function ApplyPromoCode($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/applypromocode';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
    
    public function PromoCodeHistory($restapiData=array(),$method="post"){
        $user = $this->_user;
        if($user){
            $restapiData['authkey'] = $user->authkey;
        }
        $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/promocodehistory';
        $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
        return $this->response($output);
    }
}