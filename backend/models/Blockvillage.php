<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "blockvillage".
 *
 * @property int $id
 * @property int $state_code
 * @property string $state_name
 * @property int $district_code
 * @property string $district_name
 * @property int $subdistrict_code
 * @property string $subdistrict_name
 * @property int $village_code
 * @property string $village_name
 * @property int $block_code
 * @property string $block_name
 * @property string $pincode
 * @property string $officename
 */
class Blockvillage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blockvillage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state_code', 'state_name', 'district_code', 'district_name', 'subdistrict_code', 'subdistrict_name', 'village_code', 'village_name', 'block_code', 'block_name', 'pincode', 'officename'], 'required'],
            [['state_code', 'district_code', 'subdistrict_code', 'village_code', 'block_code'], 'integer'],
            [['state_name', 'district_name', 'subdistrict_name', 'village_name', 'block_name', 'pincode', 'officename'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'state_code' => 'State Code',
            'state_name' => 'State Name',
            'district_code' => 'District Code',
            'district_name' => 'District Name',
            'subdistrict_code' => 'Subdistrict Code',
            'subdistrict_name' => 'Subdistrict Name',
            'village_code' => 'Village Code',
            'village_name' => 'Village Name',
            'block_code' => 'Block Code',
            'block_name' => 'Block Name',
            'pincode' => 'Pincode',
            'officename' => 'Officename',
        ];
    }
}
