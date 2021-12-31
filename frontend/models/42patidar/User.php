<?php

namespace frontend\models\roc;

use Yii;
use yii\base\Model;
/**
 * This is the model class for table "user".
 *
 * @property int $user_id
 * @property string $type
 * @property string|null $step_completed
 * @property string $status
 * @property string|null $lastusedfrom
 * @property string|null $authkey
 * @property string|null $unique_id
 * @property string|null $email
 * @property int|null $module_id
 * @property int|null $language_id
 * @property int|null $preferred_language_id
 * @property string|null $full_name
 * @property string|null $password
 * @property string|null $social_id
 * @property string $loginwith
 * @property string|null $mobile
 * @property string $is_deleted
 * @property string|null $agreement_first_name
 * @property string|null $agreement_last_name
 * @property string $terms_conditions
 * @property string $privacy_policy
 * @property string $my_agreement
 * @property string|null $fptoken
 * @property string|null $referral_code
 * @property float|null $referral_balance
 * @property string|null $howdidyouhereaboutbelocum
 * @property string|null $review_alert For only Owner to submit Review
 * @property string $increase_hourlyrates_alert
 * @property int|null $reference_by Used By sales User only
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $utctimestamp
 *
 * @property OwnerUser[] $ownerUsers
 * @property SlotAdsAnalytics[] $slotAdsAnalytics
 * @property StaffUser[] $staffUsers
 * @property StaffWorkExperience[] $staffWorkExperiences
 * @property AppModules $module
 * @property AppLanguages $language
 * @property AppLanguages $preferredLanguage
 * @property UserDevice[] $userDevices
 * @property UserFavouriteblock[] $userFavouriteblocks
 * @property UserLicencein[] $userLicenceins
 * @property UserQuestionsAnswer[] $userQuestionsAnswers
 * @property UserReferralHistory[] $userReferralHistories
 * @property UserReferralHistory[] $userReferralHistories0
 * @property UserReferralRedeem[] $userReferralRedeems
 */
