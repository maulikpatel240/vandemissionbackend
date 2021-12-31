<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $phone_number
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $avatar
 * @property string $aadhaar_card_number
 * @property string $aadhaar_card_photo
 * @property string $gender
 * @property string|null $birthday
 * @property string $email_code
 * @property string $sms_code
 * @property string $verified
 * @property string $address
 * @property string $latitude
 * @property string $longitude
 * @property int $ip_address
 * @property int $birth_privacy
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Users extends \yii\db\ActiveRecord {

    public $phone_number_verify_otp = "";
    public $email_verify_code = "";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['phone_number_verify_otp', 'email_verify_code'], 'required', 'skipOnEmpty' => true, 'on' => 'popupmodal'],
            [['username', 'phone_number', 'first_name', 'last_name', 'middle_name', 'gender', 'birthday'], 'required'],
            [['gender', 'verified', 'status'], 'string'],
            [['birthday', 'status_at', 'created_at', 'updated_at', 'auth_key'], 'safe'],
            [['ip_address', 'birth_privacy'], 'integer'],
            [['email_verify', 'phone_number_verify'], 'boolean'],
            [['username', 'latitude', 'longitude'], 'string', 'max' => 50],
            [['email', 'address'], 'string', 'max' => 200],
            [['phone_number'], 'string', 'max' => 16],
            [['first_name', 'last_name', 'middle_name', 'avatar', 'aadhaar_card_photo'], 'string', 'max' => 100],
            [['aadhaar_card_number'], 'string', 'max' => 20],
            [['email_code', 'sms_code'], 'string', 'max' => 8],
            [['phone_number_verify_otp', 'email_verify_code'], 'string', 'min' => 4, 'max' => 6],
            [['phone_number_verify_otp', 'email_verify_code'], 'default', 'value' => 0],
            //[['phone_number'], 'match', 'pattern' => '/^d{3}-d{3}-d{4}/'],
            [['phone_number'], 'unique'],
            [['email'], 'unique'],
            [['username'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'username' => 'Username',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'avatar' => 'Avatar',
            'aadhaar_card_number' => 'Aadhaar Card Number',
            'aadhaar_card_photo' => 'Aadhaar Card Photo',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'email_code' => 'Email Code',
            'sms_code' => 'Sms Code',
            'verified' => 'Verified',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'ip_address' => 'Ip Address',
            'birth_privacy' => 'Birth Privacy',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'phone_number_verify_otp' => 'Phone number verify otp'
        ];
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole() {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    public function upload() {
        if ($this->avatar) {
            //$this->map->baseName = $this->id.'_state';
            $filename = 'user_' . $this->id . '.' . $this->avatar->extension;
            $this->avatar->saveAs(Yii::getAlias('@webroot') . '/uploads/user/avatar/' . $filename, false);
            $this->avatar = $filename;
            $this->save();
            return true;
        } else {
            return false;
        }
    }

    public function deleteImage($oldimg = "") {
        if ($oldimg) {
            $image = Yii::getAlias('@webroot') . '/uploads/user/avatar/' . $oldimg;
            if (file_exists($image) && unlink($image)) {
                return true;
            }
        } else {
            $image = Yii::getAlias('@webroot') . '/uploads/user/avatar/' . $this->avatar;
            if (file_exists($image) && unlink($image)) {
                $this->avatar = '';
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function uploadAadhaar() {
        if ($this->aadhaar_card_photo) {
            //$this->map->baseName = $this->id.'_state';
            $filename = 'user_aadhaar_' . $this->id . '.' . $this->aadhaar_card_photo->extension;
            $this->aadhaar_card_photo->saveAs(Yii::getAlias('@webroot') . '/uploads/user/aadhaar/' . $filename, false);
            $this->aadhaar_card_photo = $filename;
            $this->save();
            return true;
        } else {
            return false;
        }
    }

    public function deleteAadhaar($oldimg = "") {
        if ($oldimg) {
            $image = Yii::getAlias('@webroot') . '/uploads/user/aadhaar/' . $oldimg;
            if (file_exists($image) && unlink($image)) {
                return true;
            }
        } else {
            $image = Yii::getAlias('@webroot') . '/uploads/user/aadhaar/' . $this->aadhaar_card_photo;
            if (file_exists($image) && unlink($image)) {
                $this->aadhaar_card_photo = '';
                $this->save();
                return true;
            }
        }
        return false;
    }

}
