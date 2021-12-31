<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "countries".
 *
 * @property int $id
 * @property string $english
 * @property string|null $gujarati
 * @property string|null $hindi
 * @property string $latitude
 * @property string $longitude
 * @property string $map
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string $lang_key
 *
 * @property Blocks[] $blocks
 * @property Districts[] $districts
 * @property States[] $states
 * @property Subdistricts[] $subdistricts
 * @property Villages[] $villages
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['english'], 'required'],
            [['bounding_box', 'status'], 'string'],
            [['latitude', 'longitude', 'bounding_box', 'gujarati', 'hindi'], 'default', 'value' => null],
            [['status_at', 'created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'string', 'max' => 50],
            [['english', 'lang_key', 'gujarati', 'hindi'], 'string', 'max' => 255],
            [['map'], 'string', 'max' => 200],
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
            'gujarati' => 'Gujarati',
            'hindi' => 'Hindi',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'map' => 'Map',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'lang_key' => 'Lang Key',
        ];
    }

    /**
     * Gets query for [[Blocks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocks()
    {
        return $this->hasMany(Blocks::className(), ['country_id' => 'id']);
    }

    /**
     * Gets query for [[Districts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistricts()
    {
        return $this->hasMany(Districts::className(), ['country_id' => 'id']);
    }

    /**
     * Gets query for [[States]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStates()
    {
        return $this->hasMany(States::className(), ['country_id' => 'id']);
    }

    /**
     * Gets query for [[Subdistricts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubdistricts()
    {
        return $this->hasMany(Subdistricts::className(), ['country_id' => 'id']);
    }

    /**
     * Gets query for [[Villages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVillages()
    {
        return $this->hasMany(Villages::className(), ['country_id' => 'id']);
    }
}
