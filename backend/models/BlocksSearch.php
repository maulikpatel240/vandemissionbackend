<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Blocks;

/**
 * BlocksSearch represents the model behind the search form of `backend\models\Blocks`.
 */
class BlocksSearch extends Blocks
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'country_id', 'state_id', 'district_id', 'code'], 'integer'],
            [['english', 'latitude', 'longitude', 'bounding_box', 'map', 'status', 'status_at', 'created_at', 'updated_at', 'lang_key', 'gujarati', 'hindi'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params,$pageSize)
    {
        $query = (isset($params['sort']) && $params['sort']) ? Blocks::find() : Blocks::find()->orderBy(['id' => SORT_DESC]);
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
            'code' => $this->code,
            'status_at' => $this->status_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'english', $this->english])
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
}
