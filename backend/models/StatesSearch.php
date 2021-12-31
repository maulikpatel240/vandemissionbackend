<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\States;

/**
 * StatesSearch represents the model behind the search form of `backend\models\States`.
 */
class StatesSearch extends States {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'country_id'], 'integer'],
            [['latitude', 'longitude', 'bounding_box', 'status', 'status_at', 'created_at', 'updated_at', 'lang_key', 'english', 'gujarati', 'hindi'], 'safe'],
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
        $query = (isset($params['sort']) && $params['sort']) ? States::find() : States::find()->orderBy(['id' => SORT_DESC]);
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
            'status_at' => $this->status_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'latitude', $this->latitude])
                ->andFilterWhere(['like', 'longitude', $this->longitude])
                ->andFilterWhere(['like', 'bounding_box', $this->bounding_box])
                ->andFilterWhere(['=', 'status', $this->status])
                ->andFilterWhere(['like', 'lang_key', $this->lang_key])
                ->andFilterWhere(['like', 'english', $this->english])
                ->andFilterWhere(['like', 'gujarati', $this->gujarati])
                ->andFilterWhere(['like', 'hindi', $this->hindi]);

        return $dataProvider;
    }

}
