<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property int $page
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page'], 'required'],
            [['page'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page' => 'Page',
        ];
    }
}
