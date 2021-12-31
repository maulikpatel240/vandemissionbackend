<?php

namespace backend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
/**
 * This is the model class for table "admin".
 *
 * @property int $admin_id
 * @property int $role_id
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string $secret_key
 * @property string $access_key
 * @property string $refresh_key
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $username
 * @property string $email_id
 * @property string $mobile_number
 * @property string $password
 * @property string $password_reset_token
 * @property string $avatar
 * @property string $profile_video
 * @property string $email_verify
 * @property string $mobile_number_verify
 * @property int|null $email_otp
 * @property int|null $mobile_number_otp
 * @property string $emergency_mobile_number
 * @property string $gender
 * @property string|null $birthdate
 * @property int|null $religion_id
 * @property string $about_me
 * @property string $description
 * @property int|null $country_id
 * @property int|null $state_id
 * @property string $city
 * @property string $village
 * @property string $address
 * @property string $pincode
 * @property string $location
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string $society
 * @property string|null $ip_address
 *
 * @property Role $role
 * @property Countries $country
 * @property States $state
 * @property Religion $religion
 * @property AdminBankDetails[] $adminBankDetails
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 'Inactive';
    const STATUS_ACTIVE = 'Active';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        //return 'admin';
        return '{{%admin}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'secret_key', 'access_key', 'refresh_key', 'first_name', 'last_name', 'middle_name', 'username', 'email_id', 'mobile_number', 'password', 'password_reset_token', 'avatar', 'profile_video', 'emergency_mobile_number', 'gender', 'about_me', 'description', 'city', 'village', 'address', 'pincode', 'location', 'society'], 'required'],
            [['role_id', 'email_otp', 'mobile_number_otp', 'religion_id', 'country_id', 'state_id'], 'integer'],
            [['status', 'secret_key', 'access_key', 'refresh_key', 'email_verify', 'mobile_number_verify', 'gender', 'about_me', 'description', 'address', 'location'], 'string'],
            [['status_at', 'created_at', 'updated_at', 'birthdate'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['first_name', 'last_name', 'middle_name', 'email_id', 'password_reset_token', 'city', 'village'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 30],
            [['mobile_number', 'emergency_mobile_number'], 'string', 'max' => 20],
            [['password'], 'string', 'max' => 12],
            [['avatar', 'profile_video'], 'string', 'max' => 50],
            [['pincode'], 'string', 'max' => 10],
            [['society'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 32],
            [['email_id'], 'unique'],
            [['mobile_number'], 'unique'],
            [['username'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['state_id'], 'exist', 'skipOnError' => true, 'targetClass' => States::className(), 'targetAttribute' => ['state_id' => 'id']],
            [['religion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Religion::className(), 'targetAttribute' => ['religion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => 'Admin ID',
            'role_id' => 'Role ID',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'secret_key' => 'Secret Key',
            'access_key' => 'Access Key',
            'refresh_key' => 'Refresh Key',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'username' => 'Username',
            'email_id' => 'Email ID',
            'mobile_number' => 'Mobile Number',
            'password' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'avatar' => 'Avatar',
            'profile_video' => 'Profile Video',
            'email_verify' => 'Email Verify',
            'mobile_number_verify' => 'Mobile Number Verify',
            'email_otp' => 'Email Otp',
            'mobile_number_otp' => 'Mobile Number Otp',
            'emergency_mobile_number' => 'Emergency Mobile Number',
            'gender' => 'Gender',
            'birthdate' => 'Birthdate',
            'religion_id' => 'Religion ID',
            'about_me' => 'About Me',
            'description' => 'Description',
            'country_id' => 'Country ID',
            'state_id' => 'State ID',
            'city' => 'City',
            'village' => 'Village',
            'address' => 'Address',
            'pincode' => 'Pincode',
            'location' => 'Location',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'society' => 'Society',
            'ip_address' => 'Ip Address',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['admin_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
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
    public static function isPasswordResetTokenValid($token)
    {
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
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

    /**
     * Gets query for [[State]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(States::className(), ['id' => 'state_id']);
    }

    /**
     * Gets query for [[Religion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReligion()
    {
        return $this->hasOne(Religion::className(), ['id' => 'religion_id']);
    }

    /**
     * Gets query for [[AdminBankDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdminBankDetails()
    {
        return $this->hasMany(AdminBankDetails::className(), ['admin_id' => 'admin_id']);
    }
    
}
