<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "modules".
 *
 * @property int $id
 * @property string $functionality
 * @property string $type
 * @property int $menu_id
 * @property int $parent_menu_id
 * @property int $parent_submenu_id
 * @property string $title
 * @property string $url
 * @property string $icon
 * @property int $menu_position
 * @property int $submenu_position
 * @property string $display 1- Show  0-hide in datatablelist only
 * @property string $hiddden 1- sidemenu Module hide & direct url worked
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Permission[] $permissions
 */
class Modules extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'modules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['functionality', 'type', 'display', 'hiddden', 'status'], 'string'],
            [['menu_id', 'parent_menu_id', 'parent_submenu_id', 'menu_position', 'submenu_position'], 'integer'],
            [['title', 'functionality', 'menu_id', 'type'], 'required'],
            ['parent_menu_id', 'required', 'when' => function ($model) {
                    return ($model->type == 'Submenu' || $model->type == 'Subsubmenu') ? true : false;
                }, 'whenClient' => "function (attribute, value) {
                return ($('#modules-type').val() == 'Submenu' || $('#modules-type').val() == 'Subsubmenu') ? true : false;
            }"],
            ['parent_submenu_id', 'required', 'when' => function ($model) {
                    return ($model->type == 'Subsubmenu') ? true : false;
                }, 'whenClient' => "function (attribute, value) {
                return ($('#modules-type').val() == 'Subsubmenu') ? true : false;
            }"],
            ['parent_menu_id', 'required', 'when' => function ($model) {
                    return ($model->type == 'Submenu') ? true : false;
                }, 'whenClient' => "function (attribute, value) {
                return $('#modules-type').val() == 'Submenu' ? true : false;
            }"],
            [['status_at', 'created_at', 'updated_at'], 'safe'],
            [['title', 'controller', 'action'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 55],
            [['title'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'functionality' => Yii::t('app', 'Functionality'),
            'type' => Yii::t('app', 'Type'),
            'menu_id' => Yii::t('app', 'Menu ID'),
            'parent_menu_id' => Yii::t('app', 'Parent Menu ID'),
            'parent_submenu_id' => Yii::t('app', 'Parent Submenu ID'),
            'title' => Yii::t('app', 'Title'),
            'controller' => Yii::t('app', 'Controller'),
            'action' => Yii::t('app', 'Action'),
            'icon' => Yii::t('app', 'Icon'),
            'menu_position' => Yii::t('app', 'Menu Position'),
            'submenu_position' => Yii::t('app', 'Submenu Position'),
            'display' => Yii::t('app', '1- Show  0-hide in datatablelist only'),
            'hiddden' => Yii::t('app', '1- sidemenu Module hide & direct url worked'),
            'status' => Yii::t('app', 'Status'),
            'status_at' => Yii::t('app', 'Status At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Permissions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions() {
        return $this->hasMany(Permission::className(), ['module_id' => 'id']);
    }

}
