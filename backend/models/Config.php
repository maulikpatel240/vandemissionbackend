<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "config".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $status
 * @property string|null $status_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string $gujarati
 * @property string $hindi
 */
class Config extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['value', 'description', 'type', 'status'], 'string'],
            [['status_at', 'created_at', 'updated_at'   ], 'safe'],
            [['name', 'gujarati', 'hindi'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'description' => 'Description',
            'type' => 'Type',
            'status' => 'Status',
            'status_at' => 'Status At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'gujarati' => 'Gujarati',
            'hindi' => 'Hindi',
        ];
    }

    public function upload() {
        if ($this->value && $this->type == "File") {
            //$this->map->baseName = $this->id.'_state';
            $filename = 'config_' . $this->id . '_'.time().'.' . $this->value->extension;
            $this->value->saveAs(Yii::getAlias('@webroot') . '/uploads/settings/' . $filename, false);
            $this->value = $filename;
            $this->save();
            return true;
        } else {
            return false;
        }
    }

    public function deleteImage($oldimg = "") {
        if ($this->type == "File") {
            if ($oldimg) {
                $image = Yii::getAlias('@webroot') . '/uploads/settings/' . $oldimg;
                if (file_exists($image) && unlink($image)) {
                    return true;
                }
            } else {
                $image = Yii::getAlias('@webroot') . '/uploads/settings/' . $this->value;
                if (file_exists($image) && unlink($image)) {
                    $this->value = '';
                    $this->save();
                    return true;
                }
            }
        }
        return false;
    }

}
