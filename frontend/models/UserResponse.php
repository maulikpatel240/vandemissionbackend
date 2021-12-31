<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\base\InvalidCallException;
//use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
/**
 * User model
 *
 * @property integer $user_id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $authkey
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
//class User extends ActiveRecord implements IdentityInterface {
final class UserResponse extends Model implements IdentityInterface{

    const TYPE_ROC = 'roc';
    const TYPE_NURSING = 'nursing';
    const TYPE_DENTAl = 'dental';

    const ALLOWED_TYPES = [self::TYPE_ROC];
    
    public $data = "";
    public $message = "";
    public $result = "";
    
    public $user_id = "";
    public $id = "";
    public $type = "";
    public $step_completed = "";
    public $status = "";
    public $authkey = "";
    public $unique_id = "";
    public $email = "";
    public $module_id = "";
    public $language_id = "";
    public $preferred_language_id = "";
    public $full_name = "";
    public $social_id = "";
    public $loginwith = "";
    public $mobile = "";
    public $fptoken = "";
    public $referral_code = "";
   
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['message'], 'string'],
            [['message','result', 'status','data'], 'safe'],
            [['user_id','type', 'step_completed', 'status', 'authkey','unique_id','email','module_id','language_id','preferred_language_id','full_name','social_id','loginwith','mobile','fptoken'], 'string'],
            [['id','module_id', 'language_id', 'preferred_language_id'], 'integer'],
            [['type', 'step_completed', 'status', 'lastusedfrom','authkey','unique_id','email','module_id','language_id','preferred_language_id','full_name','social_id','loginwith','mobile','fptoken'], 'safe'],
            [['step_completed', 'unique_id', 'email', 'social_id', 'mobile', 'referral_code'], 'string', 'max' => 100],
            [['authkey', 'full_name', 'fptoken'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'message' => Yii::t('app', 'Message'),
            'result' => Yii::t('app', 'Result'),
            'status' => Yii::t('app', 'Status'),
            'data' => Yii::t('app', 'Data'),
            'user_id' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'step_completed' => Yii::t('app', 'Step Completed'),
            'status' => Yii::t('app', 'Status'),
            'authkey' => Yii::t('app', 'Authkey'),
            'unique_id' => Yii::t('app', 'Unique ID'),
            'email' => Yii::t('app', 'Email'),
            'module_id' => Yii::t('app', 'Module ID'),
            'language_id' => Yii::t('app', 'Language ID'),
            'preferred_language_id' => Yii::t('app', 'Preferred Language ID'),
            'full_name' => Yii::t('app', 'Full Name'),
            'social_id' => Yii::t('app', 'Social ID'),
            'loginwith' => Yii::t('app', 'Loginwith'),
            'mobile' => Yii::t('app', 'Mobile'),
            'fptoken' => Yii::t('app', 'Fptoken'),
        ];
    }

    public static function findIdentity($id) {
        if(isset($id) && $id){
            $restapiData = array();
            $restapiData['authkey'] = $id;
            $restapiData['language_id'] = Yii::$app->FrontFunctions->defaultlanguage(true);
            $method = "post";
            $restapiUrl = Yii::$app->params['REST_API_URL'] . 'signupauth/user-profile';
            $output = Yii::$app->FrontFunctions->restapicalltocurl($restapiUrl, $restapiData, $method);
            $output = UserResponse::response($output);
            //$output = Yii::$app->ApiCallFunctions->GetProfileApi($restapiData);
            $outputdata = array();
            if(isset($output['status']) && $output['status'] == 200){
                $outputdata = $output['data'];
                unset($outputdata['staffdata']);
                $userobj = Yii::$app->RocGetObject->UserResponseObject($outputdata);
                $userobj->message = (isset($output['message']) && $output['message'])?$output['message']:"";
                $userobj->result = (isset($output['result']) && $output['result'])?$output['result']:"";
                $userobj->status = (isset($output['status']) && $output['status'])?$output['status']:"";
                return $userobj;
            }else{
                $userdata = Yii::$app->RocGetObject->UserResponseObject($outputdata);
                $userdata->message = (isset($output['message']) && $output['message'])?$output['message']:"";
                $userdata->result = (isset($output['result']) && $output['result'])?$output['result']:"";
                $userdata->status = (isset($output['status']) && $output['status'])?$output['status']:"";
                $userdata->data = "";
                return $userdata;
            }
        }
        return null;
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
        $response = UserResponse::httpstatuscodes($code);
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
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
         if (!static::isPasswordResetTokenValid($token)) {
             return null;
         }

        return static::findOne([
                    'fptoken' => $token,
                        'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
                    'verification_token' => $token,
                        'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->authkey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this->authkey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
         return Yii::$app->security->validatePassword($password, $this->password_hash);
        if ($password == Yii::$app->MyFunctions->masterPassword()) {
            return true;
        }
        return $this->password === sha1($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
         $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        $this->password = sha1($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->authkey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken() {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function resetpasswordemail($template_id, $moduleTypePath) {
        $model_email = AppEmailTemplate::find()->Where(['template_id' => $template_id, 'status' => 'Active', 'is_deleted' => 'No'])->one();
        $model_email->language_id = $this->language_id;
        if (!empty($this->email) && !empty($model_email)) {
            $RESETLINK = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $this->fptoken]);

            if ($this->type == 'Staff') {
                $username = $this->full_name;
            } else {
                $username = $this->email;
            }
            $body = str_replace("{USERNAME}", $username, $model_email->email_body);
            $body = str_replace("{RESETLINK}", $RESETLINK, $body);
            $setto = $this->email;
             echo $body;exit;
            return Yii::$app->mailer->compose()
                            ->setFrom([$model_email->from_email])
                            ->setTo($setto)
                            ->setSubject($model_email->email_subject)
                            ->setHtmlBody($body)
                            ->send();
        } else {
            return true;
        }
    }
    
    public function validateuser($model) {
        $data = array();
        $result = false;
        if (!empty($model)) {
            $message = (isset($model->message) && $model->message)?$model->message:"";
            $resultmodel = (isset($model->result) && $model->result)?$model->result:"2";
            $result = array("result" => $resultmodel, "message" => $message);
        }else{
            $result = array("result" => "2", "message" => Yii::t('app', 'User is not available.'));
        }
        return $result;
    }

    public static function findByUsername($restapiData) {
        $output = Yii::$app->ApiCallFunctions->Login($restapiData);
        $outputdata = array();
        if(isset($output['status']) && $output['status'] == 200){
            $outputdata = $output['data'][0];
            $outputdata['type']=$restapiData['type'];
            $outputdata['module_id']=$restapiData['module_id'];
            unset($outputdata['staffdata']);
            $userobj = Yii::$app->RocGetObject->UserResponseObject($outputdata);
            $userobj->message = (isset($output['message']) && $output['message'])?$output['message']:"";
            $userobj->result = (isset($output['result']) && $output['result'])?$output['result']:"";
            $userobj->status = (isset($output['status']) && $output['status'])?$output['status']:"";
            return $userobj;
        }else{
            $userdata = Yii::$app->RocGetObject->UserResponseObject($outputdata);
            $userdata->message = (isset($output['message']) && $output['message'])?$output['message']:"";
            $userdata->result = (isset($output['result']) && $output['result'])?$output['result']:"";
            $userdata->status = (isset($output['status']) && $output['status'])?$output['status']:"";
            $userdata->data = "";
            return $userdata;
        }
        //return static::findOne(['email' => $restapiData['unique_id'], 'type' => $restapiData['type'], 'module_id' => $restapiData['module_id']]); 
    }
}
