<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "districts".
 *
 * @property int $id
 * @property int $country_id
 * @property int $state_id
 * @property string $english
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $bounding_box
 * @property string $map
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $code
 * @property string $lang_key
 * @property string|null $gujarati
 * @property string|null $hindi
 *
 * @property Blocks[] $blocks
 * @property Countries $country
 * @property States $state
 * @property Subdistricts[] $subdistricts
 * @property Villages[] $villages
 */
class Districts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'districts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'state_id', 'english'], 'required'],
            [['country_id', 'state_id', 'code'], 'integer'],
            [['bounding_box', 'status'], 'string'],
            [['latitude', 'longitude', 'bounding_box', 'gujarati', 'hindi'], 'default', 'value' => null],
            [['status_at', 'created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'string', 'max' => 50],
            [['english', 'lang_key', 'gujarati', 'hindi'], 'string', 'max' => 255],
            [['map'], 'file', 'skipOnEmpty' => true, 'extensions' => Yii::$app->params['image_extention']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['state_id'], 'exist', 'skipOnError' => true, 'targetClass' => States::className(), 'targetAttribute' => ['state_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country',
            'state_id' => 'State',
            'english' => 'English',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'bounding_box' => 'Bounding Box',
            'map' => 'Map',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'code' => 'Code',
            'lang_key' => 'Lang Key',
            'gujarati' => 'Gujarati',
            'hindi' => 'Hindi',
        ];
    }

    public function upload()
    {
        if($this->map){
            //$this->map->baseName = $this->id.'_state';
            $filename = 'district_'.$this->id.'.' . $this->map->extension;
            $this->map->saveAs(Yii::getAlias('@webroot').'/uploads/map/district/' . $filename, false);
            $this->map = $filename;
            $this->save();
            return true;
        }else{
            return false;
        }
    }
    public function deleteImage($oldimg = "") {
        if($oldimg){
            $image = Yii::getAlias('@webroot').'/uploads/map/district/' . $oldimg;
            if (file_exists($image) && unlink($image)) {
                return true;
            }
        }else{
            $image = Yii::getAlias('@webroot').'/uploads/map/district/' . $this->map;
            if (file_exists($image) && unlink($image)) {
                $this->map = '';
                $this->save();
                return true;
            }
        }
        return false;
    }
    /**
     * Gets query for [[Blocks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocks()
    {
        return $this->hasMany(Blocks::className(), ['district_id' => 'id']);
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

    /**
     * Gets query for [[State]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(States::className(), ['id' => 'state_id']);
    }

    /**
     * Gets query for [[Subdistricts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubdistricts()
    {
        return $this->hasMany(Subdistricts::className(), ['district_id' => 'id']);
    }

    /**
     * Gets query for [[Villages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVillages()
    {
        return $this->hasMany(Villages::className(), ['district_id' => 'id']);
    }
}
