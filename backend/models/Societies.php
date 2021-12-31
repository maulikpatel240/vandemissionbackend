<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "societies".
 *
 * @property int $id
 * @property int $name
 * @property string $logo
 * @property string $headquarters
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Langs $name0
 */
class Societies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'societies';
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
            [['logo', 'headquarters'], 'string', 'max' => 200],
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
            'logo' => 'Logo',
            'headquarters' => 'Headquarters',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function upload()
    {
        if($this->logo){
            //$this->logo->baseName = $this->id.'_state';
            $filename = 'society_'.$this->id.'.' . $this->logo->extension;
            $this->logo->saveAs(Yii::getAlias('@webroot').'/uploads/society/' . $filename, false);
            $this->logo = $filename;
            $this->save();
            return true;
        }else{
            return false;
        }
    }
    public function deleteImage($oldimg = "") {
        if($oldimg){
            $image = Yii::getAlias('@webroot').'/uploads/society/' . $oldimg;
            if (file_exists($image) && unlink($image)) {
                return true;
            }
        }else{
            $image = Yii::getAlias('@webroot').'/uploads/society/' . $this->logo;
            if (file_exists($image) && unlink($image)) {
                $this->logo = '';
                $this->save();
                return true;
            }
        }
        return false;
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
