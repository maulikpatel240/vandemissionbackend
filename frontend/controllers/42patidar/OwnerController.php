<?php

namespace frontend\controllers\roc;

use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\UserLoginForm;
use frontend\models\OwnerManager;
use frontend\models\Promocode;
use frontend\models\OwnerUserDuty;
use frontend\models\OwnerUserOpenHours;
use frontend\models\OwnerCard;
use frontend\models\Documents;
use frontend\models\roc\OwnerUser;

/**
 * Staff controller
 */
class OwnerController extends Controller {

    public $_module_id = 2;
    public $_baseUrl = "/";
    public $_basePath = "/";
    public $_lang = "en";
    public $_langID = 1;
    public $_module = array();
    public $_moduleUrl = "/";
    public $_modulePath = "/";
    public $_ownerUrl = "/";
    public $_ownerPath = "/";
    public $_beforeownerUrl = "/";
    public $_beforeownerPath = "/";
    public $_user = array();
    public $_profiledataMsg = "";
    public $_profiledata = array();

    public function init() {
        parent::init();
        $this->layout = 'afterlogin';
        $this->_baseUrl = Url::base(true) . '/';
        $this->_basePath = Url::base() . '/';
        $this->_lang = Yii::$app->FrontFunctions->defaultlanguage();
        $this->_langID = Yii::$app->FrontFunctions->defaultlanguage(true);
        $this->_module = Yii::$app->FrontFunctions->AppModules($this->_module_id);
        $this->_moduleUrl = $this->_baseUrl . $this->_module['unique_name'] . '/';
        $this->_modulePath = $this->_basePath . $this->_module['unique_name'] . '/';
        $this->_ownerUrl = $this->_baseUrl . $this->_module['unique_name'] . '/owner/';
        $this->_ownerPath = '/' . $this->_module['unique_name'] . '/owner/';
        $this->_beforeownerUrl = $this->_baseUrl . $this->_module['unique_name'] . '/before-owner/';
        $this->_beforeownerPath = '/' . $this->_module['unique_name'] . '/before-owner/';

        $message = "";
        if (Yii::$app->user->identity && isset(Yii::$app->user->identity->message)) {
            $message = Yii::$app->user->identity->message;
        }
        if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity)) {
            $this->_user = Yii::$app->user->identity;
            $profileoutput = Yii::$app->ApiCallFunctions->GetProfileApi();
            $this->_profiledataMsg = $profileoutput['message'];
            if ($profileoutput['status'] == 200) {
                $this->_profiledata = $profileoutput['data'];
            }
        }
        if (empty($this->_user)) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect($this->_beforeownerPath);
        }
        $continue = true;
        if ($this->_user->type == "Staff") {
            $continue = false;
        }
        if (empty($continue)) {
            throw new \yii\web\HttpException(404, Yii::t('app', 'Page not found.'));
            exit;
        }
    }

    public function beforeAction($action) {
//        if ($this->action->id == 'slotapply') {
//            $this->enableCsrfValidation = false;
//        }
        $cookiedestory = Yii::$app->FrontFunctions->cookiedestory();
        if (isset($cookiedestory['result']) && isset($cookiedestory['type']) && $cookiedestory['result']) {
            $this->_moduleType = $cookiedestory['type'];
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforeownerPath]);
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $user = $this->_user;
        $output = Yii::$app->ApiCallFunctions->OwnerGetHomeDetails();
        $data = array();
        if ($output['status'] == 200) {
            $data = $output['data'];
            $session = Yii::$app->session;
            if (isset($data['UserModulesetting']) && $data['UserModulesetting']) {
                $session['UserModulesetting'] = $data['UserModulesetting'];
            }
        }
        $ownermodel = array();
        /* $staffmodel->scenario = "bankdetail";
          $staffmodel->sin = "";
          if ($staffmodel->load(Yii::$app->request->post())) {
          $postdata = Yii::$app->request->post();
          $void_cheque_branchcode = "";
          $sin = "";
          if ($postdata) {
          if (isset($postdata['StaffUser']) && $postdata['StaffUser']) {
          //$void_cheque_branchcode = $postdata['StaffUser']['void_cheque_branchcode'] . '-' . $postdata['StaffUser']['void_cheque_institutionnumber'] . '-' . $postdata['StaffUser']['void_cheque_accountnumber'];
          $void_cheque_branchcode = $staffmodel->void_cheque_branchcode . '-' . $staffmodel->void_cheque_institutionnumber . '-' . $staffmodel->void_cheque_accountnumber;
          if (isset($postdata['StaffUser']['sin'])) {
          $sin = $postdata['StaffUser']['sin'];
          }
          }
          }
          $restapiData = array();
          $restapiData['type'] = 'Staff';
          $restapiData['StaffUser[sin]'] = (!empty($sin)) ? Yii::$app->MyFunctions->numberencode($sin) : '';
          $restapiData['StaffUser[void_cheque_number]'] = $void_cheque_branchcode;
          $restapiData['StaffUser[void_cheque_photo]'] = Yii::$app->FrontFunctions->uploadedfiledata($staffmodel, 'void_cheque_photo');

          $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
          if ($output['status'] == 200) {
          Yii::$app->session->setFlash('success', Yii::t('app', 'Updated successfully.'));
          } else {
          Yii::$app->session->setFlash('error', Yii::t('app', 'Something went wrong. Please try again later.'));
          }
          return $this->redirect([$this->_ownerPath . 'dashboard']);
          } */
        $outputversion = Yii::$app->ApiCallFunctions->CheckVersion();
        $verisondata = array();
        if ($outputversion['status'] == 200) {
            if (isset($outputversion['data']) && $outputversion['data']) {
                $verisondata = $outputversion['data'];
            }
        }
        return $this->render('home', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'data' => $data,
                    'ownermodel' => $ownermodel,
                    'user' => $user,
                    'verisondata' => $verisondata,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Logout successfully.'));
        return $this->redirect([$this->_beforeownerPath]);
    }

    public function actionCompletedSlotReview() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $output = Yii::$app->ApiCallFunctions->OwnerGetHomeDetails();
        $data = array();
        if ($output['status'] == 200) {
            $data = $output['data'];
        }
        $outputversion = Yii::$app->ApiCallFunctions->CheckVersion();
        $verisondata = array();
        if ($outputversion['status'] == 200) {
            if (isset($outputversion['data']['alldata']) && $outputversion['data']['alldata']) {
                $verisondata = $outputversion['data']['alldata'];
            }
        }
        return $this->render('completedslotreview', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'user' => $this->_user,
                    'profiledata' => $this->_profiledata,
                    'data' => $data,
                    'verisondata' => $verisondata,
        ]);
    }

    public function actionIncreasehourlyrates() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $output = Yii::$app->ApiCallFunctions->OwnerGetHomeDetails();
        $data = array();
        if ($output['status'] == 200) {
            $data = $output['data'];
        }
        $outputversion = Yii::$app->ApiCallFunctions->CheckVersion();
        $verisondata = array();
        if ($outputversion['status'] == 200) {
            if (isset($outputversion['data']['increase_hourlyrates_data']) && $outputversion['data']['increase_hourlyrates_data']) {
                $verisondata = $outputversion['data']['increase_hourlyrates_data'];
            }
        }
        return $this->render('increasehourlyrates', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'user' => $this->_user,
                    'profiledata' => $this->_profiledata,
                    'data' => $data,
                    'verisondata' => $verisondata,
        ]);
    }

    public function actionProfile($loadcontent = "") {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_ownerPath . 'login']);
        }
        $user = $this->_user;
        if ($loadcontent) {
            return $this->renderPartial('profile', [
                        "_lang" => $this->_lang,
                        "_langID" => $this->_langID,
                        "_baseUrl" => $this->_baseUrl,
                        "_basePath" => $this->_basePath,
                        "_module" => $this->_module,
                        "_moduleUrl" => $this->_moduleUrl,
                        "_modulePath" => $this->_modulePath,
                        "_ownerUrl" => $this->_ownerUrl,
                        "_ownerPath" => $this->_ownerPath,
                        "_beforeownerUrl" => $this->_beforeownerUrl,
                        "_beforeownerPath" => $this->_beforeownerPath,
                        'user' => $this->_user,
                        'data' => $this->_profiledata,
                        'message' => $this->_profiledataMsg
            ]);
        } else {
            return $this->render('profile', [
                        "_lang" => $this->_lang,
                        "_langID" => $this->_langID,
                        "_baseUrl" => $this->_baseUrl,
                        "_basePath" => $this->_basePath,
                        "_module" => $this->_module,
                        "_moduleUrl" => $this->_moduleUrl,
                        "_modulePath" => $this->_modulePath,
                        "_ownerUrl" => $this->_ownerUrl,
                        "_ownerPath" => $this->_ownerPath,
                        "_beforeownerUrl" => $this->_beforeownerUrl,
                        "_beforeownerPath" => $this->_beforeownerPath,
                        'user' => $this->_user,
                        'data' => $this->_profiledata,
                        'message' => $this->_profiledataMsg
            ]);
        }
    }

    public function actionCreatePharmacy() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }

        $user = $this->_user;
        $ownermodel = new OwnerUser();
        $ownermodel->scenario = 'addowner';

        $manager = new OwnerManager();
        $manager->scenario = 'manager';

        $modelsManager = [$manager];
        $category = array();
        $restapiData = array();
        $restapiData['type'] = "Owner";
        $restapiData['module_id'] = $this->_module_id;
        $outputcat = Yii::$app->ApiCallFunctions->AppCategoryApi($restapiData);
        if ($outputcat['status'] == 200) {
            $category = ArrayHelper::map($outputcat['data'], 'category_id', 'name');
        }

        $province = array();
        $restapiData = array();
        $restapiData['type'] = "income";
        $outputprovince = Yii::$app->ApiCallFunctions->AppProvinceApi($restapiData);
        if ($outputprovince['status'] == 200) {
            $province = ArrayHelper::map($outputprovince['data'], 'province_id', 'name');
        }

        $required_languages = array();
        $restapiData = array();
        $restapiData['type'] = "income";
        $outputreqlang = Yii::$app->ApiCallFunctions->UserLanguages($restapiData);
        if ($outputreqlang['status'] == 200) {
            $required_languages = ArrayHelper::map($outputreqlang['data'], 'language_id', 'name');
        }
        //print_r($modelsManager);
        if ($ownermodel->load(Yii::$app->request->post())) {
         
            $postData = Yii::$app->request->post();
            if(isset($postData['OwnerUser']['covid']) && empty($postData['OwnerUser']['covid'])){
                $postData['OwnerUser']['covid'] = "No";
            }
            $image_url = UploadedFile::getInstance($ownermodel, 'image_url');
            if ($image_url) {
                //$postData['OwnerUser']['image_url'] = new \CURLFile($image_url->tempName, $image_url->type, $image_url->name);
                $postData['OwnerUser']['image_url'] = Yii::$app->FrontFunctions->uploadedfiledata($ownermodel, 'image_url', 'profileimage');
            }

            $user_language_array = array();
            if (isset($postData['OwnerUser']['required_languages']) && $postData['OwnerUser']['required_languages']) {
                foreach ($postData['OwnerUser']['required_languages'] as $value) {
                    $value_arr = explode('-', $value);
                    $relation_id = $value_arr[0];
                    $level_of_knowledge_id = $value_arr[1];
                    $level_of_knowledge = '';
                    if ($level_of_knowledge_id == 1) {
                        $level_of_knowledge = 'Beginner';
                    } else if ($level_of_knowledge_id == 2) {
                        $level_of_knowledge = 'Intermediary';
                    } else if ($level_of_knowledge_id == 3) {
                        $level_of_knowledge = 'Expert';
                    }

                    $user_language_array[] = array('relation_id' => $relation_id, 'level_of_knowledge' => $level_of_knowledge);
                }
            }
            $postData['OwnerUser']['required_languages'] = json_encode($user_language_array);

            $user_software_array = array();
            if (isset($postData['OwnerUser']['user_software']) && $postData['OwnerUser']['user_software']) {
                foreach ($postData['OwnerUser']['user_software'] as $value) {
                    $level_of_knowledge = 'Expert';
                    $user_software_array[] = array('relation_id' => $value, 'level_of_knowledge' => $level_of_knowledge);
                }
            }
            $postData['OwnerUser']['user_software'] = json_encode($user_software_array);

            $user_skill_array = array();
            if (isset($postData['OwnerUser']['user_skill']) && $postData['OwnerUser']['user_skill']) {
                foreach ($postData['OwnerUser']['user_skill'] as $value) {
                    $level_of_knowledge = 'Expert';
                    $user_skill_array[] = array('relation_id' => $value, 'level_of_knowledge' => $level_of_knowledge);
                }
            }
            $postData['OwnerUser']['user_skill'] = json_encode($user_skill_array);

            //*Category Id ( 5=>Pharmacist, 6=>Pharmacy Assistant (PA), 8=>Pharmacy Technician (PT) )
            $user_duty = array();
            if (isset($postData['OwnerUser']['pharmacy_duty'])) {
                $user_duty[] = array('category_id' => 5, 'duty_status' => $postData['OwnerUser']['pharmacy_duty'], 'display_order' => 1);
            }
            if (isset($postData['OwnerUser']['pharmacy_assistant'])) {
                $user_duty[] = array('category_id' => 6, 'duty_status' => $postData['OwnerUser']['pharmacy_assistant'], 'display_order' => 3);
            }
            if (isset($postData['OwnerUser']['pharmacy_technician'])) {
                $user_duty[] = array('category_id' => 8, 'duty_status' => $postData['OwnerUser']['pharmacy_technician'], 'display_order' => 2);
            }
            if ($user_duty) {
                $postData['OwnerUser']['user_duty'] = json_encode($user_duty);
            }

            $owner_manager_array = array();
            if (isset($postData['OwnerManager']) && $postData['OwnerManager']) {
                foreach ($postData['OwnerManager'] as $key => $value) {
                    if ($value) {
                        $first_name = $value['first_name'];
                        $last_name = $value['last_name'];
                        $phone_number = $value['phone_number'];
                        $owner_manager_array[] = array('first_name' => $first_name, 'last_name' => $last_name, 'phone_number' => $phone_number);
                    }
                }
            }
            $postData['OwnerUser']['manager'] = json_encode($owner_manager_array);


            $prefix = 'User';
            $restapiData = array();
            if (isset($postData[$prefix]) && $postData[$prefix]) {
                foreach ($postData[$prefix] as $key => $value) {
                    $restapiData[$prefix . '[' . $key . ']'] = $value;
                }
            }

            $stfprefix = 'OwnerUser';
            if (isset($postData[$stfprefix]) && $postData[$stfprefix]) {
                foreach ($postData[$stfprefix] as $key => $value) {
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    $restapiData[$stfprefix . '[' . $key . ']'] = $value;
                }
            }

            $restapiData['mode'] = "add";
            $outputprovince = Yii::$app->ApiCallFunctions->adddeleteowner($restapiData);
            if ($outputprovince['status'] == 200) {
                Yii::$app->session->setFlash('success', Yii::t('app', "Created successfully"));
                return $this->redirect([$this->_ownerPath . 'profile']);
            } else {
                Yii::$app->session->setFlash('error', $outputprovince['status'] . '! ' . $outputprovince['message']);
            }
            
        } else {
//             $errors = $ownermodel->errors;
//             echo "<pre>";
//             print_r($errors);
//             echo "</pre>";exit;
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $this->_user,
            'profiledata' => $this->_profiledata,
            'ownermodel' => $ownermodel,
            'modelsManager' => (empty($modelsManager)) ? [$manager] : $modelsManager,
            'category' => $category,
            'province' => $province,
            'required_languages' => $required_languages,
        );
        return $this->render('_pharmacy_create', $renderdata);
    }
    public function actionEditprofileform() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $title = "";
        $value = "";
        $owner_user_id = "";
        $data = array();
        $model = "";
        $message = "";
        if (isset($_REQUEST['owner_user_id']) && $_REQUEST['owner_user_id']) {
            $owner_user_id = $_REQUEST['owner_user_id'];
        }
        if (isset($_REQUEST['title']) && $_REQUEST['title']) {
            $title = $_REQUEST['title'];
        }
        $usermodel = Yii::$app->RocGetObject->UserObject($user,$this->_profiledata);
        $ownermodel = Yii::$app->RocGetObject->OwnerUserObject($user,$this->_profiledata,$owner_user_id);
        $ownermodel->scenario = 'updateowner';
        
        if ($title && $owner_user_id) {
            switch ($title) {
                case "name":
                    
                    break;
                case "neighbourhoods":
                    
                    break;
                case "business_legal_name":
                    
                    break;
                case "category_name":
                    $restapiData = array();
                    $restapiData['type'] = "Owner";
                    $restapiData['module_id'] = $this->_module_id;
                    $outputcat = Yii::$app->ApiCallFunctions->AppCategoryApi($restapiData);
                    if ($outputcat['status'] == 200) {
                        $data = ArrayHelper::map($outputcat['data'], 'category_id', 'name');
                    }
                    break;
                case "accountants_name":
                   
                    break;
                case "accountants_email":
                    
                    break;
                case "user_software":
                    $restapiData = array();
                    $restapiData['category_id'] = $ownermodel->category_id;
                    $restapiData['language_id'] = $this->_langID;
                    $outputuserSoftwares = Yii::$app->ApiCallFunctions->userSoftwares($restapiData);
                    if ($outputuserSoftwares['status'] == 200) {
                        $data = ArrayHelper::map($outputuserSoftwares['data'], 'software_id', 'name');
                    }
                    break;
                case "user_skill":
                    $restapiData = array();
                    $restapiData['category_id'] = $ownermodel->category_id;
                    $restapiData['language_id'] = $this->_langID;
                    $outputuserSkill = Yii::$app->ApiCallFunctions->UserSkill($restapiData);
                    if ($outputuserSkill['status'] == 200) {
                        $data = ArrayHelper::map($outputuserSkill['data'], 'skill_id', 'name');
                    }
                    break;
                case "weekday_traffic":
                    
                    break;
                case "weekend_traffic":
                    
                    break;
                case "province_name":
                    $restapiData = array();
                    $restapiData['type'] = "income";
                    $outputprovince = Yii::$app->ApiCallFunctions->AppProvinceApi($restapiData);
                    if ($outputprovince['status'] == 200) {
                        $data = ArrayHelper::map($outputprovince['data'], 'province_id', 'name');
                    }
                    break;
                case "owner_manager":
                    $model = Yii::$app->RocGetObject->OwnerManagerObject($user,$ownermodel->manager);
                    if($model){
                        $model = $model;
                    }else{
                        $manager = new OwnerManager();
                        $manager->scenario = 'manager';
                        $model = [$manager];
                    }
                    break;    
                case "required_languages":
                    $restapiData = array();
                    $restapiData['type'] = "income";
                    $outputreqlang = Yii::$app->ApiCallFunctions->UserLanguages($restapiData);
                    if ($outputreqlang['status'] == 200) {
                        $data = $outputreqlang['data'];
                    }
                    break;    
                case "user_duty":
                    $pharmacy_duty = Yii::$app->RocGetObject->DutyObject($user,$ownermodel->user_duty,'5');
                    $pharmacy_assistant_duty = Yii::$app->RocGetObject->DutyObject($user,$ownermodel->user_duty,'6');
                    $pharmacy_technician_duty = Yii::$app->RocGetObject->DutyObject($user,$ownermodel->user_duty,'8');
                    $ownermodel->pharmacy_duty = $pharmacy_duty->duty_status;
                    $ownermodel->pharmacy_technician = $pharmacy_technician_duty->duty_status;
                    $ownermodel->pharmacy_assistant = $pharmacy_assistant_duty->duty_status;
                    break;
                case "additional_information":
                    
                    break; 
                case "user_open_hour":
                    $data = Yii::$app->RocGetObject->OwnerUserOpenHoursObject($user,$ownermodel->user_open_hours);
                    $model = new OwnerUserOpenHours();
                    break;
                default:
                    echo Yii::t('app', 'No record(s) found.');
            }
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_formprofile', [
                            "_lang" => $this->_lang,
                            "_langID" => $this->_langID,
                            "_baseUrl" => $this->_baseUrl,
                            "_basePath" => $this->_basePath,
                            "_module" => $this->_module,
                            "_moduleUrl" => $this->_moduleUrl,
                            "_modulePath" => $this->_modulePath,
                            "_ownerUrl" => $this->_ownerUrl,
                            "_ownerPath" => $this->_ownerPath,
                            "_beforeownerUrl" => $this->_beforeownerUrl,
                            "_beforeownerPath" => $this->_beforeownerPath,
                            'user' => $this->_user,
                            'profiledata' => $this->_profiledata,
                            'usermodel' => $usermodel,
                            'ownermodel' => $ownermodel,
                            'model' => $model,
                            'title' => $title,
                            'value' => $value,
                            'owner_user_id' => $owner_user_id,
                            'data' => $data,
                            'message' => $message,
                ]);
            }
        } else {
            return false;
        }
    }
    public function actionAjaxprofileform() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $output = array();
        $data = array();
        $message = '';
        $type = '';
        $output['refreshpage'] = false;
        $owner_user_id = "";
        if (Yii::$app->request->post() && isset($_REQUEST['titlename']) && $_REQUEST['titlename']) {
            $postData = Yii::$app->request->post();
            $titlename = $postData['titlename'];
            if (isset($postData['User']) && $postData['User']) {
                $type = $postData['User']['type'];
            }
            if (isset($postData['owner_user_id']) && $postData['owner_user_id']) {
                $owner_user_id = Yii::$app->MyFunctions->decode($postData['owner_user_id']);
            }
            if($owner_user_id){
                if ($titlename == "name" || $titlename == "neighbourhoods" || $titlename == "address" || $titlename == "business_legal_name" ||
                        $titlename == "category_name" || $titlename == "accountants_name" || $titlename == "accountants_email" || $titlename == "weekday_traffic" || $titlename == "weekend_traffic"
                        || $titlename == "province_name" || $titlename == "additional_information" ) {
                    $restapiData = array();
                    $restapiData['type'] = $type;
                    $restapiData['owner_user_id'] = $owner_user_id;
                    $stfprefix = 'OwnerUser';
                    if (isset($postData[$stfprefix]) && $postData[$stfprefix]) {
                        foreach ($postData[$stfprefix] as $key => $value) {
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                            $restapiData[$stfprefix . '[' . $key . ']'] = $value;
                        }
                    }
                    $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
                    $message = $output['message'];
                    if ($output['status'] == 200) {
                        $output['refreshpage'] = true;
                        Yii::$app->session->setFlash('success', $message);
                    }else{
                        Yii::$app->session->setFlash('error', $message);
                    }
                }else if ($titlename == "user_software") {
                    $user_software_array = array();
                    if (isset($postData['OwnerUser']['user_software']) && $postData['OwnerUser']['user_software']) {
                        foreach ($postData['OwnerUser']['user_software'] as $value) {
                            $level_of_knowledge = 'Expert';
                            $user_software_array[] = array('relation_id' => $value, 'level_of_knowledge' => $level_of_knowledge);
                        }
                    }
                    if($user_software_array){
                        $restapiData = array();
                        $restapiData['data_type'] = "user_software";
                        $restapiData['data_value'] = json_encode($user_software_array);
                        $restapiData['owner_user_id'] = $owner_user_id;
                        $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                        $message = $output['message'];
                        if ($output['status'] == 200) {
                            $output['refreshpage'] = true;
                            Yii::$app->session->setFlash('success', $message);
                        }else{
                            Yii::$app->session->setFlash('error', $message);
                        }
                    }
                }else if ($titlename == "user_skill") {
                    $user_skill_array = array();
                    if (isset($postData['OwnerUser']['user_skill']) && $postData['OwnerUser']['user_skill']) {
                        foreach ($postData['OwnerUser']['user_skill'] as $value) {
                            $level_of_knowledge = 'Expert';
                            $user_skill_array[] = array('relation_id' => $value, 'level_of_knowledge' => $level_of_knowledge);
                        }
                    }
                    if($user_skill_array){
                        $restapiData = array();
                        $restapiData['data_type'] = "user_skill";
                        $restapiData['data_value'] = json_encode($user_skill_array);
                        $restapiData['owner_user_id'] = $owner_user_id;
                        $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                        $message = $output['message'];
                        if ($output['status'] == 200) {
                            $output['refreshpage'] = true;
                            Yii::$app->session->setFlash('success', $message);
                        }else{
                            Yii::$app->session->setFlash('error', $message);
                        }
                    }
                }else if ($titlename == "owner_manager") {
                    if (isset($_REQUEST['OwnerManager']) && $_REQUEST['OwnerManager']) {
                        $OwnerManagerUpdate = json_encode($_REQUEST['OwnerManager']);
                        $restapiData = array();
                        $restapiData['data_type'] = "manager";
                        $restapiData['data_value'] = $OwnerManagerUpdate;
                        $restapiData['owner_user_id'] = $owner_user_id;
                       
                        $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                        $message = $output['message'];
                        if ($output['status'] == 200) {
                            $output['loadcontent'] = true;
                            $message = Yii::t('app', "Updated successfully");
                            Yii::$app->session->setFlash('success', $message);
                        } else {
                            Yii::$app->session->setFlash('error', $message);
                        }
                    }
                }else if ($titlename == "required_languages") {
                    $user_language_array = array();
                    foreach ($postData['OwnerUser']['required_languages'] as $value) {
                        $value_arr = explode('-', $value);
                        $relation_id = $value_arr[0];
                        $level_of_knowledge_id = $value_arr[1];
                        $level_of_knowledge = '';
                        if ($level_of_knowledge_id == 1) {
                            $level_of_knowledge = 'Beginner';
                        } else if ($level_of_knowledge_id == 2) {
                            $level_of_knowledge = 'Intermediary';
                        } else if ($level_of_knowledge_id == 3) {
                            $level_of_knowledge = 'Expert';
                        }
                        $user_language_array[] = array('relation_id' => $relation_id, 'level_of_knowledge' => $level_of_knowledge);
                    }
                    if($user_language_array){
                        $restapiData = array();
                        $restapiData['data_type'] = "required_languages";
                        $restapiData['data_value'] = json_encode($user_language_array);
                        $restapiData['owner_user_id'] = $owner_user_id;
                        $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                        $message = $output['message'];
                        if ($output['status'] == 200) {
                            $output['loadcontent'] = true;
                            $message = Yii::t('app', "Updated successfully");
                            Yii::$app->session->setFlash('success', $message);
                        } else {
                            Yii::$app->session->setFlash('error', $message);
                        }
                    }
                }else if ($titlename == "user_open_hour") {
                    $user_open_hour_array = array();
                    if (isset($postData['OwnerUserOpenHours']['day']) && $postData['OwnerUserOpenHours']['day']) {
                        $openhour_day_keys = array_keys($postData['OwnerUserOpenHours']['day']);

                        if($openhour_day_keys){
                            for($i=0; $i<count($openhour_day_keys); $i++){
                                $user_open_hour_data['day'] = "$i";
                                $user_open_hour_data['open_time'] = (isset($postData['OwnerUserOpenHours']['open_time'][$openhour_day_keys[$i]]))?$postData['OwnerUserOpenHours']['open_time'][$openhour_day_keys[$i]]:"08:00";
                                $user_open_hour_data['close_time'] = (isset($postData['OwnerUserOpenHours']['close_time'][$openhour_day_keys[$i]]))?$postData['OwnerUserOpenHours']['close_time'][$openhour_day_keys[$i]]:"17:00";
                                $user_open_hour_data['is_close'] = (isset($postData['OwnerUserOpenHours']['is_close'][$openhour_day_keys[$i]]))?$postData['OwnerUserOpenHours']['is_close'][$openhour_day_keys[$i]]:"No";

                                $user_open_hour_array[] = $user_open_hour_data;
                            }
                        }
                    }else{
                        $openhour_day_keys = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                        if($openhour_day_keys){
                            for($i=0; $i<count($openhour_day_keys); $i++){
                                $user_open_hour_data['day'] = "$i";
                                $user_open_hour_data['open_time'] = "08:00";
                                $user_open_hour_data['close_time'] = "17:00";
                                $user_open_hour_data['is_close'] = "No";
                                $user_open_hour_array[] = $user_open_hour_data;
                            }
                        }
                    }
                    if($user_open_hour_array){
                        $restapiData = array();
                        $restapiData['data_type'] = "user_open_hours";
                        $restapiData['data_value'] = json_encode($user_open_hour_array);
                        $restapiData['owner_user_id'] = $owner_user_id;
                        $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                        $message = $output['message'];
                        if ($output['status'] == 200) {
                            $output['loadcontent'] = true;
                            $message = Yii::t('app', "Updated successfully");
                            Yii::$app->session->setFlash('success', $message);
                        } else {
                            Yii::$app->session->setFlash('error', $message);
                        }
                    }
                }else if ($titlename == "user_duty") {
                    $user_duty = array();
                    if (isset($postData['OwnerUser']['pharmacy_duty'])) {
                        $user_duty[] = array('category_id' => 5, 'duty_status' => $postData['OwnerUser']['pharmacy_duty'], 'display_order' => 1);
                    }
                    if (isset($postData['OwnerUser']['pharmacy_assistant'])) {
                        $user_duty[] = array('category_id' => 6, 'duty_status' => $postData['OwnerUser']['pharmacy_assistant'], 'display_order' => 3);
                    }
                    if (isset($postData['OwnerUser']['pharmacy_technician'])) {
                        $user_duty[] = array('category_id' => 8, 'duty_status' => $postData['OwnerUser']['pharmacy_technician'], 'display_order' => 2);
                    }
                    if ($user_duty) {
                        $restapiData = array();
                        $restapiData['data_type'] = "user_duty";
                        $restapiData['data_value'] = json_encode($user_duty);
                        $restapiData['owner_user_id'] = $owner_user_id;
                        $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                        
                        $message = $output['message'];
                        if ($output['status'] == 200) {
                            $output['refreshpage'] = true;
                            Yii::$app->session->setFlash('success', $message);
                        }else{
                            Yii::$app->session->setFlash('error', $message);
                        }
                    }else{
                        Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
                    }
                }else{
                    Yii::$app->session->setFlash('error', Yii::t('app', 'title name invalid'));
                }
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
            }
            return $this->redirect([$this->_ownerPath . 'view-pharmacy','id'=>Yii::$app->MyFunctions->encode($owner_user_id)]);
//            if (Yii::$app->request->isAjax) {
//                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//                return $output;
//            }
        }
        return false;
    }

    public function actionViewPharmacy($id = "", $loadcontent = "") {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_ownerPath . 'login']);
        }

        $user = $this->_user;
        $owner_user_id = Yii::$app->MyFunctions->decode($id);
        //$ownermodel = Yii::$app->RocGetObject->OwnerUserObject($user,$this->_profiledata);echo "<pre>";print_r($ownermodel);exit;
        $outputowner = array();
        if ($owner_user_id) {
            $restapiData = array();
            $restapiData['owner_user_id'] = $owner_user_id;
            $profileoutputowner = Yii::$app->ApiCallFunctions->GetProfileApi($restapiData);
            $outputowner = $profileoutputowner['message'];
            if ($profileoutputowner['status'] == 200) {
                $outputowner = $profileoutputowner['data'];
            }
        }
        $model = new Owneruser();
        if ($owner_user_id && Yii::$app->request->post()) {
            $postData = Yii::$app->request->post();
            $restapiData = array();
            $restapiData['type'] = 'Owner';
            $restapiData['owner_user_id'] = $owner_user_id;
            $restapiData['OwnerUser[image_url]'] = Yii::$app->FrontFunctions->uploadedfiledata($model, 'image_url', 'profileimage');
            $checkFileExt = Yii::$app->FrontFunctions->checkfile($restapiData['OwnerUser[image_url]'], 'image');
            if ($checkFileExt && empty($checkFileExt['result'])) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Only files with these extensions are allowed:') . " " . implode(", ", $checkFileExt['typeArrayExt']));
                return $this->redirect(Yii::$app->request->referrer);
                exit;
            }
            $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                $output['refreshpage'] = true;
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', $message);
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'data' => $outputowner,
        );
        if ($loadcontent) {
            return $this->renderPartial('_pharmacy_view', $renderdata);
        } else {
            return $this->render('_pharmacy_view', $renderdata);
        }
    }

    public function actionPharmacyValidateAjax($id) {
        $id = Yii::$app->MyFunctions->decode($id);
        $model = OwnerUser::find()->where(['owner_user_id' => $id])->one();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionAboutus() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $user = $this->_user;
        $restapiData['staticpage_id'] = 1;
        $restapiData['module_id'] = $this->_module_id;
        $output = Yii::$app->ApiCallFunctions->AppStaticpageApi($restapiData);
        $data = array();
        if ($output['status'] == 200) {
            $data = $output['data'];
        }
        return $this->render('aboutus', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'user' => $this->_user,
                    'data' => $data
        ]);
    }

    public function actionHistory() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $user = $this->_user;
        $output = Yii::$app->ApiCallFunctions->OwnerGetPreviousSlotHistory();
        $data = array();
        $message = $output['message'];
        if ($output['status'] == 200) {
            $data = $output['data'];
        }
        return $this->render('history', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'user' => $this->_user,
                    'profiledata' => $this->_profiledata,
                    'data' => $data,
                    'message' => $message,
        ]);
    }

    public function actionSoftware() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $out = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            //$list = Account::find()->andWhere(['parent' => $id])->asArray()->all();
            $outputdata = array();
            $restapiData['category_id'] = $id;
            $restapiData['language_id'] = $this->_langID;
            $outputuserSoftwares = Yii::$app->ApiCallFunctions->userSoftwares($restapiData);
            if ($outputuserSoftwares['status'] == 200) {
                $outputdata = ArrayHelper::map($outputuserSoftwares['data'], 'software_id', 'name');
            }
            $selected = null;
            if ($id != null && count($outputdata) > 0) {
                $selected = '';
                foreach ($outputdata as $key => $value) {
                    $out[] = ['id' => $key, 'name' => $value];
//                    if ($i == 0) {
//                        $selected = $account['id'];
//                    }
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSkill($email = "") {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $out = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            //$list = Account::find()->andWhere(['parent' => $id])->asArray()->all();
            $outputdata = array();
            $restapiData['category_id'] = $id;
            $restapiData['language_id'] = $this->_langID;
            $outputuserSkill = Yii::$app->ApiCallFunctions->UserSkill($restapiData);
            if ($outputuserSkill['status'] == 200) {
                $outputdata = ArrayHelper::map($outputuserSkill['data'], 'skill_id', 'name');
            }
            $selected = null;
            if ($id != null && count($outputdata) > 0) {
                $selected = '';
                foreach ($outputdata as $key => $value) {
                    $out[] = ['id' => $key, 'name' => $value];
//                    if ($i == 0) {
//                        $selected = $account['id'];
//                    }
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionPromocode() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $user = $this->_user;
        
        return $this->render('promocode', [
                "_lang" => $this->_lang,
                "_langID" => $this->_langID,
                "_baseUrl" => $this->_baseUrl,
                "_basePath" => $this->_basePath,
                "_module" => $this->_module,
                "_moduleUrl" => $this->_moduleUrl,
                "_modulePath" => $this->_modulePath,
                "_ownerUrl" => $this->_ownerUrl,
                "_ownerPath" => $this->_ownerPath,
                "_beforeownerUrl" => $this->_beforeownerUrl,
                "_beforeownerPath" => $this->_beforeownerPath,
                'user' => $this->_user,
                'profiledata' => $this->_profiledata,
            ]);
    }
    public function actionPromoCodeView() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $outputhistory = Yii::$app->ApiCallFunctions->Promocodehistory();
        
        $historydata = array();
        if($outputhistory['status'] == 200){
            if(isset($outputhistory['data'])){
                $historydata = $outputhistory['data'];
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_promo_code_view', [
                        "_lang" => $this->_lang,
                        "_langID" => $this->_langID,
                        "_baseUrl" => $this->_baseUrl,
                        "_basePath" => $this->_basePath,
                        "_module" => $this->_module,
                        "_moduleUrl" => $this->_moduleUrl,
                        "_modulePath" => $this->_modulePath,
                        "_ownerUrl" => $this->_ownerUrl,
                        "_ownerPath" => $this->_ownerPath,
                        "_beforeownerUrl" => $this->_beforeownerUrl,
                        "_beforeownerPath" => $this->_beforeownerPath,
                        'user' => $this->_user,
                        'profiledata' => $this->_profiledata,
                        'history' => $historydata,
            ]);
        }
    }
    
    public function actionAddpromocode() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        
        $model = new Promocode();
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_promo_code_form', [
                        "_lang" => $this->_lang,
                        "_langID" => $this->_langID,
                        "_baseUrl" => $this->_baseUrl,
                        "_basePath" => $this->_basePath,
                        "_module" => $this->_module,
                        "_moduleUrl" => $this->_moduleUrl,
                        "_modulePath" => $this->_modulePath,
                        "_ownerUrl" => $this->_ownerUrl,
                        "_ownerPath" => $this->_ownerPath,
                        "_beforeownerUrl" => $this->_beforeownerUrl,
                        "_beforeownerPath" => $this->_beforeownerPath,
                        'user' => $this->_user,
                        'profiledata' => $this->_profiledata,
                        'model' => $model,
            ]);
        }
    }
    
    public function actionAjaxApplyPromoCode($email = "") {
        if (Yii::$app->user->isGuest) {
            return false;
        } 
        $user = $this->_user;
        $model = new Promocode();
        if($model->load(Yii::$app->request->post())){
            $restapiData = array();
            $restapiData['promocode'] = $model->promocode;
            $output = Yii::$app->ApiCallFunctions->ApplyPromocode($restapiData);
            $message ="";
            if(isset($output['message']) && $output['message']){
                $message = $output['message'];
            }
             if($output['status'] == 200){
                Yii::$app->session->setFlash('success', $message);
            }else{
                Yii::$app->session->setFlash('error', $message);
            }
            //return $this->redirect([$this->_ownerPath.'promocode']);
            $output['message'] = $message;
        } else {
            $output['message'] = Yii::t('app', 'something went wrong');
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $output;
        }
    }
    
    public function actionNotification() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $unreadCount = 0;
        $readCount = 0;
        $generalCount = 0;
        $unreadData = array();
        $readData = array();
        $generalData = array();

        $user = $this->_user;

        $output = Yii::$app->ApiCallFunctions->getNotificationList();
        if ($output['status'] == 200) {
            $data = $output['data'];
            $unreadCount = count($data['unread']);
            $readCount = count($data['read']);
            $generalCount = count($data['general']);

            $unreadData = $data['unread'];
            $readData = $data['read'];
            $generalData = $data['general'];
        }
        
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $this->_user,
            'profiledata' => $this->_profiledata,
            'unreadData' => $unreadData,
            'readData' => $readData,
            'generalData' => $generalData,
            'unreadCount' => $unreadCount,
            'readCount' => $readCount,
            'generalCount' => $generalCount
        );
        return $this->render('notification', $renderdata);
    }

    public function actionNotificationajax() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;

        $output = array();
        $data = array();
        $message = '';
        $output['refreshpage'] = true;
        $notification = '<div class="alert alert-danger alert-dismissible">
                            <h5><i class="icon fas fa-ban"></i> ' . Yii::t('app', 'Alert.') . '!</h5>
                            ' . Yii::t('app', 'List not available.') . '      
                        </div>';

        if (isset($_REQUEST['type']) && $_REQUEST['type']) {
            $type = $_REQUEST['type'];

            $output = Yii::$app->ApiCallFunctions->getNotificationList($restapiData);
            if ($output['status'] == 200) {
                $data = $output['data'];
                $unreadCount = count($data['unread']);
                $readCount = count($data['read']);
                $generalCount = count($data['general']);

                $unreadData = $data['unread'];
                $readData = $data['read'];
                $generalData = $data['general'];

                if ($type == "unread") {
                    $notification = $this->renderPartial('_notification_unread', ['user' => $user, 'data' => $unreadData, 'unreadCount' => $unreadCount, 'readCount' => $readCount, 'generalCount' => $generalCount]);
                } elseif ($type == "read") {
                    $notification = $this->renderPartial('_notification_read', ['user' => $user, 'data' => $readData, 'unreadCount' => $unreadCount, 'readCount' => $readCount, 'generalCount' => $generalCount]);
                } elseif ($type == "general") {
                    $notification = $this->renderPartial('_notification_general', ['user' => $user, 'data' => $generalData, 'unreadCount' => $unreadCount, 'readCount' => $readCount, 'generalCount' => $generalCount]);
                }
            }
        }
        return $notification;
    }
    
    public function actionPayment() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }

        $user = $this->_user;
        $outputdata = array();
        $output = Yii::$app->ApiCallFunctions->OwnerGetPaymentSlotList();
        if ($output['status'] == 200) {
            $outputdata = $output['data'];
        }
        $allownercarddata = array();
        $output = Yii::$app->ApiCallFunctions->ownerGetAllStripeCardList();
        if ($output['status'] == 200) {
            if(isset($output['data']) && $output['data']){
                $allownercarddata = $output['data'];
            }
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'data' => $outputdata,
            'allownercarddata' => $allownercarddata,
        );
        return $this->render('payment', $renderdata);
    }
    
    public function actionCard($partoftype = "") {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }

        $user = $this->_user;
        
        $model = new OwnerCard();
        if($partoftype == "deletecard"){
            if(isset($_REQUEST['cid']) && isset($_REQUEST['owneruser']) && $_REQUEST['cid'] && $_REQUEST['owneruser']){
                $restapiData = array();
                $restapiData['owner_user_id'] = Yii::$app->MyFunctions->decode($_REQUEST['owneruser']);
                $restapiData['card_id'] = Yii::$app->MyFunctions->decode($_REQUEST['cid']);
                $output = Yii::$app->ApiCallFunctions->ownerDeleteStripeCard($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                }else{
                    Yii::$app->session->setFlash('error', $output['message']);
                }
            }else{
                $output['message'] = Yii::t('app', 'something went wrong');
                Yii::$app->session->setFlash('error', $output['message']);
            }
            return $this->redirect([$this->_ownerPath . 'card']);
        }
            
        $allownercarddata = array();
        $output = Yii::$app->ApiCallFunctions->ownerGetAllStripeCardList();
        if ($output['status'] == 200) {
            if(isset($output['data']) && $output['data']){
                $allownercarddata = $output['data'];
            }
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'model' => $model,
            'allownercarddata' => $allownercarddata,
        );
        return $this->render('card', $renderdata);
    }
    
    public function actionCardform() {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $user = $this->_user;
        
        $allownercarddata = array();
        $output = Yii::$app->ApiCallFunctions->ownerGetAllStripeCardList();
        if ($output['status'] == 200) {
            if(isset($output['data']) && $output['data']){
                $allownercarddata = $output['data'];
            }
        }
        $model = new OwnerCard();
        if($model->load(Yii::$app->request->post())){
            //echo "<pre>";print_r($model);exit;
            if ($model->validate()) {
                $restapiData = array();
                $restapiData['OwnerCard[number]'] = $model->number;
                $restapiData['OwnerCard[exp_month]'] = $model->exp_month;
                $restapiData['OwnerCard[exp_year]'] = $model->exp_year;
                $restapiData['OwnerCard[cvc]'] = $model->cvc;
                $restapiData['OwnerCard[card_name]'] = $model->card_name;
                $restapiData['OwnerCard[owner_user_id]'] = $model->owner_user_id;
                $output = Yii::$app->ApiCallFunctions->ownerAddStripeCard($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                }else{
                    Yii::$app->session->setFlash('error', $output['message']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
            }
            return $this->redirect([$this->_ownerPath . 'card']);
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'model' => $model,
            'allownercarddata' => $allownercarddata,
        );
        return $this->renderAjax('_card_form', $renderdata);
    }
    
    public function actionLocum($partoftype = "") {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }

        $user = $this->_user;
        $favourite_staffdata = array();
        $block_staffdata = array();
        $outputlist = Yii::$app->ApiCallFunctions->OwnerGetFavouriteBlockStaffList();
        if ($outputlist['status'] == 200) {
            if(isset($outputlist['favourite'])){
                $favourite_staffdata = $outputlist['favourite'];
            }
            if(isset($outputlist['block'])){
                $block_staffdata = $outputlist['block'];
            }
        }
        //echo "<pre>"; print_r($favourite_staffdata);exit;
        if($partoftype == "deletefavouriteblock"){
            $restapiData = array();
            if(isset($_REQUEST['favouriteblock_id']) && $_REQUEST['favouriteblock_id']){
                $restapiData['favouriteblock_id'] = Yii::$app->MyFunctions->decode($_REQUEST['favouriteblock_id']);
                $output = Yii::$app->ApiCallFunctions->OwnerDeleteFavouriteBlockStaff($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                }else{
                    Yii::$app->session->setFlash('error', $output['message']);
                }
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
            }
            return $this->redirect([$this->_ownerPath . 'locum']);
            
        }elseif($partoftype == "Favouritebyowner"){
            $restapiData = array();
            if(isset($_REQUEST['staff_user_id']) && $_REQUEST['staff_user_id']){
                $restapiData['staff_user_id'] = Yii::$app->MyFunctions->decode($_REQUEST['staff_user_id']);
                $restapiData['type'] = $partoftype;
                $output = Yii::$app->ApiCallFunctions->OwnerAddFavouriteBlockStaff($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                }else{
                    Yii::$app->session->setFlash('error', $output['message']);
                }
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
            }
            return $this->redirect([$this->_ownerPath . 'locum']);
        
            
        }elseif($partoftype == "Blockbyowner"){
            $restapiData = array();
            if(isset($_REQUEST['staff_user_id']) && $_REQUEST['staff_user_id']){
                $restapiData['staff_user_id'] = Yii::$app->MyFunctions->decode($_REQUEST['staff_user_id']);
                $restapiData['type'] = $partoftype;
                $output = Yii::$app->ApiCallFunctions->OwnerAddFavouriteBlockStaff($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                }else{
                    Yii::$app->session->setFlash('error', $output['message']);
                }
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
            }
            return $this->redirect([$this->_ownerPath . 'locum']);
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'favourite_staffdata' => $favourite_staffdata,
            'block_staffdata' => $block_staffdata,
        );
        return $this->render('locum', $renderdata);
    }
    public function actionReviewForm(){
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $user = $this->_user;
        $slotdata = array();
        $slot_id = "";
        $current_url = $this->_ownerPath . 'slots';
        if(isset($_REQUEST['current_url']) && $_REQUEST['current_url']){
            $current_url = $_REQUEST['current_url'];
        }
        if(isset($_REQUEST['slot_id']) && $_REQUEST['slot_id']){
            $slot_id = $_REQUEST['slot_id'];
        }
        $restapiData = array();
        $restapiData['slot_id'] = $slot_id;
        $output = Yii::$app->ApiCallFunctions->getSingleSlotsDetails($restapiData);
        $message = $output['message'];
        if ($output['status'] == 200) {
            $slotdata = $output['data'];
            $message = $output['message'];
        }
        $model = new Review();
        $model->slot_id = $slot_id;
        if($model->load(Yii::$app->request->post())){
            //echo "<pre>";print_r($model);exit;
            if ($model->validate()) {
                $restapiData = array();
                $restapiData['slot_id'] = $model->slot_id;
                $restapiData['rate_skill'] = $model->rate_skill;
                $restapiData['rate_attire'] = $model->rate_attire;
                $restapiData['rate_punctuality'] = $model->rate_punctuality;
                $restapiData['additional_notes'] = $model->additional_notes;
                $output = Yii::$app->ApiCallFunctions->OwnerGiveStaffSlotReview($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                }else{
                    Yii::$app->session->setFlash('error', $output['message']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
            }
            return $this->redirect([$current_url]);
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'model' => $model,
            'slotdata' => $slotdata,
        );
        return $this->renderAjax('_review_form', $renderdata);
    }
    public function actionIncreaseHourlyForm(){
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $user = $this->_user;
        $slotdata = array();
        $jsonslotdata = array();
        $slot_id = "";
        $current_url = $this->_ownerPath . 'slots';
        if(isset($_REQUEST['current_url']) && $_REQUEST['current_url']){
            $current_url = $_REQUEST['current_url'];
        }
        if(isset($_REQUEST['slot_id']) && $_REQUEST['slot_id']){
            $slot_id = $_REQUEST['slot_id'];
        }
        if(isset($_REQUEST['slotdata']) && $_REQUEST['slotdata']){
            $jsonslotdata = json_decode($_REQUEST['slotdata'],true);
        }
        $restapiData = array();
        $restapiData['slot_id'] = $slot_id;
        $output = Yii::$app->ApiCallFunctions->getSingleSlotsDetails($restapiData);
        $message = $output['message'];
        if ($output['status'] == 200) {
            $slotdata = $output['data'];
            $message = $output['message'];
        }
        
        $model = new IncreaseHourlyRateform();
        $model->slot_id = $slot_id;
        if($model->load(Yii::$app->request->post())){
            //echo "<pre>";print_r($model);exit;
            if ($model->validate()) {
                $restapiData = array();
                $restapiData['slot_id'] = $model->slot_id;
                $restapiData['owner_hour_price'] = ($model->hour_price+$model->incentive);
                $output = Yii::$app->ApiCallFunctions->Increasehourlyrates($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                }else{
                    Yii::$app->session->setFlash('error', $output['message']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'something went wrong'));
            }
            return $this->redirect([$this->_ownerPath.'increasehourlyrates']);
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'model' => $model,
            'slotdata' => $slotdata,
            'jsonslotdata' => $jsonslotdata,
        );
        return $this->renderAjax('_increase_hourly_form', $renderdata);
    }
    public function actionLocumprofile($locum_id=""){
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        if(empty($locum_id)){
            Yii::$app->session->setFlash('error', Yii::t('app', 'Locum not available'));
            return $this->redirect([$this->_ownerPath . 'locum']);
        }
        $staff_user_id = Yii::$app->MyFunctions->decode($locum_id);
        
        $user = $this->_user;
        $staffdata = array();
        $restapiData = array();
        $restapiData['staff_user_id'] = $staff_user_id;
        $output = Yii::$app->ApiCallFunctions->GetProfileApi($restapiData);
        $message = $output['message'];
        if ($output['status'] == 200) {
            if(isset($output['data']) && $output['data']){
                $staffdata = $output['data'];
            }
        }
        $Documentsmodel = new Documents();
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $user,
            'profiledata' => $this->_profiledata,
            'staffdata' => $staffdata,
            'Documentsmodel' => $Documentsmodel,
        );
        return $this->render('locumprofile', $renderdata);
    }
    
    public function actionSettings($loadcontent = "") {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $user = $this->_user;
        
        $allownercarddata = array();
        $output = Yii::$app->ApiCallFunctions->ownerGetAllStripeCardList();
        if ($output['status'] == 200) {
            if(isset($output['data']) && $output['data']){
                $allownercarddata = $output['data'];
            }
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
            'user' => $this->_user,
            'profiledata' => $this->_profiledata,
            'allownercarddata' => $allownercarddata,
        );
        if ($loadcontent) {
            return $this->renderPartial('settings', $renderdata);
        } else {
            return $this->render('settings', $renderdata);
        }
    }

    public function actionSettingssave($partoftype = "") {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $output['refreshpage'] = false;
        $preferred_lang_name = "English";
        if ($partoftype == "preferred_lang") {  
            $restapiData = array();
            $restapiData['type'] = 'User';
            $restapiData['User[preferred_language_id]'] = "1";
            if (isset($_REQUEST['preferred_lang_id']) && $_REQUEST['preferred_lang_id']) {
                $restapiData['User[preferred_language_id]'] = $_REQUEST['preferred_lang_id'];
            }
            if (isset($_REQUEST['preferred_lang_name']) && $_REQUEST['preferred_lang_name']) {
                $preferred_lang_name = $_REQUEST['preferred_lang_name'];
            }
            $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
            $output['message'] = $output['message'];
            if ($output['status'] == 200) {
                $output['refreshpage'] = true;
                $output['preferred_lang_name'] = $preferred_lang_name;
            }
        }else {
            $output['message'] = Yii::t('app', 'something went wrong');
        }
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $output;
        }
    }
}
