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
use frontend\models\roc\OwnerUser;
use frontend\models\roc\User;
use frontend\models\OwnerManager;
use frontend\models\ForgotPassword;

/**
 * Staff controller
 */
class BeforeOwnerController extends Controller {

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
        $this->_ownerUrl = $this->_baseUrl . $this->_module['unique_name'] . '/owner/';
        $this->_ownerPath = '/' . $this->_module['unique_name'] . '/owner/';
        $this->_beforeownerUrl = $this->_baseUrl . $this->_module['unique_name'] . '/before-owner/';
        $this->_beforeownerPath = '/' . $this->_module['unique_name'] . '/before-owner/';

        if (!empty(Yii::$app->user->identity)) {
            $this->_user = Yii::$app->user->identity;
            $message = "";
            if (Yii::$app->user->identity && isset(Yii::$app->user->identity->message)) {
                $message = Yii::$app->user->identity->message;
            }
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect([$this->_beforeownerPath]);
        }
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforeownerPath]);
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
            return $this->redirect([$this->_beforeownerPath]);
        }
        $this->layout = 'beforelogin';
        $usermodel = new User();
        $usermodel->scenario = 'emailrequired';
        $model = new UserLoginForm();
        $model->module_id = $this->_module_id;
        $model->type = 'Owner';
        $model->language_id = $this->_langID;
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect([$this->_ownerPath]);
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
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'model' => $model,
                    'usermodel' => $usermodel,
        ]);
    }

    public function actionLogin($email = "") {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforeownerPath]);
        }
        return $this->redirect([$this->_beforeownerPath]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionForgotPassword() {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforeownerPath]);
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
                return $this->redirect([$this->_beforeownerPath]);
            } else {
                Yii::$app->session->setFlash('error', $message);
                return $this->redirect([$this->_beforeownerPath . 'forgotPassword']);
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
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'model' => $model,
        ]);
    }

    public function actionResetPassword($token) {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforeownerPath]);
        }
        $this->layout = 'mainlogin';
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
            "_ownerUrl" => $this->_ownerUrl,
            "_ownerPath" => $this->_ownerPath,
            "_beforeownerUrl" => $this->_beforeownerUrl,
            "_beforeownerPath" => $this->_beforeownerPath,
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
            $restapiData['User[type]'] = 'Owner';
            $restapiData['User[email]'] = $usermodel->email;
            $restapiData['language_id'] = $this->_langID;
            $output = Yii::$app->ApiCallFunctions->EmailCheck($restapiData);
            if (isset($output['status']) && $output['status'] == 200) {
                $step_completed = (isset($output['data'][0]['step_completed'])) ? $output['data'][0]['step_completed'] : 0;
                $step_completed = str_replace('step-', '', $step_completed);
                $urlNumber = $step_completed == '0' ? '' : $step_completed + 1;
                return $this->redirect([$this->_beforeownerPath . 'signup' . $urlNumber, 'email' => $usermodel->email]);
            } else {
                $message = ($output && $output['message']) ? $output['message'] : 'something went wrong';
                Yii::$app->session->setFlash('error', $message);
                return $this->redirect([$this->_beforeownerPath]);
            }
        } else {
            return $this->redirect([$this->_beforeownerPath]);
        }
    }

    public function checkSignup($email, $cur_step = 0) {

        if (!Yii::$app->user->isGuest) {
            return false;
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
        $restapiData['User[type]'] = 'Owner';
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
            return $this->redirect([$this->_beforeownerPath]);
        }
        if (empty($email)) {
            return $this->redirect([$this->_beforeownerPath]);
        }
        $this->layout = 'beforelogin';

        $usermodel = new User();
        $usermodel->scenario = 'step1';

        $ownermodel = new OwnerUser();
        $ownermodel->scenario = 'step1';

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
        if ($usermodel->load(Yii::$app->request->post())) {

            $postData = Yii::$app->request->post();

            $postData['User']['module_id'] = $this->_module_id;
            $postData['User']['type'] = 'Owner';
            $postData['User']['language_id'] = $this->_langID;
            $postData['User']['preferred_language_id'] = $this->_langID;

            if (isset($_REQUEST['userdata']) && $_REQUEST['userdata']) {
                $userData = json_decode(base64_decode($_REQUEST['userdata']), true);
                if ($userData) {
                    $postData['User']['social_id'] = $userData['id'];
                    $postData['User']['loginwith'] = $userData['loginwith'];
                }
            }

            $prefix = 'User';
            $restapiData = array();
            if (isset($postData[$prefix]) && $postData[$prefix]) {
                foreach ($postData[$prefix] as $key => $value) {
                    $restapiData[$prefix . '[' . $key . ']'] = $value;
                }
            }
            $output = Yii::$app->ApiCallFunctions->SignupStepOneOwner($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect([$this->_beforeownerPath . 'signup2', 'email' => $postData['User']['email']]);
            } else {
                Yii::$app->session->setFlash('error', $message);
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
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'usermodel' => $usermodel,
                    'ownermodel' => $ownermodel,
                    'categoryData' => $categoryData,
                    'moduleData' => $moduleData,
        ]);
    }

    public function actionSignup2($email) {
        $user = $this->checkSignup($email, 2);

        if (!$user) {
            Yii::$app->session->setFlash('error', Yii::t('app', "Owner user invalid"));
            return $this->redirect([$this->_beforeownerPath]);
        }

        $this->layout = 'beforelogin';

        $ownermodel = new OwnerUser();
        $ownermodel->scenario = 'step2';

        $usermodel = new User();
        $usermodel->scenario = 'step2';

        $ownmgrmodel = new OwnerManager();
        $ownmgrmodel->scenario = 'manager';
        $modelsManager = [$ownmgrmodel];


        /* -----------Roc Start Api-------------------- */
        $categoryData = array();
        $restapiData = array();
        $restapiData['type'] = 'Owner';
        $restapiData['module_id'] = $this->_module_id;
        $restapiData['language_id'] = $this->_langID;
        $outputcat = Yii::$app->ApiCallFunctions->AppCategoryApi($restapiData);
        if ($outputcat['status'] == 200) {
            $categoryData = ArrayHelper::map($outputcat['data'], 'category_id', 'name');
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
        $restapiData['language_id'] = $this->_langID;
        $restapiData['module_id'] = $this->_module_id;
        $outputprovince = Yii::$app->ApiCallFunctions->beforeauthAppProvinceApi($restapiData);
        if ($outputprovince['status'] == 200) {
            $AppProvince = ArrayHelper::map($outputprovince['data'], 'province_id', 'name');
        }

        /* -----------Common end Api-------------------- */

        if ($ownermodel->load(Yii::$app->request->post()) && $usermodel->load(Yii::$app->request->post())) {
            $postData = Yii::$app->request->post();
            $image_url = UploadedFile::getInstance($ownermodel, 'image_url');
            if ($image_url) {
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
            if ($postData['OwnerManager'] && isset($postData['OwnerManager']['first_name']) && $postData['OwnerManager']['first_name']) {
                foreach ($postData['OwnerManager']['first_name'] as $key => $value) {
                    if ($value) {
                        $first_name = $value;
                        $last_name = (isset($postData['OwnerManager']['last_name']) && isset($postData['OwnerManager']['last_name'][$key]) && $postData['OwnerManager']['last_name'] && $postData['OwnerManager']['last_name'][$key]) ? $postData['OwnerManager']['last_name'][$key] : null;
                        $phone_number = (isset($postData['OwnerManager']['phone_number']) && isset($postData['OwnerManager']['phone_number'][$key]) && $postData['OwnerManager']['phone_number'] && $postData['OwnerManager']['phone_number'][$key]) ? $postData['OwnerManager']['phone_number'][$key] : null;

                        $owner_manager_array[] = array('first_name' => $first_name, 'last_name' => $last_name, 'phone_number' => $phone_number);
                    }
                }
            }
            $owner_manager_array = array();
            if (isset($postData['OwnerManager']) && $postData['OwnerManager']) {
                $owner_manager_array = json_encode($postData['OwnerManager']);
            }
            $postData['OwnerUser']['manager'] = $owner_manager_array;
            if (isset($postData['User']['howdidyouhereaboutbelocum']) && $postData['User']['howdidyouhereaboutbelocum'] == "Word of mouth") {
                if (isset($postData['User']['referral_name']) && $postData['User']['referral_name']) {
                    $postData['User']['howdidyouhereaboutbelocum'] = "Word of mouth : " . $postData['User']['referral_name'];
                }
            }
            $restapiData = array();
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
                    $restapiData[$stfprefix . '[' . $key . ']'] = $value;
                }
            }
            $restapiData['authkey'] = $user['authkey'];
            $output = Yii::$app->ApiCallFunctions->SignupStepTwoStaff($restapiData);

            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect([$this->_beforeownerPath, 'email' => $_GET['email']]);
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
                    "_ownerUrl" => $this->_ownerUrl,
                    "_ownerPath" => $this->_ownerPath,
                    "_beforeownerUrl" => $this->_beforeownerUrl,
                    "_beforeownerPath" => $this->_beforeownerPath,
                    'email' => $email,
                    'user' => $user,
                    'usermodel' => $usermodel,
                    'UserLanguages' => $UserLanguages, //Common
                    'AppProvince' => $AppProvince, //Common
                    'categoryData' => $categoryData, //Roc
                    'ownermodel' => $ownermodel, //Form
                    'ownermodelsManager' => (empty($ownermodelsManager)) ? [$ownmgrmodel] : $ownermodelsManager,
        ]);
    }

    public function actionSoftware($email = "") {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            if ($email) {
                $user = $this->checkSignup($email, 2);
                if (!$user) {
                    return false;
                }
            } else {
                return false;
            }
        }
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
            if ($email) {
                $user = $this->checkSignup($email, 2);
                if (!$user) {
                    return false;
                }
            } else {
                return false;
            }
        }
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

}
