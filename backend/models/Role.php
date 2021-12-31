<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Role".
 *
 * @property int $id
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string $name
 * @property string $panel
 */
class Role extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'Role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['status', 'panel'], 'string'],
            [['status_at', 'created_at', 'updated_at'], 'safe'],
            [['name', 'status', 'panel'], 'required'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'name' => 'Name',
            'panel' => 'Panel',
        ];
    }

    /**
     * Gets query for [[Admins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins() {
        return $this->hasMany(Admin::className(), ['role_id' => 'id']);
    }

    /**
     * Gets query for [[RoleAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleAccesses() {
        return $this->hasMany(RoleAccess::className(), ['role_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(Users::className(), ['role_id' => 'id']);
    }

    public static function get_status() {
        $cat = Role::find()->all();
        $cat = ArrayHelper::map($cat, 'id', 'status');
        return $cat;
    }

    public function getSortAbcArray() {
        if (empty($this->sort_abs)) {
            return [];
        }
        return explode(',', $this->sort_abc);
    }

    public function setSortAbcArray($value) {
        $this->sort_abs = implode(',', array_filter($value));
    }

}
