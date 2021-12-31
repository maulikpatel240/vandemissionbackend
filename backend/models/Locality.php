<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "locality".
 *
 * @property int $id
 * @property string $english
 * @property string $officename
 * @property string $pincode
 * @property string $subdistrict
 * @property string $district
 * @property string $state
 */
class Locality extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'locality';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['english', 'officename', 'pincode', 'subdistrict', 'district', 'state'], 'required'],
            [['code'], 'safe'],
            [['english', 'officename', 'pincode', 'subdistrict', 'district', 'state'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'english' => 'English',
            'officename' => 'Officename',
            'pincode' => 'Pincode',
            'subdistrict' => 'Subdistrict',
            'district' => 'District',
            'state' => 'State',
        ];
    }
}
