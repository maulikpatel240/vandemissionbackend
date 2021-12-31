<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "langs".
 *
 * @property int $id
 * @property string $lang_key
 * @property string|null $type
 * @property string|null $english
 * @property string|null $gujarati
 * @property string|null $hindi
 *
 * @property BlogsCategories[] $blogsCategories
 * @property GroupsCategories[] $groupsCategories
 * @property JobCategories[] $jobCategories
 * @property PagesCategories[] $pagesCategories
 * @property ProductsCategories[] $productsCategories
 * @property Societies[] $societies
 */
class Langs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'langs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lang_key', 'type', 'english'], 'required'],
            [['english', 'gujarati', 'hindi'], 'string'],
            [['lang_key'], 'string', 'max' => 200],
            [['type'], 'safe'],
            [['lang_key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lang_key' => 'Lang Key',
            'type' => 'Type',
            'english' => 'English',
            'gujarati' => 'Gujarati',
            'hindi' => 'Hindi',
        ];
    }

    /**
     * Gets query for [[BlogsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlogsCategories()
    {
        return $this->hasMany(BlogsCategories::className(), ['name' => 'id']);
    }

    /**
     * Gets query for [[GroupsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupsCategories()
    {
        return $this->hasMany(GroupsCategories::className(), ['name' => 'id']);
    }

    /**
     * Gets query for [[JobCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJobCategories()
    {
        return $this->hasMany(JobCategories::className(), ['name' => 'id']);
    }

    /**
     * Gets query for [[PagesCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPagesCategories()
    {
        return $this->hasMany(PagesCategories::className(), ['name' => 'id']);
    }

    /**
     * Gets query for [[ProductsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductsCategories()
    {
        return $this->hasMany(ProductsCategories::className(), ['name' => 'id']);
    }
    
    /**
     * Gets query for [[Societies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocieties()
    {
        return $this->hasMany(Societies::className(), ['name' => 'id']);
    }
}
