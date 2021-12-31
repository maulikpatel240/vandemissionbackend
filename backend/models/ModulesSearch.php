<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Modules;

/**
 * ModulesSearch represents the model behind the search form of `backend\models\Modules`.
 */
class ModulesSearch extends Modules
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'menu_id', 'parent_menu_id', 'parent_submenu_id', 'menu_position', 'submenu_position'], 'integer'],
            [['functionality', 'type', 'title', 'controller', 'icon', 'display', 'hiddden', 'created_at'], 'safe'],
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
        $query = Modules::find()->where(['!=','functionality','none'])->orderBy(['id'=>SORT_DESC]);

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
            'menu_id' => $this->menu_id,
            'parent_menu_id' => $this->parent_menu_id,
            'parent_submenu_id' => $this->parent_submenu_id,
            'menu_position' => $this->menu_position,
            'submenu_position' => $this->submenu_position,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'functionality', $this->functionality])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'controller', $this->controller])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'display', $this->display])
            ->andFilterWhere(['like', 'hiddden', $this->hiddden]);

        return $dataProvider;
    }
}
