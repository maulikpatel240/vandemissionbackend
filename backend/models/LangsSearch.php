<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Langs;

/**
 * LangsSearch represents the model behind the search form of `backend\models\Langs`.
 */
class LangsSearch extends Langs
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['lang_key', 'type', 'english', 'gujarati', 'hindi'], 'safe'],
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
    public function search($params, $pageSize)
    {
        $query = (isset($params['sort']) && $params['sort']) ? Langs::find() : Langs::find()->orderBy(['id' => SORT_DESC]);
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
        ]);

        $query->andFilterWhere(['like', 'lang_key', $this->lang_key])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'english', $this->english])
            ->andFilterWhere(['like', 'gujarati', $this->gujarati])
            ->andFilterWhere(['like', 'hindi', $this->hindi]);

        return $dataProvider;
    }
}
