<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pincode".
 *
 * @property int $id
 * @property string $CircleName
 * @property string $RegionName
 * @property string $DivisionName
 * @property string $OfficeName
 * @property string $Pincode
 * @property string $OfficeType
 * @property string $Delivery
 * @property string $District
 * @property string $StateName
 */
class Pincode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pincode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['CircleName', 'RegionName', 'DivisionName', 'OfficeName', 'Pincode', 'OfficeType', 'Delivery', 'District', 'StateName'], 'required'],
            [['CircleName', 'RegionName', 'DivisionName', 'OfficeName', 'Pincode', 'OfficeType', 'Delivery', 'District', 'StateName'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'CircleName' => 'Circle Name',
            'RegionName' => 'Region Name',
            'DivisionName' => 'Division Name',
            'OfficeName' => 'Office Name',
            'Pincode' => 'Pincode',
            'OfficeType' => 'Office Type',
            'Delivery' => 'Delivery',
            'District' => 'District',
            'StateName' => 'State Name',
        ];
    }
}
