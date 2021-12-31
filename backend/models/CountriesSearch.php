<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Countries;

/**
 * CountriesSearch represents the model behind the search form of `backend\models\Countries`.
 */
class CountriesSearch extends Countries {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['latitude', 'longitude', 'status', 'status_at', 'created_at', 'updated_at', 'lang_key', 'english', 'gujarati', 'hindi'], 'safe'],
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
        $query = (isset($params['sort']) && $params['sort']) ? Countries::find() : Countries::find()->orderBy(['id' => SORT_DESC]);
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
            'status_at' => $this->status_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'latitude', $this->latitude])
                ->andFilterWhere(['like', 'longitude', $this->longitude])
                ->andFilterWhere(['=', 'status', $this->status])
                ->andFilterWhere(['like', 'lang_key', $this->lang_key])
                ->andFilterWhere(['like', 'english', $this->english])
                ->andFilterWhere(['like', 'gujarati', $this->gujarati])
                ->andFilterWhere(['like', 'hindi', $this->hindi]);

        return $dataProvider;
    }

}