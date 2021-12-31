<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "role_access".
 *
 * @property int $id
 * @property int $role_id
 * @property int $permission_id
 * @property string $access
 * @property string|null $created_at
 *
 * @property Role $role
 * @property Permission $permission
 */
class RoleAccess extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'role_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['role_id', 'permission_id'], 'required'],
            [['role_id', 'permission_id'], 'integer'],
            [['access'], 'string'],
            [['created_at'], 'safe'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => Permission::className(), 'targetAttribute' => ['permission_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'role_id' => Yii::t('app', 'Role ID'),
            'permission_id' => Yii::t('app', 'Permission ID'),
            'access' => Yii::t('app', 'Access'),
            'created_at' => Yii::t('app', 'Created At'),
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

    /**
     * Gets query for [[Permission]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermission() {
        return $this->hasOne(Permission::className(), ['id' => 'permission_id']);
    }

}
