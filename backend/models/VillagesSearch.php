<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Villages;

/**
 * VillagesSearch represents the model behind the search form of `backend\models\Villages`.
 */
class VillagesSearch extends Villages {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'country_id', 'state_id', 'district_id', 'subdistrict_id', 'block_id', 'code', 'page'], 'integer'],
            [['english', 'pincode', 'officename', 'latitude', 'longitude', 'bounding_box', 'map', 'status', 'status_at', 'created_at', 'updated_at', 'lang_key', 'gujarati', 'hindi'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize) {
        $query = (isset($params['sort']) && $params['sort']) ? Villages::find() : Villages::find()->orderBy(['id' => SORT_DESC]);
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'district_id' => $this->district_id,
            'subdistrict_id' => $this->subdistrict_id,
            'block_id' => $this->block_id,
            'code' => $this->code,
            'status_at' => $this->status_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'page' => $this->page,
        ]);

        $query->andFilterWhere(['like', 'english', $this->english])
                ->andFilterWhere(['like', 'pincode', $this->pincode])
                ->andFilterWhere(['like', 'officename', $this->officename])
                ->andFilterWhere(['like', 'latitude', $this->latitude])
                ->andFilterWhere(['like', 'longitude', $this->longitude])
                ->andFilterWhere(['like', 'bounding_box', $this->bounding_box])
                ->andFilterWhere(['like', 'map', $this->map])
                ->andFilterWhere(['like', 'status', $this->status])
                ->andFilterWhere(['like', 'lang_key', $this->lang_key])
                ->andFilterWhere(['like', 'gujarati', $this->gujarati])
                ->andFilterWhere(['like', 'hindi', $this->hindi]);

        return $dataProvider;
    }
    
    
    public function searchlocation($params, $limit) {
        $query = Villages::find()->where(['villages.status' => 'Active']);
        $query->joinWith([
                'block' => function ($query) {
                    $query->select(['id', 'english', 'map', 'gujarati', 'hindi']);
                },
                'subdistrict' => function ($query) {
                    $query->select(['id', 'english', 'map', 'gujarati', 'hindi']);
                },
                'district' => function ($query) {
                    $query->select(['id', 'english', 'map', 'gujarati', 'hindi']);
                },
                'state' => function ($query) {
                    $query->select(['id', 'english', 'map', 'gujarati', 'hindi']);
                },
                'country' => function ($query) {
                    $query->select(['id', 'english', 'map', 'gujarati', 'hindi']);
                }
            ]);
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andWhere(['blocks.status' => 'Active']);
        $query->andWhere(['subdistricts.status' => 'Active']);
        $query->andWhere(['districts.status' => 'Active']);
        $query->andWhere(['states.status' => 'Active']);
        $query->andWhere(['countries.status' => 'Active']);
        $query->andFilterWhere(['like', 'villages.english', $this->english])
                ->andFilterWhere(['like', 'villages.pincode', $this->pincode])
                ->andFilterWhere(['like', 'villages.officename', $this->officename]);
        $query->limit($limit);
        return $dataProvider;
    }

}
