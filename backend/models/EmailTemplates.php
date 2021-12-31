<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "email_templates".
 *
 * @property int $id
 * @property string $type
 * @property string $code
 * @property string $title
 * @property string|null $subject
 * @property string|null $html
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Templatefield[] $templatefields
 */
class EmailTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'title', 'subject', 'html', 'status'], 'string'],
            [['code', 'title'], 'required'],
            [['status_at', 'created_at', 'updated_at'], 'safe'],
            [['code', 'title', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'code' => 'Code',
            'title' => 'Title',
            'subject' => 'Subject',
            'html' => 'Html',
            'status' => 'Status',
            'status_at' => 'Status Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Templatefields]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemplatefields()
    {
        return $this->hasMany(Templatefield::className(), ['email_template_id' => 'id']);
    }
}
