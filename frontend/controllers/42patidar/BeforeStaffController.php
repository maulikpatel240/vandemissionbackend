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
use frontend\models\AppModules;
use frontend\models\UserLoginForm;
use frontend\models\UserResponse;
use frontend\models\roc\StaffUser;
use frontend\models\roc\User;
use frontend\models\roc\StaffWorkExperience;
use frontend\models\UserQuestions;
use frontend\models\UserQuestionsAnswer;
use frontend\models\UserQuestionsOptions;
use frontend\models\ForgotPassword;

/**
 * Staff controller
 */
class BeforeStaffController extends Controller {

    public $_module_id = 2;
    public $_baseUrl = "/";
    public $_basePath = "/";
    public $_lang = "en";
    public $_langID = 1;
    public $_module = array();
    public $_moduleUrl = "/";
    public $_modulePath = "/";
    public $_staffUrl = "/";
    public $_staffPath = "/";
    public $_beforestaffUrl = "/";
    public $_beforestaffPath = "/";
    public $_user = array();

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function init() {
        parent::init();
        $this->_baseUrl = Url::base(true) . '/';
        $this->_basePath = Url::base() . '/';
        $this->_lang = Yii::$app->FrontFunctions->defaultlanguage();
        $this->_langID = Yii::$app->FrontFunctions->defaultlanguage(true);
        $this->_module = Yii::$app->FrontFunctions->AppModules($this->_module_id);
        $this->_moduleUrl = $this->_baseUrl . $this->_module['unique_name'] . '/';
        $this->_modulePath = $this->_basePath . $this->_module['unique_name'] . '/';
        $this->_staffUrl = $this->_baseUrl . $this->_module['unique_name'] . '/staff/';
        $this->_staffPath = '/' . $this->_module['unique_name'] . '/staff/';
        $this->_beforestaffUrl = $this->_baseUrl . $this->_module['unique_name'] . '/before-staff/';
        $this->_beforestaffPath = '/' . $this->_module['unique_name'] . '/before-staff/';

        if (!empty(Yii::$app->user->identity)) {
            $this->_user = Yii::$app->user->identity;
            $message = "";
            if (Yii::$app->user->identity && isset(Yii::$app->user->identity->message)) {
                $message = Yii::$app->user->identity->message;
            }
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect([$this->_beforestaffPath]);
        }
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }
    }

    public function beforeAction($action) {
//        if ($this->action->id == 'slotapply') {
//            $this->enableCsrfValidation = false;
//        }
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
    public function actionIndex($email = "") {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';
        $usermodel = new User();
        $usermodel->scenario = 'emailrequired';
        $model = new UserLoginForm();
        $model->module_id = $this->_module_id;
        $model->type = 'Staff';
        $model->language_id = $this->_langID;
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect([$this->_staffPath]);
        } else {
            $model->password = '';
//            $errors = $model->getErrors();
//            if ($errors) {
//                print_r($errors);
//                exit;
//            }
        }
        return $this->render('login', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'model' => $model,
                    'usermodel' => $usermodel,
        ]);
    }

    public function actionLogin($email = "") {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }
        return $this->redirect([$this->_beforestaffPath]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionForgotPassword() {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';
        $model = new ForgotPassword();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $restapiData = array();
            $restapiData['type'] = 'Staff';
            $restapiData['email'] = $model->email;
            $restapiData['module_id'] = $this->_module_id;
            $output = Yii::$app->ApiCallFunctions->ForgotPassword($restapiData);
            $message = $output['message'];
            if (isset($output['status']) && $output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect([$this->_beforestaffPath]);
            } else {
                Yii::$app->session->setFlash('error', $message);
                return $this->redirect([$this->_beforestaffPath . 'forgotPassword']);
            }
        }
        return $this->render('forgotpassword', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'model' => $model,
        ]);
    }

    public function actionResetPassword($token) {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }
        $renderdata = array(
            "_lang" => $this->_lang,
            "_langID" => $this->_langID,
            "_baseUrl" => $this->_baseUrl,
            "_basePath" => $this->_basePath,
            "_module" => $this->_module,
            "_moduleUrl" => $this->_moduleUrl,
            "_modulePath" => $this->_modulePath,
            "_staffUrl" => $this->_staffUrl,
            "_staffPath" => $this->_staffPath,
            "_beforestaffUrl" => $this->_beforestaffUrl,
            "_beforestaffPath" => $this->_beforestaffPath,
            'model' => $model,
        );
        return $this->render('resetPassword', $renderdata);
    }

    public function actionCheckEmail() {
        $usermodel = new User();
        $usermodel->scenario = 'emailrequired';
        if ($usermodel->load(Yii::$app->request->post())) {
            $restapiData = array();
            $restapiData['User[module_id]'] = $this->_module_id;
            $restapiData['User[type]'] = 'Staff';
            $restapiData['User[email]'] = $usermodel->email;
            $restapiData['language_id'] = $this->_langID;
            $output = Yii::$app->ApiCallFunctions->EmailCheck($restapiData);
            if (isset($output['status']) && $output['status'] == 200) {
                $step_completed = (isset($output['data'][0]['step_completed'])) ? $output['data'][0]['step_completed'] : 0;
                $step_completed = str_replace('step-', '', $step_completed);
                $urlNumber = $step_completed == '0' ? '' : $step_completed + 1;
                return $this->redirect([$this->_beforestaffPath . 'signup' . $urlNumber, 'email' => $usermodel->email]);
            } else {
                $message = ($output && $output['message']) ? $output['message'] : 'something went wrong';
                Yii::$app->session->setFlash('error', $message);
                return $this->redirect([$this->_beforestaffPath]);
            }
        } else {
            return $this->redirect([$this->_beforestaffPath]);
        }
    }

    public function checkSignup($email, $cur_step = 0) {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }

        $emailErrorMsg = Yii::t('app', "Email doesn't exist");
        $isValid = true;
        $step_completed = "";
        $urlNumber = "";
        if (!isset($email) || !$email) {
            Yii::$app->session->setFlash('error', $emailErrorMsg);
            $isValid = false;
        }
        $restapiData = array();
        $restapiData['User[module_id]'] = $this->_module_id;
        $restapiData['User[type]'] = 'Staff';
        $restapiData['User[email]'] = $email;
        $restapiData['language_id'] = $this->_langID;

        $output = Yii::$app->ApiCallFunctions->EmailCheck($restapiData);

        if (isset($output['status']) && $output['status'] == 200) {
            $user = (isset($output['data'][0])) ? $output['data'][0] : "";
            $step_completed = (isset($output['data'][0]['step_completed'])) ? $output['data'][0]['step_completed'] : 0;
            $step_completed = str_replace('step-', '', $step_completed);
            $urlNumber = $step_completed == '0' ? '' : $step_completed + 1;
        } else {
            $message = ($output && $output['message']) ? $output['message'] : 'something went wrong';
        }
        if ($isValid && $cur_step > (int) $urlNumber) {
            Yii::$app->session->setFlash('error', Yii::t('app', "Something went wrong"));
            $isValid = false;
        }
        if ($cur_step && $step_completed && $cur_step != $urlNumber) {
            $isValid = false;
        }
        return (!$isValid) ? false : $user;
    }

    public function actionSignup($email = "") {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }
        if (empty($email)) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';

        $usermodel = new User();
        $usermodel->scenario = 'step1';

        $staffmodel = new StaffUser();
        $staffmodel->scenario = 'step1';

        $categoryData = array();
        $restapiData = array();
        $restapiData['type'] = 'Staff';
        $restapiData['module_id'] = $this->_module_id;
        $restapiData['language_id'] = $this->_langID;
        $outputcat = Yii::$app->ApiCallFunctions->AppCategoryApi($restapiData);

        if ($outputcat['status'] == 200) {
            $categoryData = ArrayHelper::map($outputcat['data'], 'category_id', 'name');
        }
        $moduleData = array();
        $restapiData = array();
        $restapiData['module_id'] = $this->_module_id;
        $restapiData['language_id'] = $this->_langID;
        $outputmodule = Yii::$app->ApiCallFunctions->AppModulesApi($restapiData);
        if ($outputmodule['status'] == 200) {
            $moduleData = ArrayHelper::map($outputmodule['data'], 'module_id', 'name');
        }
        if ($usermodel->load(Yii::$app->request->post()) && $staffmodel->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();

            //User
            $postData['User']['module_id'] = $this->_module_id;
            $postData['User']['type'] = 'Staff';
            $postData['User']['language_id'] = $this->_langID;
            $postData['User']['preferred_language_id'] = 1;

            $prefix = 'User';
            $restapiData = array();
            if (isset($postData[$prefix]) && $postData[$prefix]) {
                foreach ($postData[$prefix] as $key => $value) {
                    $restapiData[$prefix . '[' . $key . ']'] = $value;
                }
            }
            $postData['StaffUser']['image_url'] = Yii::$app->FrontFunctions->uploadedfiledata($staffmodel, 'image_url', 'profileimage');
            $prefix = 'StaffUser';
            if (isset($postData[$prefix]) && $postData[$prefix]) {
                foreach ($postData[$prefix] as $key => $value) {
                    $restapiData[$prefix . '[' . $key . ']'] = $value;
                }
            }


            //Social data
            if (isset($_REQUEST['userdata']) && $_REQUEST['userdata']) {
                $userData = json_decode(base64_decode($_REQUEST['userdata']), true);
                if ($userData) {
                    $restapiData['User[social_id]'] = $userData['id'];
                    $restapiData['User[loginwith]'] = $userData['loginwith'];
                }
            }
            $output = Yii::$app->ApiCallFunctions->SignupStepOneStaff($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect([$this->_beforestaffPath . 'signup2', 'email' => $usermodel->email]);
            } else {
                Yii::$app->session->setFlash('error', $message);
                $errors = $staffmodel->getErrors();
                if ($errors) {
                    print_r($errors);
                    exit;
                }
            }
        }

        return $this->render('signup', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'usermodel' => $usermodel,
                    'staffmodel' => $staffmodel,
                    'categoryData' => $categoryData,
                    'moduleData' => $moduleData,
        ]);
    }

    public function actionSignup2($email) {
        $user = $this->checkSignup($email, 2);
        if (!$user) {
            Yii::$app->session->setFlash('error', Yii::t('app', "Staff user invalid"));
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';

        $staffmodel = new StaffUser();
        $staffmodel->scenario = 'step2';

        /* -----------Roc Start Api-------------------- */
        $userSoftwares = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $outputuserSoftwares = Yii::$app->ApiCallFunctions->userSoftwares($restapiData);
        if ($outputuserSoftwares['status'] == 200) {
            $userSoftwares = ArrayHelper::map($outputuserSoftwares['data'], 'software_id', 'name');
        }

        $UserSkill = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $outputuserSkill = Yii::$app->ApiCallFunctions->UserSkill($restapiData);
        if ($outputuserSkill['status'] == 200) {
            $UserSkill = ArrayHelper::map($outputuserSkill['data'], 'skill_id', 'name');
        }
        /* -----------Roc End Api-------------------- */

        /* -----------Common Start Api-------------------- */
        $UserLanguages = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $outputlang = Yii::$app->ApiCallFunctions->UserLanguages($restapiData);
        if ($outputlang['status'] == 200) {
            $UserLanguages = ArrayHelper::map($outputlang['data'], 'language_id', 'name');
        }

        $AppProvince = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $restapiData['type'] = 'income';
        $outputprovince = Yii::$app->ApiCallFunctions->AppProvinceApi($restapiData);
        if ($outputprovince['status'] == 200) {
            $AppProvince = ArrayHelper::map($outputprovince['data'], 'province_id', 'name');
        }

        /* -----------Common end Api-------------------- */

        if ($staffmodel->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();

            $user_software_array = array();
            if (isset($postData['StaffUser']['user_software']) && $postData['StaffUser']['user_software']) {
                foreach ($postData['StaffUser']['user_software'] as $value) {
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

                    $user_software_array[] = array('relation_id' => $relation_id, 'level_of_knowledge' => $level_of_knowledge);
                }
            }
            $postData['StaffUser']['user_software'] = json_encode($user_software_array);

            $user_skill_array = array();
            if (isset($postData['StaffUser']['user_skill']) && $postData['StaffUser']['user_skill']) {
                foreach ($postData['StaffUser']['user_skill'] as $value) {
                    $relation_id = $value;
                    $level_of_knowledge_id = $value_arr[1];
                    $level_of_knowledge = 'Expert';

                    $user_skill_array[] = array('relation_id' => $relation_id, 'level_of_knowledge' => $level_of_knowledge);
                }
            }
            $postData['StaffUser']['user_skill'] = json_encode($user_skill_array);

            $user_language_array = array();
            if (isset($postData['StaffUser']['user_languages']) && $postData['StaffUser']['user_languages']) {
                foreach ($postData['StaffUser']['user_languages'] as $value) {
                    $value_arr = explode('-', $value);
                    $relation_id = $value_arr[0];
                    $level_of_knowledge_id = $value_arr[1];
                    $level_of_knowledge = '';
                    if ($level_of_knowledge_id == 1) {
//                        $level_of_knowledge = Yii::t('app','Beginner');
                        $level_of_knowledge = 'Beginner';
                    } else if ($level_of_knowledge_id == 2) {
                        $level_of_knowledge = 'Intermediary';
                    } else if ($level_of_knowledge_id == 3) {
                        $level_of_knowledge = 'Expert';
                    }

                    $user_language_array[] = array('relation_id' => $relation_id, 'level_of_knowledge' => $level_of_knowledge);
                }
            }
            $postData['StaffUser']['user_languages'] = json_encode($user_language_array);

            $licence_number_array = array();
            if ($postData['StaffUser']['province_id']) {
                for ($i = 0; $i < sizeof($postData['StaffUser']['province_id']); $i++) {
                    $licence_number = (!empty($postData['StaffUser']['licence_number'])) ? $postData['StaffUser']['licence_number'][$i] : '';
                    $licence_number_array[] = array('licence_number' => $licence_number, 'province_id' => (int) $postData['StaffUser']['province_id'][$i]);
                }
                $postData['StaffUser']['province_id'] = $postData['StaffUser']['province_id'][0];
            }
            $postData['StaffUser']['user_licencein'] = json_encode($licence_number_array);
            $certiNames = array();
            if (isset($postData['certificates_tranings_name']) && $postData['certificates_tranings_name']) {
                $certificates_name = $postData['certificates_tranings_name']['name'];
                $certificates_expireddate = $postData['certificates_tranings_name']['expireddate'];
                $certificates_document_id = $postData['certificates_tranings_name']['document_id'];
                for ($i = 0; $i < count($certificates_name); $i++) {
                    $certiNamesfield['name'] = $certificates_name[$i];
                    $certiNamesfield['expireddate'] = $certificates_expireddate[$i];
                    $certiNames[] = $certiNamesfield;
                }
            }
            //$postData['StaffUser']['certificates_tranings_name'] = json_encode($certiNames);
            $postData['StaffUser']['certificates_tranings_name'] = "";
            $stfprefix = 'StaffUser';
            $restapiData = array();
            if (isset($postData[$stfprefix]) && $postData[$stfprefix]) {
                foreach ($postData[$stfprefix] as $key => $value) {
                    if (is_array($value) && $key != 'certificates_tranings') {
                        $value = implode(', ', $value);
                    }

                    if ($key == 'certificates_tranings') {
                        foreach ($value as $k => $vl) {
                            if ($vl) {
                                $restapiData[$stfprefix . '[' . $key . '][' . $k . ']'] = $vl;
                            }
                        }
                    } else {
                        $restapiData[$stfprefix . '[' . $key . ']'] = $value;
                    }
                }
            }

            $restapiData['authkey'] = $user['authkey'];

            $output = Yii::$app->ApiCallFunctions->SignupStepTwoStaff($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', Yii::t('app', "Updated successfully"));
                return $this->redirect([$this->_beforestaffPath . 'signup3', 'email' => $_GET['email']]);
            } else {
                Yii::$app->session->setFlash('error', $message);
            }
        }
        return $this->render('signup2', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'user' => $user,
                    'UserLanguages' => $UserLanguages, //Common
                    'AppProvince' => $AppProvince, //Common
                    'userSoftwares' => $userSoftwares, //Roc
                    'UserSkill' => $UserSkill, //Roc
                    'staffmodel' => $staffmodel, //Form
        ]);
    }

    public function actionSignup3($email) {
        $user = $this->checkSignup($email, 3);
        if (!$user) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';
        $model = new StaffWorkExperience();
        $model->scenario = "step3";
        $WorkExperienceModal = [$model];

        $workexpData = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $outputexp = Yii::$app->ApiCallFunctions->UserWorkExperience($restapiData);

        if ($outputexp['status'] == 200) {
            $workexpData = $outputexp['data'];
        }
//        if ($model->load(Yii::$app->request->post())) {
//            
//        }
        $postData = Yii::$app->request->post();
        if ($postData) {
            $StaffWorkExperience = array();
            $restapiData = array();
            $restapiData['authkey'] = $user['authkey'];
            if (isset($postData['StaffWorkExperience']) && is_array($postData['StaffWorkExperience'])) {
                $postData['StaffWorkExperience'] = $postData['StaffWorkExperience'];
                foreach ($postData['StaffWorkExperience'] as $exp) {
                    $exp['knownsw'] = implode(",", $exp['knownsw']);
                    $exp['is_verify'] = 'No';
                    $StaffWorkExperience[] = $exp;
                }
                $restapiData['staffworkexperience'] = json_encode($StaffWorkExperience);
            }else{
                $restapiData['staffworkexperience'] = json_encode($StaffWorkExperience);
            }
            $output = Yii::$app->ApiCallFunctions->SignupStepThreeStaff($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect([$this->_beforestaffPath . 'signup4', 'email' => $_GET['email']]);
            } else {
                Yii::$app->session->setFlash('error', $message);
            }
        }
        return $this->render('signup3', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'user' => $user,
                    'workexpData' => $workexpData,
                    'model' => (empty($WorkExperienceModal)) ? [$model] : $WorkExperienceModal,
        ]);
    }

    public function actionSignup4($email) {
        $user = $this->checkSignup($email, 4);
        if (!$user) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';
        $staffmodel = new StaffUser();
        $staffmodel->scenario = 'step4';
        if ($staffmodel->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            $restapiData = array();
            $stfprefix = 'StaffUser';
            if (isset($postData[$stfprefix]) && $postData[$stfprefix]) {
                foreach ($postData[$stfprefix] as $key => $value) {
                    $restapiData[$stfprefix . '[' . $key . ']'] = $value;
                }
            }
            $restapiData['authkey'] = $user['authkey'];
            //echo "<pre>"; print_r($restapiData);exit;
            $output = Yii::$app->ApiCallFunctions->SignupStepFourStaff($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect([$this->_beforestaffPath . 'signup5', 'email' => $_GET['email']]);
            } else {
                Yii::$app->session->setFlash('error', $message);
            }
        } else {
//            $errors = $staffmodel->getErrors();
//            if ($errors) {
//                print_r($errors);
//                exit;
//            }
        }
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $restapiData['type'] = 'income';
        $provinceoutput = Yii::$app->ApiCallFunctions->AppProvinceApi($restapiData);
        $provincedata = array();
        $gst_percentage_province_id = array();
        $hst_percentage_province_id = array();
        $pst_percentage_province_id = array();
        $qst_percentage_province_id = array();
        if ($provinceoutput['status'] == 200) {
            $provinceDetails = $provinceoutput['data'];
            if ($provinceDetails) {
                $gst_percentage_province_id = Yii::$app->FrontFunctions->removeElementWithValuebyGetID($provinceDetails, "gst_percentage", 0, 'province_id', 'string');
                $hst_percentage_province_id = Yii::$app->FrontFunctions->removeElementWithValuebyGetID($provinceDetails, "hst_percentage", 0);
                $pst_percentage_province_id = Yii::$app->FrontFunctions->removeElementWithValuebyGetID($provinceDetails, "pst_percentage", 0, 'province_id', 'string');
                $qst_percentage_province_id = Yii::$app->FrontFunctions->removeElementWithValuebyGetID($provinceDetails, "qst_percentage", 0, 'province_id', 'string');


                $gst_percentage_province_id = Yii::$app->FrontFunctions->getarraycolumn($gst_percentage_province_id, 'province_id');
                $hst_percentage_province_id = Yii::$app->FrontFunctions->getarraycolumn($hst_percentage_province_id, 'province_id');
                $pst_percentage_province_id = Yii::$app->FrontFunctions->getarraycolumn($pst_percentage_province_id, 'province_id');
                $qst_percentage_province_id = Yii::$app->FrontFunctions->getarraycolumn($qst_percentage_province_id, 'province_id');
            }
            if ($provinceDetails) {
                $provinceData = ArrayHelper::map($provinceDetails, 'province_id', 'name');
            }
        }
        return $this->render('signup4', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'user' => $user,
                    'staffmodel' => $staffmodel,
                    'provinceData' => $provinceData,
                    'gst_percentage_province_id' => $gst_percentage_province_id,
                    'hst_percentage_province_id' => $hst_percentage_province_id,
                    'pst_percentage_province_id' => $pst_percentage_province_id,
                    'qst_percentage_province_id' => $qst_percentage_province_id,
        ]);
    }

    public function actionSignup5($email) {
        $user = $this->checkSignup($email, 5);
        if (!$user) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';
        $usermodel = new User();
        $staffmodel = new StaffUser();
        $usermodel->scenario = 'step5';
        $staffmodel->scenario = 'step5';
        if ($usermodel->load(Yii::$app->request->post()) && $staffmodel->load(Yii::$app->request->post())) {

            $postData = Yii::$app->request->post();
            $postData['StaffUser']['registration_card'] = Yii::$app->FrontFunctions->uploadedfiledata($staffmodel, 'registration_card', 'registration_card_' . date('Y-m-d_H:i'));
            $postData['StaffUser']['id_with_picture'] = Yii::$app->FrontFunctions->uploadedfiledata($staffmodel, 'id_with_picture', 'id_with_picture_' . date('Y-m-d_H:i'));
            $postData['StaffUser']['diploma'] = Yii::$app->FrontFunctions->uploadedfiledata($staffmodel, 'diploma', 'diploma_' . date('Y-m-d_H:i'));
            if (isset($postData['User']['howdidyouhereaboutbelocum']) && $postData['User']['howdidyouhereaboutbelocum'] == "Word of mouth") {
                if (isset($postData['User']['referral_name']) && $postData['User']['referral_name']) {
                    $postData['User']['howdidyouhereaboutbelocum'] = "Word of mouth : " . $postData['User']['referral_name'];
                }
            }
            $prefix = 'User';
            $restapiData = array();
            if (isset($postData[$prefix]) && $postData[$prefix]) {
                foreach ($postData[$prefix] as $key => $value) {
                    $restapiData[$prefix . '[' . $key . ']'] = $value;
                }
            }

            $stfprefix = 'StaffUser';
            if (isset($postData[$stfprefix]) && $postData[$stfprefix]) {
                foreach ($postData[$stfprefix] as $key => $value) {
                    $restapiData[$stfprefix . '[' . $key . ']'] = $value;
                }
            }

            $restapiData['authkey'] = $user['authkey'];

            $output = Yii::$app->ApiCallFunctions->SignupStepFiveStaff($restapiData);

            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect([$this->_beforestaffPath . 'signup6', 'email' => $_GET['email']]);
            } else {
                Yii::$app->session->setFlash('error', $message);
            }
        }

        return $this->render('signup5', [
                    "_lang" => $this->_lang,
                    "_langID" => $this->_langID,
                    "_baseUrl" => $this->_baseUrl,
                    "_basePath" => $this->_basePath,
                    "_module" => $this->_module,
                    "_moduleUrl" => $this->_moduleUrl,
                    "_modulePath" => $this->_modulePath,
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'user' => $user,
                    'usermodel' => $usermodel,
                    'staffmodel' => $staffmodel
        ]);
    }

    public function actionSignup6($email) {

        $user = $this->checkSignup($email, 6);
        if (!$user) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';

        $staffUser = array();
        $profiledata = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $profileoutput = Yii::$app->ApiCallFunctions->GetProfileApi($restapiData);
        if ($profileoutput['status'] == 200) {
            $profiledata = $profileoutput['data'];
            if ($profiledata && isset($profiledata['staffdata']) && is_array($profiledata['staffdata']) && $profiledata['staffdata']) {
                $staffUser = $profiledata['staffdata'][0];
            }
        }
        if ($staffUser) {
            $model = new UserQuestionsAnswer();
            $model->scenario = 'step6';

            if ($model->load(Yii::$app->request->post())) {

                $postData = Yii::$app->request->post();
                $postData = $postData['UserQuestionsAnswer'];
                $restapiData = array();
                $fieldArray = array();
                $dataArray = array();
                if (isset($postData['is_comment']) && $postData['is_comment']) {
                    foreach ($postData['is_comment'] as $key => $value) {
                        $fieldArray['question_id'] = $key;
                        if ($value == "Yes") {
                            $fieldArray['answer'] = "";
                            $fieldArray['comment'] = (isset($postData['comment'][$key]) && $postData['comment'][$key]) ? $postData['comment'][$key] : "";
                            $dataArray[] = $fieldArray;
                        } elseif ($value == "No") {
                            $fieldArray['answer'] = (isset($postData['answer'][$key]) && $postData['answer'][$key]) ? $postData['answer'][$key] : "";
                            $fieldArray['comment'] = (isset($postData['comment'][$key]) && $postData['comment'][$key]) ? $postData['comment'][$key] : "";
                            $dataArray[] = $fieldArray;
                        } elseif ($value == "onYes" || $value == "onNo") {
                            $fieldArray['answer'] = (isset($postData['answer'][$key]) && $postData['answer'][$key]) ? $postData['answer'][$key] : "";
                            $fieldArray['comment'] = (isset($postData['comment'][$key]) && $postData['comment'][$key]) ? $postData['comment'][$key] : "";
                            $dataArray[] = $fieldArray;
                        }
                    }
                }
                $restapiData['authkey'] = $user['authkey'];
                $restapiData['questions'] = json_encode($dataArray);
                $output = Yii::$app->ApiCallFunctions->SignupStepSixStaff($restapiData);

                $message = $output['message'];
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $message);
                    return $this->redirect([$this->_beforestaffPath . 'signup7', 'email' => $_GET['email']]);
                } else {
                    Yii::$app->session->setFlash('error', $message);
                }
            }
            $UserQuestions = array();
            $restapiData = array();
            $restapiData['step'] = 'step-6';
            $restapiData['authkey'] = $user['authkey'];
            $restapiData['language_id'] = $this->_langID;
            $restapiData['category_id'] = $staffUser['category_id'];
            $output = Yii::$app->ApiCallFunctions->UserQuestions($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                $UserQuestions = $output['data'];
            }
            return $this->render('signup6', [
                        "_lang" => $this->_lang,
                        "_langID" => $this->_langID,
                        "_baseUrl" => $this->_baseUrl,
                        "_basePath" => $this->_basePath,
                        "_module" => $this->_module,
                        "_moduleUrl" => $this->_moduleUrl,
                        "_modulePath" => $this->_modulePath,
                        "_staffUrl" => $this->_staffUrl,
                        "_staffPath" => $this->_staffPath,
                        "_beforestaffUrl" => $this->_beforestaffUrl,
                        "_beforestaffPath" => $this->_beforestaffPath,
                        'user' => $user,
                        'model' => $model,
                        'UserQuestions' => $UserQuestions,
            ]);
        } else {
            return $this->redirect([$this->_beforestaffPath]);
        }
    }

    public function actionSignup7($email) {
        $user = $this->checkSignup($email, 7);
        if (!$user) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $this->layout = 'beforelogin';

        $staffUser = array();
        $profiledata = array();
        $restapiData = array();
        $restapiData['authkey'] = $user['authkey'];
        $restapiData['language_id'] = $this->_langID;
        $profileoutput = Yii::$app->ApiCallFunctions->GetProfileApi($restapiData);
        if ($profileoutput['status'] == 200) {
            $profiledata = $profileoutput['data'];
            if ($profiledata && isset($profiledata['staffdata']) && is_array($profiledata['staffdata']) && $profiledata['staffdata']) {
                $staffUser = $profiledata['staffdata'][0];
            }
        }
        if ($staffUser) {
            $model = new UserQuestionsAnswer();
            $model->scenario = 'step7';

            if ($model->load(Yii::$app->request->post())) {

                $postData = Yii::$app->request->post();

                $postData = $postData['UserQuestionsAnswer'];
                $restapiData = array();
                $fieldArray = array();
                $dataArray = array();
                if (isset($postData['question_id']) && $postData['question_id']) {
                    foreach ($postData['question_id'] as $key => $value) {
                        $fieldArray['question_id'] = $value;
                        $fieldArray['answer'] = 'Yes';
                        $fieldArray['comment'] = (isset($postData['comment'][$value]) && $postData['comment'][$value]) ? $postData['comment'][$value] : implode(',', $postData['option']);
                        $dataArray[] = $fieldArray;
                    }
                }
                $restapiData['authkey'] = $user['authkey'];
                $restapiData['questions'] = json_encode($dataArray);
                $output = Yii::$app->ApiCallFunctions->SignupStepSevenStaff($restapiData);

                $message = $output['message'];
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $message);
                    $data = $output['data'];
                    if (isset($data['interview_schedule']) && $data['interview_schedule']) {
                        return $this->redirect($data['interview_schedule']);
                    }
                    $ipInfo = Yii::$app->MyFunctions->ip_info();
                    Yii::$app->MyFunctions->addAccessLog($user['staff_user_id'], $user['type'], 'login', json_encode($ipInfo));

                    $userdata = UserResponse::findIdentity($user['authkey']);
                    $rememberMe = true;
                    $dataobj = Yii::$app->user->login($userdata, $rememberMe ? 3600 * 24 * 30 : 0);

                    if ($dataobj) {
                        return $this->redirect([$this->_staffPath]);
                    }
                    return $this->redirect([$this->_beforestaffPath, 'email' => $_GET['email']]);
                } else {
                    Yii::$app->session->setFlash('error', $message);
                }

                //            $apiUrl = Url::home(true) . 'restapi/v1/signupauth/signup-step7';
                //            $response = Yii::$app->MyFunctions->submitForm($apiUrl, $postArray);
                //
                //            if ($response) {
                //                $data = json_decode($response);
                //                if ($data && isset($data->result) && $data->result == 1) {
                //                    //success next step redirect
                //                    if (isset($data->interview_schedule) && $data->interview_schedule) {
                //                        $this->redirect($data->interview_schedule);
                //                    }
                //                    $this->redirect([$this->_moduleTypePath.'login', 'email' => $_GET['email']]);
                //                } else {
                //                    $message = ($data && $data->message) ? $data->message : 'something went wrong';
                //                    Yii::$app->session->setFlash('error', $message);
                //                }
                //            }
            }
            $UserQuestions = array();
            $restapiData = array();
            $restapiData['step'] = 'step-7';
            $restapiData['authkey'] = $user['authkey'];
            $restapiData['language_id'] = $this->_langID;
            $restapiData['category_id'] = $staffUser['category_id'];
            $output = Yii::$app->ApiCallFunctions->UserQuestions($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                $UserQuestions = $output['data'];
            }
            return $this->render('signup7', [
                        "_lang" => $this->_lang,
                        "_langID" => $this->_langID,
                        "_baseUrl" => $this->_baseUrl,
                        "_basePath" => $this->_basePath,
                        "_module" => $this->_module,
                        "_moduleUrl" => $this->_moduleUrl,
                        "_modulePath" => $this->_modulePath,
                        "_staffUrl" => $this->_staffUrl,
                        "_staffPath" => $this->_staffPath,
                        "_beforestaffUrl" => $this->_beforestaffUrl,
                        "_beforestaffPath" => $this->_beforestaffPath,
                        'user' => $user,
                        'model' => $model,
                        'UserQuestions' => $UserQuestions,
            ]);
        } else {
            return $this->redirect([$this->_beforestaffPath]);
        }
    }

}
