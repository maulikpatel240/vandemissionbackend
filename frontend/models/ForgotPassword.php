<?php
namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class ForgotPassword extends Model
{
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email']
        ];
    }
     public function attributeLabels(){
        return [
            'email'=>Yii::t('app','Email'),
        ];
    }
}