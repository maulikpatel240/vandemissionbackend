<?php

namespace frontend\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\models\Htmlcode;
use frontend\models\AppModules;
use frontend\models\UserResponse;
use frontend\models\UserQuestionsAnswer;
use frontend\models\OwnerManager;
use frontend\models\OwnerUserDuty;
use frontend\models\OwnerUserOpenHours;
use frontend\models\roc\User;
use frontend\models\roc\StaffUser;
use frontend\models\roc\StaffWorkExperience;
use frontend\models\roc\OwnerUser;

class RocGetObject extends Component {

    public $_user = "";
    public $array;
    public $pairs;
    public $key;
    public $value;
    public $key_sorting = "ASC"; //ASC, DESC, Normal
    public $string = "", $model = "", $title = "", $cc = "", $extra_check = false, $language_id = false;
    public $_baseUrl = "/";
    public $_basePath = "/";

    public function init() {
        parent::init();
        $this->_user = Yii::$app->user->identity;
        $this->_baseUrl = Url::base(true).'/';
        $this->_basePath = Url::base().'/';
    }
    public function UserResponseObject($outputdata){
        $model = new UserResponse();
        if($outputdata){
            // $staffmodel->load($staffdata, ''); // '' = formname or empty string
            $model->attributes = $outputdata; 
            $model->unique_id = $outputdata['email'];
            $model->language_id = $outputdata['preferred_language_id'];
            $model->preferred_language_id = $outputdata['preferred_language_id'];
            $model->social_id = (isset($outputdata['social_id']) && $outputdata['social_id'])?$outputdata['social_id']:"";
            $model->fptoken = "";
            $model->message = (isset($output['message']) && $output['message'])?$output['message']:"";
            $model->result = (isset($output['result']) && $output['result'])?$output['result']:"";
            $model->status = (isset($output['status']) && $output['status'])?$output['status']:"";
        }
        return $model;
    }
    public function UserObject($user,$profiledata){
        $model = new User();
        if($profiledata){
            if(isset($outputdata['staffdata'])){
                unset($outputdata['staffdata']);
            }
            if(isset($outputdata['ownerdata'])){
                unset($outputdata['ownerdata']);
            }
            $model->attributes = $profiledata; 
            $model->user_id = $user->id;
            $model->unique_id = $user->unique_id;
            $model->language_id = $user->language_id;
        }
        return $model;
    }
    
    public function StaffUserObject($user,$profiledata){
        $model = new StaffUser();
        if(isset($profiledata['staffdata']) && is_array($profiledata['staffdata']) && $profiledata['staffdata']){
            $staffdata = $profiledata['staffdata'][0];
            if($staffdata){
                $model->attributes = $staffdata; 
            }
        }
        return $model;
    }
    public function StaffWorkExperienceObject($user,$profiledata){
        $modeldata = array();
        if(isset($profiledata['staffdata']) && is_array($profiledata['staffdata']) && $profiledata['staffdata']){
            $staffdata = $profiledata['staffdata'][0];
            if(isset($staffdata['work_experience']) && is_array($staffdata['work_experience']) && $staffdata['work_experience']){
                foreach ($staffdata['work_experience'] as $exp){
                    $model = new StaffWorkExperience();
                    $model->attributes = $exp; 
                    $knowsoftdata = array();
                    if(isset($exp['user_software']) && $exp['user_software']){
                        foreach ($exp['user_software'] as $knowsoft){
                            $knowsoftdata[] = $knowsoft['software_id'];
                        }
                    }
                    $model->knownsw = $knowsoftdata; 
                    $modeldata[] = $model;
                }
            }
        }
        return $modeldata;
    }
    public function UserQuestionsAnswerObject($user,$profiledata,$step = '6',$qid=""){
        $modeldata = array();
        if(isset($profiledata['staffdata']) && is_array($profiledata['staffdata']) && $profiledata['staffdata']){
            $staffdata = $profiledata['staffdata'][0];
            if($step == '6' && isset($staffdata['user_question6']) && is_array($staffdata['user_question6']) && $staffdata['user_question6']){
                if($qid){
                    $modeldata = Yii::$app->FrontFunctions->multiSearch($staffdata['user_question6'], array('question_id' => $qid));
                    if($modeldata && count($modeldata) == 1){
                        $model = new UserQuestionsAnswer();
                        $model->attributes = $modeldata[0]; 
                        $model->is_comment = $modeldata[0]['is_comment']; 
                        $model->comment = $modeldata[0]['comment']; 
                        $model->option = $modeldata[0]['options']; 
                        $modeldata = $model;
                    }
                }else{
                    foreach ($staffdata['user_question6'] as $q){
                        $model = new UserQuestionsAnswer();
                        $model->attributes = $q; 
                        $modeldata[] = $model;
                    }
                }
            }
            if($step == '7'  && isset($staffdata['user_question7']) && is_array($staffdata['user_question7']) && $staffdata['user_question7']){
                if($qid){
                    $modeldata = Yii::$app->FrontFunctions->multiSearch($staffdata['user_question7'], array('question_id' => $qid));
                    if($modeldata && count($modeldata) == 1){
                        $model = new UserQuestionsAnswer();
                        $model->attributes = $modeldata[0]; 
                        $model->is_comment = $modeldata[0]['is_comment']; 
                        $model->comment = $modeldata[0]['comment']; 
                        $model->option = $modeldata[0]['options']; 
                        $modeldata = $model;
                    }
                }else{
                    foreach ($staffdata['user_question7'] as $q){
                        $model = new UserQuestionsAnswer();
                        $model->attributes = $q;  
                        $modeldata[] = $model;
                    }
                }
            }
        }
        return $modeldata;
    }
    