class User extends Model
{
    public $user_id = "";
    public $type = "";
    public $step_completed = "";
    public $status = "";
    public $lastusedfrom = "";
    public $authkey = "";
    public $unique_id = "";
    public $email = "";
    public $module_id = "";
    public $language_id = "";
    public $preferred_language_id = "";
    public $full_name = "";
    public $password = "";
    public $social_id = "";
    public $loginwith = "";
    public $mobile = "";
    public $is_deleted = "";
    public $agreement_first_name = "";
    public $agreement_last_name = "";
    public $terms_conditions = "";
    public $privacy_policy = "";
    public $my_agreement = "";
    public $fptoken = "";
    public $referral_code = "";
    public $referral_balance = "";
    public $howdidyouhereaboutbelocum = "";
    public $review_alert = "";
    public $increase_hourlyrates_alert = "";
    public $reference_by = "";
    public $created_at = "";
    public $updated_at = "";
    public $utctimestamp = "";
    public $referral_name = "";
    public $confirm_password, $general_comments, $important_comments, $interview_notes, $preferred_communication, $province_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // emailrequired
            [['email'], 'required', 'on' => ["emailrequired"]],
            //step-1 required
            [['module_id', 'language_id', 'preferred_language_id', 'type', 'email', 'loginwith', 'lastusedfrom', 'mobile'], 'required', 'on' => ["step1"]],
            ['mobile', 'match', 'pattern' => '#\d{3}-\d{3}-\d{4}#', 'on' => ["step1"]],
            ['password', 'required', 'when' => function($model) {
                    return $model->loginwith == 'Normal';
                }],
            ['social_id', 'required', 'when' => function($model) {
                    return $model->loginwith != 'Normal';
                }],
            ['confirm_password', 'required', 'on' => 'step1', 'when' => function($model) {
                    return $model->lastusedfrom == 'Webapp';
                }],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'when' => function($model) {
                    return $model->lastusedfrom == 'Webapp';
                }],  
            //step-2, step-5, updateprofile required            
            [['agreement_first_name', 'agreement_last_name', 'terms_conditions', 'privacy_policy', 'my_agreement', 'howdidyouhereaboutbelocum'], 'required', 'on' => ["step2", "step5", "updateprofile"]],     
            ['referral_name', 'required', 'when' => function ($model) {
                    return $model->howdidyouhereaboutbelocum == "Word of mouth";
                }, 'whenClient' => 'function (attribute, value) {
                    return $("#user-howdidyouhereaboutbelocum").val() == "Word of mouth" ? true : false;
                }', 'on' => ["step2", "step5"]],             
            //integer    
            [['module_id', 'language_id', 'preferred_language_id', 'reference_by', 'utctimestamp'], 'integer'],
            //number            
            [['referral_balance'], 'number'],
            //safe            
            [['created_at', 'updated_at','general_comments', 'important_comments', 'interview_notes', 'preferred_communication', 'review_alert', 'province_id', 'reference_by'], 'safe'],
            //Strings             
            [['type', 'status', 'lastusedfrom', 'loginwith', 'is_deleted', 'terms_conditions', 'privacy_policy', 'my_agreement', 'review_alert', 'increase_hourlyrates_alert'], 'string'],
            [['step_completed', 'unique_id', 'email', 'password', 'social_id', 'mobile', 'agreement_first_name', 'agreement_last_name', 'referral_code', 'howdidyouhereaboutbelocum'], 'string', 'max' => 100],
            [['authkey', 'full_name', 'fptoken'], 'string', 'max' => 255],
            //Custom Default Values         
            ['email', 'email'],            
            [['fptoken', 'full_name', 'referral_code', 'howdidyouhereaboutbelocum'], 'default', 'value' => NULL],
            [['status'], 'default', 'value' => 'In Complete'],
            [['is_deleted', 'review_alert'], 'default', 'value' => 'No'],
            [['terms_conditions', 'privacy_policy', 'my_agreement'], 'default', 'value' => 'Yes'],
            [['utctimestamp'], 'default', 'value' => Yii::$app->MyFunctions->currentUTCDatetime()],
            [['created_at', 'updated_at'], 'default', 'value' => Yii::$app->MyFunctions->currentDatetime()],
            [['authkey'], 'default', 'value' => Yii::$app->MyFunctions->generateAuthkey(150)],
            [['step_completed'], 'default', 'value' => 'step-1'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'step_completed' => Yii::t('app', 'Step Completed'),
            'status' => Yii::t('app', 'Status'),
            'lastusedfrom' => Yii::t('app', 'Lastusedfrom'),
            'authkey' => Yii::t('app', 'Authkey'),
            'unique_id' => Yii::t('app', 'Unique ID'),
            'email' => Yii::t('app', 'Email'),
            'module_id' => Yii::t('app', 'Module ID'),
            'language_id' => Yii::t('app', 'Language ID'),
            'preferred_language_id' => Yii::t('app', 'Preferred Language ID'),
            'full_name' => Yii::t('app', 'Full Name'),
            'password' => Yii::t('app', 'Password'),
            'social_id' => Yii::t('app', 'Social ID'),
            'loginwith' => Yii::t('app', 'Loginwith'),
            'mobile' => Yii::t('app', 'Mobile'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'agreement_first_name' => Yii::t('app', 'Agreement First Name'),
            'agreement_last_name' => Yii::t('app', 'Agreement Last Name'),
            'terms_conditions' => Yii::t('app', 'Terms Conditions'),
            'privacy_policy' => Yii::t('app', 'Privacy Policy'),
            'my_agreement' => Yii::t('app', 'My Agreement'),
            'fptoken' => Yii::t('app', 'Fptoken'),
            'referral_code' => Yii::t('app', 'Referral Code'),
            'referral_balance' => Yii::t('app', 'Referral Balance'),
            'howdidyouhereaboutbelocum' => Yii::t('app', 'Howdidyouhereaboutbelocum'),
            'review_alert' => Yii::t('app', 'For only Owner to submit Review'),
            'increase_hourlyrates_alert' => Yii::t('app', 'Increase Hourlyrates Alert'),
            'reference_by' => Yii::t('app', 'Used By sales User only'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'utctimestamp' => Yii::t('app', 'Utctimestamp'),
            'confirm_password' => Yii::t('app', 'Confirm password'),
            'general_comments' => Yii::t('app', 'General Comments'),
            'important_comments' => Yii::t('app', 'Important Comments'),
            'province_id' => Yii::t('app', 'Province'),
            'interview_notes' => Yii::t('app', 'Interview Notes'),
            'preferred_communication' => Yii::t('app', 'Preferred Communication'),
        ];
    }
}