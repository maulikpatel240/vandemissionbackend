<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "permission".
 *
 * @property int $id
 * @property string $type
 * @property int|null $module_id
 * @property string $title
 * @property string $name
 * @property string $controller
 * @property string $action
 *
 * @property Modules $module
 * @property RoleAccess[] $roleAccesses
 */
class Permission extends \yii\db\ActiveRecord
{
    public $schedule = "";
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type','module_id', 'title', 'name'], 'required'],
            [['type'], 'string'],
            [['module_id'], 'integer'],
            [['title'], 'string', 'max' => 200],
            [['name', 'controller', 'action'], 'string', 'max' => 100],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Modules::className(), 'targetAttribute' => ['module_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'module_id' => Yii::t('app', 'Module'),
            'title' => Yii::t('app', 'Title'),
            'name' => Yii::t('app', 'Name'),
            'controller' => Yii::t('app', 'Controller'),
            'action' => Yii::t('app', 'Action'),
        ];
    }

    /**
     * Gets query for [[Module]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Modules::className(), ['id' => 'module_id']);
    }

    /**
     * Gets query for [[RoleAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleAccesses()
    {
        return $this->hasMany(RoleAccess::className(), ['permission_id' => 'id']);
    }
}