    public function OwnerUserObject($user,$profiledata,$owner_user_id="",$arraytype = ""){
        //$arraytype == single ? [''=>''] : $arraytype == multi ? [[0]=>[''=>''],[1]=>[''=>'']]
        $modeldata = new OwnerUser();
        if($owner_user_id){
            $restapiData = array();
            $restapiData['owner_user_id'] = $owner_user_id;
            $profileoutputowner = Yii::$app->ApiCallFunctions->GetProfileApi($restapiData);
            $outputowner = $profileoutputowner['message'];
            if ($profileoutputowner['status'] == 200) {
                if (is_array($profileoutputowner['data']) && $profileoutputowner['data']) {
                    $outputowner = $profileoutputowner['data'];
                    if ($outputowner && isset($outputowner['ownerdata']) && $outputowner['ownerdata']) {
                        $outputownerdata = $outputowner['ownerdata'][0];
                        $model = new OwnerUser();
                        $model->attributes = $outputownerdata; 
                        $model->owner_user_id = $outputownerdata['owner_user_id']; 
                        $model->user_id = $user->user_id; 
                        $model->category_id = $outputownerdata['category_id']; 
                        $model->province_id = $outputownerdata['province_id']; 
                        $model->required_languages = $outputownerdata['required_languages']; 
                        $model->user_software = $outputownerdata['user_software'];  
                        $model->user_skill = $outputownerdata['user_skill']; 
                        $model->user_duty = $outputownerdata['user_duty']; 
                        $model->user_open_hours = $outputownerdata['user_open_hours']; 
                        $model->manager = $outputownerdata['owner_manager']; 
                        $modeldata = $model;
                    }
                }
            }
        }else{
            if(isset($profiledata['ownerdata']) && $profiledata['ownerdata']){
                foreach ($profiledata['ownerdata'] as $value){
                    $model = new OwnerUser();
                    $model->attributes = $value; 
                    $model->owner_user_id = $value['owner_user_id']; 
                    $model->user_id = $user->user_id; 
                    $model->category_id = $value['category_id']; 
                    $model->province_id = $value['province_id']; 
                    $model->required_languages = $value['required_languages']; 
                    $model->user_software = $value['user_software']; 
                    $model->user_skill = $value['user_skill']; 
                    $model->user_duty = $value['user_duty']; 
                    $model->user_open_hours = $value['user_open_hours']; 
                    $model->manager = $value['owner_manager']; 
    //                    $model->pharmacy_duty = $value['pharmacy_duty']; 
    //                    $model->pharmacy_technician = $value['pharmacy_technician']; 
    //                    $model->pharmacy_assistant = $value['pharmacy_assistant']; 
                    $modeldata[] = $model;
                }
            }
        }
        return $modeldata;
    }
    
    public function OwnerManagerObject($user,$OwnerManagerdata,$manager_id=""){
        $ownermodeldata = array();
        $modeldata = array();
        if(isset($OwnerManagerdata) && is_array($OwnerManagerdata) && $OwnerManagerdata){
            if($manager_id){
                $ownermanagerdataArray = array();
                $ownermanagerdataArray[] = $OwnerManagerdata;
                $modeldata = Yii::$app->FrontFunctions->multiSearch($ownermanagerdataArray, array('manager_id' => $manager_id));
                if($modeldata && count($modeldata) == 1){
                    $model = new OwnerManager();
                    $model->attributes = $modeldata[0]; 
                    $modeldata = $model;
                }
            }else{
                foreach ($OwnerManagerdata as $value){
                    $model = new OwnerManager();
                    $model->attributes = $value; 
                    $modeldata[] = $model;
                }
            }
        }
        return $modeldata;
    }
    
    public function DutyObject($user,$Dutydata,$category_id="") {
        $ownermodeldata = array();
        $modeldata = array();
        if(isset($Dutydata) && is_array($Dutydata) && $Dutydata){
            if($category_id){
                $dutydataArray = array();
                $dutydataArray = $Dutydata;
                $modeldata = Yii::$app->FrontFunctions->multiSearch($dutydataArray, array('category_id' => $category_id));
                if($modeldata && count($modeldata) == 1){
                    $model = new OwnerUserDuty();
                    $model->attributes = $modeldata[0]; 
                    $modeldata = $model;
                }
            }else{
                foreach ($Dutydata as $value){
                    $model = new OwnerUserDuty();
                    $model->attributes = $value; 
                    $modeldata[] = $model;
                }
            }
        }
        return $modeldata;
    }
    
    public function OwnerUserOpenHoursObject($user,$UserOpenHoursdata){
        $modeldata = array();
        if(isset($UserOpenHoursdata) && is_array($UserOpenHoursdata) && $UserOpenHoursdata){
            foreach ($UserOpenHoursdata as $value){
                $model = new OwnerUserOpenHours();
                $model->attributes = $value; 
                $modeldata[] = $model;
            }
        }
        return $modeldata;
    }
    
}
