<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Users;

/**
 * UsersSearch represents the model behind the search form of `backend\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ip_address', 'birth_privacy'], 'integer'],
            [['username', 'email', 'phone_number', 'first_name', 'last_name', 'middle_name', 'avatar', 'aadhaar_card_number', 'aadhaar_card_photo', 'gender', 'birthday', 'email_code', 'sms_code', 'verified', 'address', 'latitude', 'longitude'], 'safe'],
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
    public function search($params)
    {
        $query = Users::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'birthday' => $this->birthday,
            'ip_address' => $this->ip_address,
            'birth_privacy' => $this->birth_privacy,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'aadhaar_card_number', $this->aadhaar_card_number])
            ->andFilterWhere(['like', 'aadhaar_card_photo', $this->aadhaar_card_photo])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'email_code', $this->email_code])
            ->andFilterWhere(['like', 'sms_code', $this->sms_code])
            ->andFilterWhere(['like', 'verified', $this->verified])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'latitude', $this->latitude])
            ->andFilterWhere(['like', 'longitude', $this->longitude]);

        return $dataProvider;
    }
}
