<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "products_categories".
 *
 * @property int $id
 * @property int $name
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Langs $name0
 */
class ProductsCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'integer'],
            [['status'], 'string'],
            [['status_at', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'exist', 'skipOnError' => true, 'targetClass' => Langs::className(), 'targetAttribute' => ['name' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Name0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getName0()
    {
        return $this->hasOne(Langs::className(), ['id' => 'name']);
    }
}
