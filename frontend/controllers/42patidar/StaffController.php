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
use frontend\models\Availability;
use frontend\models\Documents;
use frontend\models\UserQuestionsAnswer;
use frontend\models\Promocode;
use frontend\models\roc\User;
use frontend\models\roc\StaffUser;
use frontend\models\roc\StaffWorkExperience;


/**
 * Staff controller
 */
class StaffController extends Controller {
    
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
    public $_profiledataMsg = "";
    public $_profiledata = array();

   
    public function init() {
        parent::init();
        $this->layout = 'afterlogin';
        $this->_baseUrl = Url::base(true).'/';
        $this->_basePath = Url::base().'/';
        $this->_lang = Yii::$app->FrontFunctions->defaultlanguage();
        $this->_langID = Yii::$app->FrontFunctions->defaultlanguage(true);
        $this->_module = Yii::$app->FrontFunctions->AppModules($this->_module_id);
        $this->_moduleUrl = $this->_baseUrl.$this->_module['unique_name'].'/';
        $this->_modulePath = $this->_basePath.$this->_module['unique_name'].'/';
        $this->_staffUrl = $this->_baseUrl.$this->_module['unique_name'].'/staff/';
        $this->_staffPath = '/'.$this->_module['unique_name'].'/staff/';
        $this->_beforestaffUrl = $this->_baseUrl.$this->_module['unique_name'].'/before-staff/';
        $this->_beforestaffPath = '/'.$this->_module['unique_name'].'/before-staff/';
        
        $message = "";
        if(Yii::$app->user->identity && isset(Yii::$app->user->identity->message)){
            $message = Yii::$app->user->identity->message;
        }
        if(!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity)){
            $this->_user = Yii::$app->user->identity;
            $profileoutput = Yii::$app->ApiCallFunctions->GetProfileApi();
            $this->_profiledataMsg = $profileoutput['message'];
            if ($profileoutput['status'] == 200) {
                $this->_profiledata = $profileoutput['data'];
            }
        }
        if(empty($this->_user)){
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect($this->_beforestaffPath);
        }
        $continue = true;
        if($this->_user && $this->_user->type == "Owner"){
            $continue = false;
        }
        if(empty($continue)){
           throw new \yii\web\HttpException(404, Yii::t('app', 'Page not found.')); 
           exit;
        }
    }
    public function beforeAction($action) {
//        if ($this->action->id == 'slotapply') {
//            $this->enableCsrfValidation = false;
//        }
        $cookiedestory = Yii::$app->FrontFunctions->cookiedestory();
        if(isset($cookiedestory['result']) && isset($cookiedestory['type']) && $cookiedestory['result']) {
            $this->_moduleType = $cookiedestory['type'];
            Yii::$app->user->logout();
            return $this->redirect([$this->_beforestaffPath]);
        }
        return parent::beforeAction($action);
    }


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
        
        if(empty($this->_user) || Yii::$app->user->isGuest){
            Yii::$app->session->setFlash('error', '');
            return $this->redirect([$this->_beforestaffPath]);
        }
        $user = $this->_user;
        $output = Yii::$app->ApiCallFunctions->StaffGetHomeDetails();
        $data = array();
        if ($output['status'] == 200) {
            $data = $output['data'];
            $session = Yii::$app->session;
            if(isset($data['UserModulesetting']) && $data['UserModulesetting']){
                $session['UserModulesetting'] = $data['UserModulesetting'];
            }
        }
        $staffmodel = new StaffUser();
        $staffmodel->scenario = "bankdetail";
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
            return $this->redirect([$this->_staffPath]);
        }
        return $this->render('home', [
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
                    'data' => $data,
                    'staffmodel' => $staffmodel,
                    'user' => $user,
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
        return $this->redirect([$this->_beforestaffPath]);
    }
    
    public function actionAvailability() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        if ($this->_user->user_id == 753) {
            //echo Yii::$app->FrontFunctions->tohtmlcode("La plage demand�e n'est pas satisfaisante"); exit;
            $msgnotapproved = "<h5>BeLocum</h5>" . Yii::$app->FrontFunctions->tohtmlcode(Yii::t('app', 'Your account is not yet authorized to apply for contracts or publish availabilities.'));
        }
        $user = $this->_user;
        $output = Yii::$app->ApiCallFunctions->StaffGetAvailability();
        $data = array();
        if ($output['status'] == 200) {
            $data = $output['data'];
        }
        $staffmodel = new StaffUser();
//        $staffmodel->scenario = "bankdetail";
//        $staffmodel->sin = "";
//        if ($staffmodel->load(Yii::$app->request->post())) {
//            $postdata = Yii::$app->request->post();
//            $void_cheque_branchcode = "";
//            $sin = "";
//            if ($postdata) {
//                if (isset($postdata['StaffUser']) && $postdata['StaffUser']) {
//                    //$void_cheque_branchcode = $postdata['StaffUser']['void_cheque_branchcode'] . '-' . $postdata['StaffUser']['void_cheque_institutionnumber'] . '-' . $postdata['StaffUser']['void_cheque_accountnumber'];
//                    $void_cheque_branchcode = $staffmodel->void_cheque_branchcode . '-' . $staffmodel->void_cheque_institutionnumber . '-' . $staffmodel->void_cheque_accountnumber;
//                    if (isset($postdata['StaffUser']['sin'])) {
//                        $sin = $postdata['StaffUser']['sin'];
//                    }
//                }
//            }
//            $restapiData = array();
//            $restapiData['type'] = 'Staff';
//            $restapiData['StaffUser[sin]'] = (!empty($sin)) ? Yii::$app->MyFunctions->numberencode($sin) : '';
//            $restapiData['StaffUser[void_cheque_number]'] = $void_cheque_branchcode;
//            $restapiData['StaffUser[void_cheque_photo]'] = Yii::$app->FrontFunctions->uploadedfiledata($staffmodel, 'void_cheque_photo');
//
//            $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
//            if ($output['status'] == 200) {
//                Yii::$app->session->setFlash('success', Yii::t('app', 'Updated successfully.'));
//            } else {
//                Yii::$app->session->setFlash('error', Yii::t('app', 'Something went wrong. Please try again later.'));
//            }
//            return $this->redirect([$this->_staffPath . 'availability']);
//        }
        return $this->render('availability', [
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
                    'user' => $this->_user,
                    'profiledata' => $this->_profiledata,
                    'data' => $data,
                    "staffmodel" => $staffmodel
        ]);
    }

    public function actionAddavailability() {

        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;

        $model = new Availability();
        $model->scenario = "add";
        if ($model->load(Yii::$app->request->post())) {
            $postdata = Yii::$app->request->post();

            $tagmultidate = $model->tagmultidate;
            $availabletype = $model->type;
            if ($model->type != "Specificschedule") {
                $model->starttime = "";
                $model->endtime = "";
            }
            $starttime = $model->starttime;
            $endtime = $model->endtime;

            if ($availabletype && $tagmultidate) {
                $restapiData = array();
                $restapiData['date'] = $tagmultidate;
                $restapiData['type'] = $availabletype;
                if ($availabletype == 'Specificschedule') {
                    $restapiData['start_time'] = $starttime;
                    $restapiData['end_time'] = $endtime;
                } else {
                    $restapiData['start_time'] = "";
                    $restapiData['end_time'] = "";
                }
                $restapiData['schedule_type'] = 'date';
                $output = Yii::$app->ApiCallFunctions->StaffAddAvailability($restapiData);
                $data = array();
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                    return $this->redirect([$this->_staffPath . 'availability']);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Not Added. Please try again.'));
                    return $this->redirect([$this->_staffPath . 'availability']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Not Added. Please try again.'));
                return $this->redirect([$this->_staffPath . 'availability']);
            }
        }

        if (isset($_REQUEST['add_checked_date'])) {
            $add_checked_date = ($_REQUEST['add_checked_date']) ? $_REQUEST['add_checked_date'] : 0;
            if (!empty($add_checked_date)) {
                return $this->renderAjax('_add_availability', [
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
                            'user' => $this->_user,
                            'profiledata' => $this->_profiledata,
                            'add_checked_date' => implode(',', $add_checked_date),
                            'model' => $model
                ]);
            }
        }
    }

    public function actionRecurrentavailability() {

        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;

        $model = new Availability();
        $model->scenario = "recurrent";

        if ($model->load(Yii::$app->request->post())) {
            $postdata = Yii::$app->request->post();

            $weekday = $model->weekday;
            $noofmonth = $model->noofmonth;
            $availabletype = $model->type;
            if ($model->type != "Specificschedule") {
                $model->starttime = "";
                $model->endtime = "";
            }
            $starttime = $model->starttime;
            $endtime = $model->endtime;

            $start_time = ' 00:01:00';
            $end_time = ' 23:59:00';
            if ($weekday && $noofmonth && $availabletype) {
                $restapiData = array();
                //$restapiData['date'] = "";
                $restapiData['type'] = $availabletype;
                if ($availabletype == 'Specificschedule') {
                    $restapiData['start_time'] = $starttime;
                    $restapiData['end_time'] = $endtime;
                } else {
                    $restapiData['start_time'] = "";
                    $restapiData['end_time'] = "";
                }
                $restapiData['schedule_type'] = 'day';
                $restapiData['days'] = $weekday;
                $restapiData['noof_month'] = $noofmonth;

                $output = Yii::$app->ApiCallFunctions->StaffAddAvailability($restapiData);
                $data = array();
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                    return $this->redirect([$this->_staffPath . 'availability']);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Schedule not added. Please try again.'));
                    return $this->redirect([$this->_staffPath . 'availability']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Schedule not added. Please try again.'));
                return $this->redirect([$this->_staffPath . 'availability']);
            }
        }
        return $this->renderAjax('_recurrent_availability', [
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
                    'user' => $this->_user,
                    'profiledata' => $this->_profiledata,
                    'model' => $model
        ]);
    }

    public function actionEditavailability() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;

        $model = new Availability();
        $model->scenario = "update";
        if ($model->load(Yii::$app->request->post())) {
            $postdata = Yii::$app->request->post();
            $availability_id = $model->availability_id;
            $date = $model->date;
            $availabletype = $model->type;
            if ($model->type != "Specificschedule") {
                $model->starttime = "";
                $model->endtime = "";
            }
            $starttime = $model->starttime;
            $endtime = $model->endtime;
            if ($availability_id && $availabletype) {
                $restapiData = array();
                $restapiData['availability_id'] = $availability_id;
                $restapiData['date'] = $date;
                $restapiData['type'] = $availabletype;
                if ($availabletype == 'Specificschedule') {
                    $restapiData['start_time'] = $starttime;
                    $restapiData['end_time'] = $endtime;
                } else {
                    $restapiData['start_time'] = "";
                    $restapiData['end_time'] = "";
                }

                $output = Yii::$app->ApiCallFunctions->StaffUpdateRemoveAvailability($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                    return $this->redirect([$this->_staffPath . 'availability']);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Schedule not added. Please try again.'));
                    return $this->redirect([$this->_staffPath . 'availability']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Schedule not added. Please try again.'));
                return $this->redirect([$this->_staffPath . 'availability']);
            }
        }
        if (isset($_REQUEST['availabledata']) && !empty($_REQUEST['availabledata'])) {
            $availabledata = json_decode($_REQUEST['availabledata'], true);
            $model->date = $availabledata['date'];
            $model->type = $availabledata['type'];
            if ($model->type == "Specificschedule") {
                $model->starttime = $availabledata['start_time'];
                $model->endtime = $availabledata['end_time'];
            } else {
                $model->starttime = "";
                $model->endtime = "";
            }
            $model->availability_id = $availabledata['availability_id'];
            return $this->renderAjax('_edit_availability', [
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
                        'user' => $this->_user,
                        'profiledata' => $this->_profiledata,
                        'availabledata' => $availabledata,
                        'model' => $model
            ]);
        }
        return $this->redirect([$this->_staffPath . 'availability']);
    }

    public function actionEditavailabilityremove($id) {

        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;

        $output = array();
        $data = array();
        $message = '';
        $output['refreshpage'] = true;
        $id = Yii::$app->MyFunctions->decode($id);

        if ($id) {
            $restapiData = array();
            $restapiData['availability_id'] = $id;
            $restapiData['date'] = "";
            $restapiData['type'] = 'Removesingle';
            $restapiData['start_time'] = "";
            $restapiData['end_time'] = "";
            //$restapiData['month'] = '';
            $output = Yii::$app->ApiCallFunctions->StaffUpdateRemoveAvailability($restapiData);
            if ($output['status'] == 200) {
                $output['message'] = $output['message'];
                $output['refreshpage'] = true;
            } else {
                $output['message'] = Yii::t('app', 'Not update. Please try again.');
            }
        } else {
            $output['message'] = Yii::t('app', 'No update please try again.');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDeleteavailability() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;

        $model = new Availability();
        $model->scenario = "delete";
        if ($model->load(Yii::$app->request->post())) {
            $noofmonth = $model->noofmonth;
            if ($noofmonth) {
                $restapiData = array();
                $restapiData['availability_id'] = "Delete";
                //$restapiData['date'] = $noofmonth."-01";
                $restapiData['type'] = 'Removemonth';
                //$restapiData['start_time'] = "";
                //$restapiData['end_time'] = "";
                $restapiData['month'] = $noofmonth;
                $output = Yii::$app->ApiCallFunctions->StaffUpdateRemoveAvailability($restapiData);
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $output['message']);
                    return $this->redirect([$this->_staffPath . 'availability']);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Schedule not found data.'));
                    return $this->redirect([$this->_staffPath . 'availability']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Schedule not found data.'));
                return $this->redirect([$this->_staffPath . 'availability']);
            }
        }
        $months = array();
        $scheduled_month = array();
        $scheduled_month_data = \Yii::$app->request->post('scheduled_month', null);
        if ($scheduled_month_data) {
            for ($i = 0; $i < 12; $i++) {
                $date = explode('-', date('m-Y', strtotime('+ ' . $i . ' month')));
                $month = $date[0];
                $year = $date[1];
                $scheduled_month[$i]['word'] = Yii::t('app', date('F', strtotime('+ ' . $i . ' month'))) . '-' . date('Y', strtotime('+ ' . $i . ' month'));
                $scheduled_month[$i]['number'] = $year . '-' . $month;
            }
        }
        return $this->renderAjax('_delete_availability', [
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
                    'user' => $this->_user,
                    'profiledata' => $this->_profiledata,
                    'scheduled_month' => $scheduled_month,
                    'model' => $model
        ]);
    }
    
     public function actionProfile($loadcontent = "") {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforeStaffPath]);
        }
        $user = $this->_user;
        $Documentsmodel = new Documents();
        
        if ($loadcontent) {
            return $this->renderPartial('profile', [
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
                        'user' => $this->_user,
                        'profiledata' => $this->_profiledata,
                        'data' => $this->_profiledata,
                        'message' => $this->_profiledataMsg,
                        'Documentsmodel' => $Documentsmodel,
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
                        "_staffUrl" => $this->_staffUrl,
                        "_staffPath" => $this->_staffPath,
                        "_beforestaffUrl" => $this->_beforestaffUrl,
                        "_beforestaffPath" => $this->_beforestaffPath,
                        'user' => $this->_user,
                        'profiledata' => $this->_profiledata,
                        'data' => $this->_profiledata,
                        'message' => $this->_profiledataMsg,
                        'Documentsmodel' => $Documentsmodel,
            ]);
        }
    }
    
    public function actionEditprofileform() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $user = $this->_user;
        $title = "";
        $value = "";
        $staff_user_id = "";
        $data = array();
        $UserQuestions = array();
        $workexpData = array();
        $model = "";
        $usermodel = Yii::$app->RocGetObject->UserObject($user,$this->_profiledata);
        $staffmodel = Yii::$app->RocGetObject->StaffUserObject($user,$this->_profiledata);
        $message = "";
        if (isset($_REQUEST['staff_user_id']) && $_REQUEST['staff_user_id']) {
            $staff_user_id = $_REQUEST['staff_user_id'];
        }
        if (isset($_REQUEST['title']) && $_REQUEST['title']) {
            $title = $_REQUEST['title'];
        }
        if (isset($_REQUEST['value']) && $_REQUEST['value']) {
            $value = $_REQUEST['value'];
        }
         /* -----------Roc Start Api-------------------- */
        $userSoftwares = array();
        

        $UserSkill = array();
        
        /* -----------Roc End Api-------------------- */
        if ($title && $staff_user_id) {
            switch ($title) {
                case "email":
                    $usermodel->scenario = 'step1';
                    break;
                case "mobile":
                    $usermodel->scenario = 'step1';
                    break;
                case "user_software":
                    $staffmodel->scenario = 'step2';
                    $restapiData = array();
                    $restapiData['authkey'] = $user['authkey'];
                    $restapiData['language_id'] = $this->_langID;
                    $outputuserSoftwares = Yii::$app->ApiCallFunctions->userSoftwares($restapiData);
                    if ($outputuserSoftwares['status'] == 200) {
                        $data = $outputuserSoftwares['data'];
                    }
                    break;
                case "user_skill":
                    $staffmodel->scenario = 'step2';
                    $restapiData = array();
                    $restapiData['authkey'] = $user['authkey'];
                    $restapiData['language_id'] = $this->_langID;
                    $outputuserSkill = Yii::$app->ApiCallFunctions->UserSkill($restapiData);
                    if ($outputuserSkill['status'] == 200) {
                        $data = ArrayHelper::map($outputuserSkill['data'], 'skill_id', 'name');
                    }
                    break;    
                case "user_languages":
                    $output = Yii::$app->ApiCallFunctions->UserLanguages();
                    $data = array();
                    $message = $output['message'];
                    if ($output['status'] == 200) {
                        $data = $output['data'];
                    }
                    break;
                case "work_experience":
                    $restapiData = array();
                    $outputexp = Yii::$app->ApiCallFunctions->UserWorkExperience();
                    if ($outputexp['status'] == 200) {
                        $workexpData = $outputexp['data'];
                    }
                    $model = Yii::$app->RocGetObject->StaffWorkExperienceObject($user,$this->_profiledata);
                    if($model){
                        $model = $model;
                    }else{
                        $experience = new StaffWorkExperience();
                        $experience->scenario = 'step3';
                        $model = [$experience];
                    }
                    break;
                case "charge_sales_taxes":
                    $staffmodel->scenario = 'step4';
                    break;
                case "cover_latter":
                    break;
                case "":
                    $staffmodel->scenario = 'step4';
                    break;
                case "user_question6":
                    $restapiData = array();
                    $restapiData['step'] = 'step-6';
                    $restapiData['authkey'] = $user->authkey;
                    $restapiData['language_id'] = $this->_langID;
                    $restapiData['category_id'] = $staffmodel->category_id;
                    $output = Yii::$app->ApiCallFunctions->UserQuestions($restapiData);
                    $message = $output['message'];
                    if ($output['status'] == 200) {
                        $UserQuestions = $output['data'];
                    }
                    break;
                case "user_question7":
        
                    $restapiData = array();
                    $restapiData['step'] = 'step-7';
                    $restapiData['authkey'] = $user->authkey;
                    $restapiData['language_id'] = $this->_langID;
                    $restapiData['category_id'] = $staffmodel->category_id;
                    $output = Yii::$app->ApiCallFunctions->UserQuestions($restapiData);
                    $message = $output['message'];
                    if ($output['status'] == 200) {
                        $UserQuestions = $output['data'];
                    }
                    break;
                case "documents2":

                    break;
                case "documents5":
                    $model = new Documents();
                    $model->scenario = 'update';
                    break;
                default:
                    echo "Your favorite color is neither red, blue, nor green!";
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
                            "_staffUrl" => $this->_staffUrl,
                            "_staffPath" => $this->_staffPath,
                            "_beforestaffUrl" => $this->_beforestaffUrl,
                            "_beforestaffPath" => $this->_beforestaffPath,
                            'user' => $this->_user,
                            'profiledata' => $this->_profiledata,
                            'usermodel' => $usermodel,
                            'staffmodel' => $staffmodel,
                            'model' => $model,
                            'title' => $title,
                            'value' => $value,
                            'UserQuestions' => $UserQuestions,
                            'staff_user_id' => $staff_user_id,
                            'data' => $data,
                            'message' => $message,
                            'workexpData' => $workexpData,
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
        $staff_user_id = "";
        if (Yii::$app->request->post() && isset($_REQUEST['titlename']) && $_REQUEST['titlename']) {
            $postData = Yii::$app->request->post();

            $titlename = $postData['titlename'];
            if (isset($postData['User']) && $postData['User']) {
                $type = $postData['User']['type'];
            }
            if (isset($postData['staff_user_id']) && $postData['staff_user_id']) {
                $staff_user_id = $postData['staff_user_id'];
            }
            if ($titlename == "file_upload_profile") {
                $model = new Staffuser();
                $restapiData = array();
                $restapiData['type'] = 'Staff';
                $restapiData['staff_user_id'] = $staff_user_id;
                $restapiData['StaffUser[image_url]'] = Yii::$app->FrontFunctions->uploadedfiledata($model, 'image_url','profileimage');
                $checkFileExt = Yii::$app->FrontFunctions->checkfile($restapiData['StaffUser[image_url]'], 'image');
                if ($checkFileExt && empty($checkFileExt['result'])) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Only files with these extensions are allowed:') . " " . implode(", ", $checkFileExt['typeArrayExt']));
                    return $this->redirect([$this->_staffPath . 'profile']);
                }
                $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "documents5" || $titlename == "cv" || $titlename == "CV") {

                if (isset($postData['mode']) && $postData['mode']) {
                    $mode = $postData['mode'];
                    $document_id = "";
                    $FileDocuments = "";
                    if (isset($postData['document_id']) && $postData['document_id']) {
                        $document_id = $postData['document_id'];
                    }
                    $model = new Documents();
                    $restapiData = array();
                    if ($model->load(Yii::$app->request->post())) {
                        $restapiData['mode'] = $mode;
                        if ($document_id) {
                            $restapiData['document_id'] = $document_id;
                        }
                        $restapiData['Documents[relation_id]'] = $model->relation_id;
                        $restapiData['Documents[document_type]'] = $model->document_type;
                        $restapiData['Documents[expired_date]'] = "No expiration date";
                        $restapiData['Documents[title]'] = $model->title;
                        $restapiData['Documents[image_url]'] = Yii::$app->FrontFunctions->uploadedfiledata($model, 'image_url', str_replace("/", "+", $restapiData['Documents[title]']));
                        if ($titlename == "documents5") {
                            $checkFileExt = Yii::$app->FrontFunctions->checkfile($restapiData['Documents[image_url]'], 'image');
                            if ($checkFileExt && empty($checkFileExt['result'])) {
                                Yii::$app->session->setFlash('error', Yii::t('app', 'Only files with these extensions are allowed:') . " " . implode(", ", $checkFileExt['typeArrayExt']));
                                return $this->redirect([$this->_staffPath . 'profile']);
                            }
                        }
                        $output = Yii::$app->ApiCallFunctions->UpdateDocuments($restapiData);
                        if ($output['status'] == 200) {
                            $message = $output['message'];
                            $output['refreshpage'] = true;
                        }
                    }
                }
            } else if ($titlename == "email" || $titlename == "mobile") {
                $restapiData = array();
                $restapiData['type'] = $type;
                if ($titlename == "email") {
                    $restapiData['User[email]'] = $postData['User']['email'];
                }
                if ($titlename == "mobile") {
                    $restapiData['User[mobile]'] = $postData['User']['mobile'];
                }
                $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "user_software") {
                $restapiData = array();
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
                $restapiData['data_type'] = 'user_software';
                $restapiData['data_value'] = json_encode($user_software_array);
                $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "user_skill") {
                $restapiData = array();
                $user_skill_array = array();
                if (isset($postData['StaffUser']['user_skill']) && $postData['StaffUser']['user_skill']) {
                    foreach ($postData['StaffUser']['user_skill'] as $value) {
                        $level_of_knowledge = 'Expert';

                        $user_skill_array[] = array('relation_id' => $value, 'level_of_knowledge' => $level_of_knowledge);
                    }
                }
                $restapiData['data_type'] = 'user_skill';
                $restapiData['data_value'] = json_encode($user_skill_array);
                $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "user_languages") {
                $restapiData = array();
                $data_value = array();
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
                $restapiData['data_type'] = 'user_languages';
                $restapiData['data_value'] = json_encode($user_language_array);
                $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "work_experience") {
                $restapiData = array();
                $data_value = array();
                $StaffWorkExperience = array();
                $restapiData['data_type'] = 'staffworkexperience';
                if (isset($postData['StaffWorkExperience']) && is_array($postData['StaffWorkExperience'])) {
                    $postData['StaffWorkExperience'] = $postData['StaffWorkExperience'];
                    foreach ($postData['StaffWorkExperience'] as $exp) {
                        $exp['knownsw'] = implode(",", $exp['knownsw']);
                        $exp['is_verify'] = 'No';
                        $StaffWorkExperience[] = $exp;
                    }
                    $restapiData['data_value'] = json_encode($StaffWorkExperience);
                }else{
                    $restapiData['data_value'] = "";
                }
                $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "user_question6") {
                
                $model = new UserQuestionsAnswer();
                $model->scenario = 'step6';
                
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
                $restapiData['data_type'] = 'step-6';
                $restapiData['data_value'] = json_encode($dataArray);
                $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "user_question7") {
                $model = new UserQuestionsAnswer();
                $model->scenario = 'step7';
                
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
                $restapiData['data_type'] = 'step-7';
                $restapiData['data_value'] = json_encode($dataArray);
                $output = Yii::$app->ApiCallFunctions->updateProfileModule($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "charge_sales_taxes") {
                $restapiData = array();
                $restapiData['type'] = $type;
                $restapiData['StaffUser[charge_sales_taxes]'] = $postData['StaffUser']['charge_sales_taxes'];
                $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            } elseif ($titlename == "cover_latter") {
                $restapiData = array();
                $restapiData['type'] = $type;
                $restapiData['StaffUser[cover_latter]'] = $postData['StaffUser']['cover_latter'];
                $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
                if ($output['status'] == 200) {
                    $message = $output['message'];
                    $output['refreshpage'] = true;
                }
            }
            Yii::$app->session->setFlash('success', $message);
            return $this->redirect([$this->_staffPath . 'profile']);
//            if (Yii::$app->request->isAjax) {
//                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//                return $output;
//            }
        }
        return false;
    }
    
    public function actionAboutus() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforestaffPath]);
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
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'user' => $this->_user,
                    'data' => $data
        ]);
    }
    
    public function actionHistory() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $user = $this->_user;
        $output = Yii::$app->ApiCallFunctions->StaffGetPreviousSlotHistory();
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
                    "_staffUrl" => $this->_staffUrl,
                    "_staffPath" => $this->_staffPath,
                    "_beforestaffUrl" => $this->_beforestaffUrl,
                    "_beforestaffPath" => $this->_beforestaffPath,
                    'user' => $this->_user,
                    'profiledata' => $this->_profiledata,
                    'data' => $data,
                    'message' => $message,
        ]);
    }
    
    public function actionPromocode() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforestaffPath]);
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
                "_staffUrl" => $this->_staffUrl,
                "_staffPath" => $this->_staffPath,
                "_beforestaffUrl" => $this->_beforestaffUrl,
                "_beforestaffPath" => $this->_beforestaffPath,
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
                        "_staffUrl" => $this->_staffUrl,
                        "_staffPath" => $this->_staffPath,
                        "_beforestaffUrl" => $this->_beforestaffUrl,
                        "_beforestaffPath" => $this->_beforestaffPath,
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
                        "_staffUrl" => $this->_staffUrl,
                        "_staffPath" => $this->_staffPath,
                        "_beforestaffUrl" => $this->_beforestaffUrl,
                        "_beforestaffPath" => $this->_beforestaffPath,
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
            //return $this->redirect([$this->_moduleTypePath.'promocode']);
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
            return $this->redirect([$this->_beforestaffPath]);
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
            "_staffUrl" => $this->_staffUrl,
            "_staffPath" => $this->_staffPath,
            "_beforestaffUrl" => $this->_beforestaffUrl,
            "_beforestaffPath" => $this->_beforestaffPath,
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
            $output = Yii::$app->ApiCallFunctions->getNotificationList();
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
    
    public function actionSettings($loadcontent = "") {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_beforestaffPath]);
        }
        $user = $this->_user;
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
            'user' => $this->_user,
            'profiledata' => $this->_profiledata,
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
            $restapiData['type'] = 'User';
            $restapiData['User[preferred_language_id]'] = "1";
            if (isset($_REQUEST['preferred_lang_id']) && $_REQUEST['preferred_lang_id']) {
                $restapiData['User[preferred_language_id]'] = $_REQUEST['preferred_lang_id'];
            }
            if (isset($_REQUEST['preferred_lang_name']) && $_REQUEST['preferred_lang_name']) {
                $preferred_lang_name = $_REQUEST['preferred_lang_name'];
            }
            $output = Yii::$app->ApiCallFunctions->updateProfile($restapiData);
            if ($output['status'] == 200) {
                $output['message'] = $output['message'];
                $output['refreshpage'] = true;
                $output['preferred_lang_name'] = $preferred_lang_name;
            }
        } elseif ($partoftype == "add_card") {
            $model = new OwnerCard();
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate()) {
                    $restapiData['OwnerCard[number]'] = $model->number;
                    $restapiData['OwnerCard[exp_month]'] = $model->exp_month;
                    $restapiData['OwnerCard[exp_year]'] = $model->exp_year;
                    $restapiData['OwnerCard[cvc]'] = $model->cvc;
                    $restapiData['OwnerCard[card_name]'] = $model->card_name;
                    $restapiData['OwnerCard[owner_user_id]'] = $model->owner_user_id;

                    $output = Yii::$app->ApiCallFunctions->ownerAddStripeCard($restapiData);
                    if ($output['status'] == 200) {
                        $output['message'] = $output['message'];
                    } else {
                        $output['message'] = $output['message'];
                    }
                } else {
                    $output['message'] = Yii::t('app', 'something went wrong');
                }
            } else {
                $output['message'] = Yii::t('app', 'something went wrong');
            }
        } else {
            $output['message'] = Yii::t('app', 'something went wrong');
        }
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $output;
        }
    }
    
}
