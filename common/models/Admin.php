<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property int $admin_id Admin Auto Incrament ID
 * @property string|null $name Display Name in Admin Panel
 * @property string|null $email Email for Login
 * @property string|null $password Password For Login
 * @property string|null $image_url
 * @property string $role
 * @property string|null $description
 * @property string|null $authkey Unique Authantication Key
 * @property int|null $verification_code Verification Code For login
 * @property string $verification_status Verification Status For login
 * @property string|null $fptoken Forgot Password Token
 * @property string $status Status
 * @property string $is_deleted Dleted Status
 * @property int $timezone_id TimeZone of Admin
 * @property string|null $created_at Admin Created Date
 * @property string|null $updated_at Admin Information Last Nodified Date
 * @property int|null $utctimestamp UTC time stamp in mili seconds
 * @property string|null $access_data Last Access Log
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
	/**
     * {@inheritdoc}
     */

    public $confirm_password;

    public static function tableName()
    {
        return 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role', 'description', 'verification_status', 'is_deleted', 'access_data', 'status'], 'string'],
            [['verification_code', 'utctimestamp', 'timezone_id'], 'integer'],
            [['created_at', 'updated_at','type','side_menu'], 'safe'],
			[['type'],'default','value'=>'Admin'],
            [['name', 'email', 'password', 'image_url', 'authkey', 'fptoken'], 'string', 'max' => 255],

            [['name','timezone_id'], 'required', 'on'=>'change-profile'],

            // create and update admin
            [['name','email','verification_status','timezone_id','role','password','confirm_password'], 'required', 'on'=>['create-admin']],
            ['password', 'string', 'min' => 6,'on'=>['create-admin','update-admin']],
            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match",'on'=>['create-admin']],

            // update admin user
            [['name','email','verification_status','timezone_id','role'], 'required', 'on'=>['update-admin']],
            ['confirm_password', 'required', 'when' => function($model) {
                return $model->password !== '';
                }, 
                'whenClient' => "function (attribute, value) {
                    return $('input[type=\"password\"][name=\"Admin[confirm_password]\"]).val() == '';
                }", 'on'=>['update-admin']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => Yii::t('app', 'Admin ID'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'image_url' => Yii::t('app', 'Image Url'),
            'role' => Yii::t('app', 'Role'),
            'description' => Yii::t('app', 'Description'),
            'authkey' => Yii::t('app', 'Authkey'),
            'verification_code' => Yii::t('app', 'Verification Code'),
            'verification_status' => Yii::t('app', 'Verification Status'),
            'fptoken' => Yii::t('app', 'Fptoken'),
            'status' => Yii::t('app', 'Status'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'timezone_id' => Yii::t('app', 'Timezone ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'utctimestamp' => Yii::t('app', 'Utctimestamp'),
            'side_menu' => Yii::t('app', 'Side Menu'),
            'access_data' => Yii::t('app', 'Access Data'),
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface|null the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->authkey;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
//        echo $username; die('22');
        return static::findOne(['email' => $username]);
    }

    public function validatePassword($password)
    {
        return $this->password === sha1($password);
    }

    public function getTimezone()
    {
        return $this->hasOne(AppTimezone::className(), ['timezone_id' => 'timezone_id']);
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->is_deleted = 'Yes';
            $this->save();
            return false;
        }

        return false;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if($this->isNewRecord) {
                $this->created_at = Yii::$app->MyFunctions->currentDatetime();
                //$this->side_menu = Yii::$app->MyFunctions->currentDatetime();
            }

            $this->updated_at = Yii::$app->MyFunctions->currentDatetime();
            $this->utctimestamp = Yii::$app->MyFunctions->currentUTCDatetime();

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //Add activity logs
        Yii::$app->MyFunctions->addAppLog($this->admin_id, Yii::$app->user->id, self::tableName(), ($insert ? 'add' : 'update'), json_encode($this->getAttributes()));
    }

    public function AdminRole(){
        return [ 'Owner' => 'Owner', 'Developer' => 'Developer', 'Staff' => 'Staff', 'Audit' => 'Audit', ];
    }
}
