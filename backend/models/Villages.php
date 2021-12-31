<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "villages".
 *
 * @property int $id
 * @property int $country_id
 * @property int $state_id
 * @property int $district_id
 * @property int|null $subdistrict_id
 * @property int|null $block_id
 * @property string $english
 * @property string|null $pincode
 * @property string|null $officename
 * @property int $code
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $bounding_box
 * @property string $map
 * @property string $status
 * @property string $status_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $lang_key
 * @property string|null $gujarati
 * @property string|null $hindi
 * @property int $page
 *
 * @property Countries $country
 * @property States $state
 * @property Districts $district
 * @property Subdistricts $subdistrict
 * @property Blocks $block
 */
class Villages extends \yii\db\ActiveRecord {

//    public $block_name="";
//    public $subdistrict_name="";
//    public $district_name="";
//    public $state_name="";
//    public $country_name="";
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'villages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['country_id', 'state_id', 'district_id', 'english'], 'required'],
            [['country_id', 'state_id', 'district_id', 'subdistrict_id', 'block_id', 'code', 'page'], 'integer'],
            [['bounding_box', 'status'], 'string'],
            [['status_at', 'created_at', 'updated_at'], 'safe'],
            [['pincode'], 'string', 'max' => 10],
            [['english', 'lang_key', 'gujarati', 'hindi'], 'string', 'max' => 255],
            [['officename'], 'string', 'max' => 200],
            [['latitude', 'longitude'], 'string', 'max' => 50],
            [['map'], 'file', 'skipOnEmpty' => true, 'extensions' => Yii::$app->params['image_extention']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['state_id'], 'exist', 'skipOnError' => true, 'targetClass' => States::className(), 'targetAttribute' => ['state_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => Districts::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['subdistrict_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subdistricts::className(), 'targetAttribute' => ['subdistrict_id' => 'id']],
            [['block_id'], 'exist', 'skipOnError' => true, 'targetClass' => Blocks::className(), 'targetAttribute' => ['block_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'country_id' => 'Country',
            'state_id' => 'State',
            'district_id' => 'District',
            'subdistrict_id' => 'Subdistrict',
            'block_id' => 'Block',
            'english' => 'English',
            'pincode' => 'Pincode',
            'officename' => 'Officename',
            'code' => 'Code',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'bounding_box' => 'Bounding Box',
            'map' => 'Map',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'lang_key' => 'Lang Key',
            'gujarati' => 'Gujarati',
            'hindi' => 'Hindi',
            'page' => 'Page',
        ];
    }
    public function upload()
    {
        if($this->map){
            //$this->map->baseName = $this->id.'_state';
            $filename = 'village_'.$this->id.'.' . $this->map->extension;
            $this->map->saveAs(Yii::getAlias('@webroot').'/uploads/map/village/' . $filename, false);
            $this->map = $filename;
            $this->save();
            return true;
        }else{
            return false;
        }
    }
    public function deleteImage($oldimg = "") {
        if($oldimg){
            $image = Yii::getAlias('@webroot').'/uploads/map/village/' . $oldimg;
            if (file_exists($image) && unlink($image)) {
                return true;
            }
        }else{
            $image = Yii::getAlias('@webroot').'/uploads/map/village/' . $this->map;
            if (file_exists($image) && unlink($image)) {
                $this->map = '';
                $this->save();
                return true;
            }
        }
        return false;
    }
    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry() {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

    /**
     * Gets query for [[State]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState() {
        return $this->hasOne(States::className(), ['id' => 'state_id']);
    }

    /**
     * Gets query for [[District]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict() {
        return $this->hasOne(Districts::className(), ['id' => 'district_id']);
    }

    /**
     * Gets query for [[Subdistrict]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubdistrict() {
        return $this->hasOne(Subdistricts::className(), ['id' => 'subdistrict_id']);
    }

    /**
     * Gets query for [[Block]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlock() {
        return $this->hasOne(Blocks::className(), ['id' => 'block_id']);
    }

}
