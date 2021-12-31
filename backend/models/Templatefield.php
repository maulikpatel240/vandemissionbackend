<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "templatefield".
 *
 * @property int $id
 * @property int $email_template_id
 * @property string $field
 * @property string $description
 * @property string $is_default
 * @property string|null $created_at
 *
 * @property EmailTemplates $emailTemplate
 */
class Templatefield extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'templatefield';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email_template_id', 'field', 'description'], 'required'],
            [['email_template_id'], 'integer'],
            [['description', 'is_default'], 'string'],
            [['created_at'], 'safe'],
            [['field'], 'string', 'max' => 255],
            [['email_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplates::className(), 'targetAttribute' => ['email_template_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email_template_id' => 'Email Template ID',
            'field' => 'Field',
            'description' => 'Description',
            'is_default' => 'Is Default',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[EmailTemplate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmailTemplate()
    {
        return $this->hasOne(EmailTemplates::className(), ['id' => 'email_template_id']);
    }
}
