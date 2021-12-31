<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class UserLoginForm extends Model {

    public $email;
    public $password;
    public $type;
    public $module_id;
    public $language_id;
    public $rememberMe = true;
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            // username and password are both required
            [['email', 'password', 'type', 'module_id','language_id'], 'required'],
            [['email'], 'email'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            $validateuser = UserResponse::validateuser($user);
            if (!$user) {
                $this->addError($attribute, Yii::t('app', 'User is not available.'));
            } else if ($validateuser && isset($validateuser['result']) && isset($validateuser['message']) && $validateuser['message']) {
                if($validateuser['result'] != 1){
                    $this->addError($attribute, $validateuser['message']);
                }
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            //$ipInfo = Yii::$app->MyFunctions->ip_info();
            $user_id = "";
            $authkey = "";
//            if(isset($this->_user) && $this->_user){
//                $useriddata = explode('+',$this->_user->user_id);
//                if($useriddata){
//                    $user_id = $useriddata[0];
//                    $authkey = $useriddata[1];
//                    Yii::$app->MyFunctions->addAccessLog($user_id, 'User', 'login', json_encode($ipInfo));
//                }
//            }
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }else{
            //print_r($this->getErrors());exit;
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser() {
        if ($this->_user === null) {
            $restapiData = array();
            $restapiData['type'] = $this->type;
            $restapiData['unique_id'] = $this->email;
            $restapiData['password'] = $this->password;
            $restapiData['module_id'] = $this->module_id;
            $restapiData['devicetype'] = 'Webapp';
            $restapiData['devicetoken'] = 'Web';
            $restapiData['app_version'] = '1';
            $restapiData['device_model'] = $_SERVER['HTTP_USER_AGENT'];
            $this->_user = UserResponse::findByUsername($restapiData);
        }
        return $this->_user;
    }

}
