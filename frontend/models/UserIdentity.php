<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
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
class UserIdentity extends Model{

    public $user_id = "";
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
   
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id','type', 'step_completed', 'status', 'authkey','unique_id','email','module_id','language_id','preferred_language_id','full_name','social_id','loginwith','mobile','fptoken'], 'string'],
            [['module_id', 'language_id', 'preferred_language_id'], 'integer'],
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
    
}
