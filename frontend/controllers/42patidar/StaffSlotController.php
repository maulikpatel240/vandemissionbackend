<?php
namespace frontend\controllers\nursing;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\nursing\User;
use frontend\models\nursing\Appendcontent;
use frontend\models\nursing\Myfunction;
use frontend\models\nursing\Slot;
use frontend\models\nursing\Slotcancellation;
use frontend\models\nursing\SlotCancelAttachment;
use frontend\models\nursing\SlotModifications;
use frontend\models\nursing\SlotItems;
use frontend\models\nursing\SlotAttachment;
use frontend\models\nursing\Model;
/**
 * Slot controller
 */
class StaffSlotController extends Controller
{
    public $_user = array();
    public $_baseUrl = "/";
    public $_module = 'nursing';
    public $_moduleID = '5';
    public $_moduleType = "staff";
    public $_moduleTypePath = '/nursing/staff-slot/';
    public $_moduleTypeUrl = "/";
    public $_moduleTypePathStaff = '/nursing/staff/';
    public $_moduleTypeUrlStaff = '/';
    public $_lang = "en";
    public $_langID = 1;
    public $_profiledata = "";
    public $_profiledataMsg = "";
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
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

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
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
    public function init() {
        parent::init();
        if (Yii::$app->user->identity) {
            $this->_moduleType = Yii::$app->user->identity->type == 'Owner' ? 'owner' : 'staff';
            $this->_user = User::findOne(['user_id' => Yii::$app->user->identity->user_id, 'authkey' => Yii::$app->user->identity->authkey, 'type' => Yii::$app->user->identity->type,'module_id'=>$this->_moduleID]);
            if (!$this->_user) {
                Yii::$app->user->logout();
                return $this->redirect([$this->_moduleTypePathStaff . 'login']);
            }
            $profileoutput = Yii::$app->ApiCallFunctions->GetProfileApi();
            $this->_profiledataMsg = $profileoutput['message'];
            if ($profileoutput['status'] == 200) {
                $this->_profiledata = $profileoutput['data'];
            }
            $this->_moduleType = Yii::$app->user->identity->type == 'Owner' ? 'owner' : 'staff';
            $this->_moduleID = $this->_user->module_id;
        }
        
        $this->_module = Yii::$app->params['module-structure'][$this->_moduleID];
        
        $this->_lang = Yii::$app->FrontFunctions->defaultlanguage();
        $this->_langID = Yii::$app->FrontFunctions->defaultlanguage(true);
        $this->_baseUrl = Yii::$app->request->baseUrl;
        $this->_moduleTypePath = '/'.$this->_module.'/staff-slot/';
        $this->_moduleTypeUrl = Yii::$app->params[$this->_module.'Url'] . 'staff-slot/';
        $this->_moduleTypePathStaff = '/'.$this->_module.'/'.$this->_moduleType.'/';
        $this->_moduleTypeUrlStaff = Yii::$app->params[$this->_module.'Url'] . ''.$this->_moduleType.'/';
    }
    
    public function beforeAction($action) {
        $cookiedestory = Yii::$app->FrontFunctions->cookiedestory();
        if(isset($cookiedestory['result']) && isset($cookiedestory['type']) && $cookiedestory['result']) {
            $this->_moduleType = $cookiedestory['type'];
            Yii::$app->user->logout();
            return $this->redirect([$this->_moduleTypePathStaff . 'login']);
        }
        $this->_lang = Yii::$app->FrontFunctions->defaultlanguage();
        $this->_langID = Yii::$app->FrontFunctions->defaultlanguage(true);
        if (!empty(Yii::$app->user->identity)) {
            $this->_moduleType = Yii::$app->user->identity->type == 'Owner' ? 'owner' : 'staff';
            $user = User::findOne(['user_id' => Yii::$app->user->identity->user_id, 'authkey' => Yii::$app->user->identity->authkey, 'type' => Yii::$app->user->identity->type,'module_id'=>$this->_moduleID]);
            if (!$user) {
                Yii::$app->user->logout();
                return $this->redirect([$this->_moduleTypePathStaff . 'login']);
            }
            //echo $this->action->id; exit;
            $continue = true;
            if($this->_moduleType == "owner"){
                $continue = false;
            }
            if(empty($continue)){
               throw new \yii\web\HttpException(404, Yii::t('app', 'Page not found.')); 
               exit;
            }
        }
        return parent::beforeAction($action);
    }
    
    public function actionDifferencetime() {
        $this->layout = 'siteblank';
        try {
            $available_lcum_date = Yii::$app->request->post('date', null);
            $starttime = Yii::$app->request->post('start', null);
            $endtime = Yii::$app->request->post('end', null);
            $slot_hour_price = Yii::$app->request->post('slot_hour_price', null);
            $slot_type = Yii::$app->request->post('slot_type', null);
            $slot_id = Yii::$app->request->post('slot_id', null);

            if (isset($_REQUEST['accomand_fees']) && $_REQUEST['accomand_fees']) {
                $accommodation_fees = $_REQUEST['accomand_fees'];
            } else {
                $accommodation_fees = 0;
            }
            if (isset($_REQUEST['travel_fees']) && $_REQUEST['travel_fees']) {
                $travel_fees = $_REQUEST['travel_fees'];
            } else {
                $travel_fees = 0;
            }
            if (isset($_REQUEST['totalsum_modifiedprice']) && $_REQUEST['totalsum_modifiedprice']) {
                $totalsum_modifiedprice = $_REQUEST['totalsum_modifiedprice'];
            } else {
                $totalsum_modifiedprice = 0;
            }
            if (isset($_REQUEST['taxdata']) && $_REQUEST['taxdata']) {
                $taxdata = $_REQUEST['taxdata'];
            } else {
                $taxdata = "";
            }
            

            if (!empty($available_lcum_date) && !empty($starttime) && !empty($endtime) && !empty($slot_type) && !empty($slot_hour_price)) {

                //Single slot
                if ($slot_type == 1) {
                    $time1 = $endtime;
                    $time2 = $starttime;

                    $diff = abs(strtotime($time1) - strtotime($time2));

                    $tmins = $diff / 60;

                    $hours = floor($tmins / 60);

                    $mins = $tmins % 60;

                    $finaldifftime = $hours . ':' . $mins;
                    // mulitple slot
                } elseif ($slot_type == 2) {
                    $multi_difftime = array();

                    if (is_array($available_lcum_date) && is_array($starttime) && is_array($endtime)) {
                        if ($available_lcum_date) {
                            for ($i = 0; $i < count($available_lcum_date); $i++) {
                                $time1 = $endtime[$i];
                                $time2 = $starttime[$i];

                                $diff = abs(strtotime($time1) - strtotime($time2));

                                $tmins = $diff / 60;

                                $hours = floor($tmins / 60);

                                $mins = $tmins % 60;

                                $multi_difftime[] = $hours . ':' . $mins;
                            }
                        }
                    }
                    $finaldifftime = Yii::$app->MyFunctions->calculatetime($multi_difftime);
                }

                $iCostPerHour = $slot_hour_price;
                $timespent = $finaldifftime;
                $timeparts = explode(':', $timespent);
                $pay = $timeparts[0] * $iCostPerHour + $timeparts[1] / 60 * $iCostPerHour;

                $totalrate = $pay;
                $total_available_lcum_date = ($slot_type == 2) ? count($available_lcum_date) : 1;

                $Slot_sub_total_price = round($totalrate + ($accommodation_fees * $total_available_lcum_date) + $travel_fees, 2);
                
                if($taxdata){
                    $taxdata_json = json_decode($taxdata,true);
                    if($taxdata_json){
                        //Total hour+ travel fees+ accommandation
                        if($Slot_sub_total_price){
                            $Slot_tax_gst_total = (($Slot_sub_total_price*$taxdata_json['staff_gst_precentage'])/100);
                            $Slot_tax_hst_total = (($Slot_sub_total_price*$taxdata_json['staff_hst_precentage'])/100);
                            $Slot_tax_pst_total = (($Slot_sub_total_price*$taxdata_json['staff_pst_precentage'])/100);
                            $Slot_tax_qst_total = (($Slot_sub_total_price*$taxdata_json['staff_qst_precentage'])/100);
                         }else{
                            $Slot_tax_gst_total = 0;
                            $Slot_tax_hst_total = 0;
                            $Slot_tax_pst_total = 0;
                            $Slot_tax_qst_total = 0;  
                        }
                        $total_tax = $Slot_tax_gst_total + $Slot_tax_hst_total + $Slot_tax_pst_total + $Slot_tax_qst_total;
                        
                        //Item
                        $Slot_tax_gst = (($totalsum_modifiedprice*$taxdata_json['staff_gst_precentage'])/100);
                        $Slot_tax_hst = (($totalsum_modifiedprice*$taxdata_json['staff_hst_precentage'])/100);
                        $Slot_tax_pst = (($totalsum_modifiedprice*$taxdata_json['staff_pst_precentage'])/100);
                        $Slot_tax_qst = (($totalsum_modifiedprice*$taxdata_json['staff_qst_precentage'])/100);
                        
                        $item_tax = $Slot_tax_gst + $Slot_tax_hst + $Slot_tax_pst + $Slot_tax_qst;
                    }else{
                        $Slot_tax_gst = 0;
                        $Slot_tax_hst = 0;
                        $Slot_tax_pst = 0;
                        $Slot_tax_qst = 0; 
                        
                        $Slot_tax_gst_total = 0;
                        $Slot_tax_hst_total = 0;
                        $Slot_tax_pst_total = 0;
                        $Slot_tax_qst_total = 0;  
                        
                        $total_tax = 0;
                        $item_tax = 0;
                    }
                }else{
                    $Slot_tax_gst = 0;
                    $Slot_tax_hst = 0;
                    $Slot_tax_pst = 0;
                    $Slot_tax_qst = 0;
                    
                    $Slot_tax_gst_total = 0;
                    $Slot_tax_hst_total = 0;
                    $Slot_tax_pst_total = 0;
                    $Slot_tax_qst_total = 0; 
                    
                    $total_tax = 0;
                    $item_tax = 0;
                }
                
                $Slot_total_price = round($Slot_sub_total_price + $totalsum_modifiedprice + $total_tax + $item_tax, 2);
                //echo $Slot_sub_total_price." = ".$total_tax." = ".$item_tax.' = '.$totalsum_modifiedprice;

                if ($Slot_total_price) {
                    $modifiedtotal = $Slot_total_price;
                } else {
                    $modifiedtotal = 0;
                }
                $result['message'] = array('success' => 1, 'difftime' => $finaldifftime, "rate" => $slot_hour_price, "totalrate" => $totalrate, "modifiedtotal" => $modifiedtotal);
            } else {
                $result['message'] = array('success' => 0, 'errorData' => "");
            }
        } catch (\Exception $e) {
            $result['message'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
        }

        echo json_encode($result);
    }
    
    public function actionRequestcancellation() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_moduleTypePathStaff . 'login']);
        }
        $user = $this->_user;
        $slot_id = "";
        if (isset($_REQUEST['slot_id']) && $_REQUEST['slot_id']) {
            $slot_id = Yii::$app->MyFunctions->decode($_REQUEST['slot_id']);
        }
        $btncancel = "";
        if (isset($_REQUEST['btncancel']) && $_REQUEST['btncancel']) {
            $btncancel = $_REQUEST['btncancel'];
        }
        $userModulesetting = "";
        if (isset($_SESSION['UserModulesetting']) && $_SESSION['UserModulesetting']) {
            $userModulesetting = $_SESSION['UserModulesetting'];
        }
        if (empty($slot_id) || (isset($userModulesetting['SlotCancellation']) && $userModulesetting['SlotCancellation'] == "No")) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Slots id or SlotCancellation disabled.'));
            return $this->redirect([$this->_moduleTypePathStaff . 'dashboard']);
        }
        $model = new Slotcancellation();
        $SlotAttachment = new SlotCancelAttachment();
        $modelAttachment = [$SlotAttachment];
        $model->option_type = 1;
        $restapiData = array();
        $restapiData['slot_id'] = $slot_id;
        $output = Yii::$app->ApiCallFunctions->getSingleSlotsDetails($restapiData);
        $data = array();
        $slotcancellation = array();
        $message = $output['message'];
        if ($output['status'] == 200) {
            $data = $output['data'];
            if (isset($data['slotcancellation']) && $data['slotcancellation']) {
                $slotcancellation = $data['slotcancellation'];
                $model->option_type = $slotcancellation['option_type'];
                $model->comment = $slotcancellation['comment'];
                $modelAttachment = SlotCancelAttachment::find()->where(['slot_cancellation_id' => $slotcancellation['slot_cancellation_id'], 'slot_id' => $slot_id])->all();
            }
        }
       
        $renderdata = array(
            "baseUrl" => $this->_baseUrl,
            "moduleID" => $this->_moduleID,
            "module" => $this->_module,
            "moduleType" => $this->_moduleType,
            "moduleTypePath" => $this->_moduleTypePath,
            "moduleTypeUrl" => $this->_moduleTypeUrl,
            "moduleTypePathStaff" => $this->_moduleTypePathStaff,
            "moduleTypeUrlStaff" => $this->_moduleTypeUrlStaff,
            "lang" => $this->_lang,
            "langID" => $this->_langID,
            'user' => $this->_user,
            'profiledata' => $this->_profiledata,
            'data' => $data,
            'model' => $model,
            'modelAttachment' => (empty($modelAttachment)) ? [$SlotAttachment] : $modelAttachment,
            'slot_id' => $slot_id,
            'slotcancellation' => $slotcancellation,
            'userModulesetting' => $userModulesetting,
        );
        if ($slotcancellation) {
            if ($model->load(Yii::$app->request->post()) && $slot_id) {
                $model->slot_cancel_request_status = Yii::$app->MyFunctions->decode($model->slot_cancel_request_status);
                if($model->slot_cancel_request_status == 2){
                    if($btncancel == "Approve"){
                        $model->slot_cancel_request_status = 3;
                    }elseif($btncancel == "Reject"){
                        $model->slot_cancel_request_status = 4;
                    }elseif($btncancel == "Remove"){
                        $model->slot_cancel_request_status = 5;
                    }
                }
                $restapiData = array();
                $restapiData['slot_cancellation_id'] = $slotcancellation['slot_cancellation_id'];
                $restapiData['slot_cancel_request_status'] = $model->slot_cancel_request_status;
                $restapiData['cancel_fee'] = $model->cancel_fee;
                
                $output = Yii::$app->ApiCallFunctions->ChangeSlotCancelRequestStatus($restapiData);
                $message = $output['message'];
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $message);
                } else {
                    Yii::$app->session->setFlash('error', $message);
                }
                return $this->redirect([$this->_moduleTypePath . 'requestcancellation', 'slot_id' => Yii::$app->MyFunctions->encode($slot_id)]);
            }
            return $this->render('requestedcancellation', $renderdata);
        } else {
            if ($model->load(Yii::$app->request->post()) && $slot_id) {
                $postData = Yii::$app->request->post();
                $restapiData = array();
                $restapiData['SlotCancellation[slots_id]'] = $model->slots_id;
                $restapiData['SlotCancellation[comment]'] = $model->comment;
                $restapiData['SlotCancellation[option_type]'] = $model->option_type;
                $restapiData['SlotCancellation[slot_id]'] = $slot_id;
                
                $modelAttachment = Model::createMultiple(SlotAttachment::classname());

                Model::loadMultiple($modelAttachment, Yii::$app->request->post());

                $i = 0;
                foreach ($modelAttachment as $index => $modelOptionValue) {
                    $attachmentdata = UploadedFile::getInstance($modelOptionValue, "[{$index}]attachment_url");
                    $explodeext = explode(".", $attachmentdata->name);
                    if ($explodeext) {
                        $ext = end($explodeext);
                        $attachment['name'] = $modelOptionValue->name . "." . $ext;
                        $attachment['type'] = $attachmentdata->type;
                        $attachment['tmp_name'] = $attachmentdata->tempName;
                        $attachment['error'] = $attachmentdata->error;
                        $attachment['size'] = $attachmentdata->size;
                        $restapiData['attachment[' . $i . ']'] = new \CURLFile($attachmentdata->tempName, $attachmentdata->type, $attachmentdata->name);
                        $i++;
                    }
                }
                $output = Yii::$app->ApiCallFunctions->CancelSlotRequest($restapiData);
                $message = $output['message'];
                if ($output['status'] == 200) {
                    Yii::$app->session->setFlash('success', $message);
                } else {
                    Yii::$app->session->setFlash('error', $message);
                }
                return $this->redirect([$this->_moduleTypePath . 'requestcancellation', 'slot_id' => Yii::$app->MyFunctions->encode($slot_id)]);
            }
            return $this->render('requestcancellation', $renderdata);
        }
    }
    public function actionRequestmodification(){
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_moduleTypePathStaff . 'login']);
        }
        $user = $this->_user;
        $slot_id = "";
        if (isset($_REQUEST['slot_id']) && $_REQUEST['slot_id']) {
            $slot_id = Yii::$app->MyFunctions->decode($_REQUEST['slot_id']);
        }
        $slots_id = "";
        if (isset($_REQUEST['slots_id']) && $_REQUEST['slots_id']) {
            $slots_id = Yii::$app->MyFunctions->decode($_REQUEST['slots_id']);
        }
        $btncancel = "";
        if (isset($_REQUEST['btncancel']) && $_REQUEST['btncancel']) {
            $btncancel = $_REQUEST['btncancel'];
        }
        $userModulesetting = "";
        if (isset($_SESSION['UserModulesetting']) && $_SESSION['UserModulesetting']) {
            $userModulesetting = $_SESSION['UserModulesetting'];
        }
        if (empty($slot_id) || (isset($userModulesetting['slotModification']) && $userModulesetting['slotModification'] == "No")) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Slots id or slotModification disabled.'));
            return $this->redirect([$this->_moduleTypePathStaff . 'dashboard']);
        }
        $slotdata = array();
        $restapiData = array();
        $restapiData['slot_id'] = $slot_id;
        $outputmodification = Yii::$app->ApiCallFunctions->GetModifiedSlotDetails($restapiData);
        $slotmodifications = array();
        $message = $outputmodification['message'];
        if ($outputmodification['status'] == 200) {
            $slotdata = $outputmodification['data'];
           
            $slotmodificationsData = $outputmodification['slotmodificationdata'];
            if(isset($slotmodificationsData['slot_id']) && $slotmodificationsData['slot_id']){
                $slotmodifications = $slotmodificationsData;
                if($slotmodifications && $slotmodifications['change_request_status'] == 2){
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Slots id already requested.'));
                    return $this->redirect([$this->_moduleTypePathStaff . 'slotdetails', 'slot_id' => Yii::$app->MyFunctions->encode($slot_id)]);
                }
            }else{
                $slotmodifications = "";
            } 
        }
        if(empty($slotdata)){
            $restapiData = array();
            $restapiData['slot_id'] = $slot_id;
            $output = Yii::$app->ApiCallFunctions->getSingleSlotsDetails($restapiData);
            $slotdata = array();
            $message = $output['message'];
            if ($output['status'] == 200) {
                $slotdata = $output['data'];
            }
        }
        $SlotAttachmentdata = new SlotAttachment();
        $modelAttachmentdata = [$SlotAttachmentdata];
        $model = SlotModifications::find()->where(['slot_id'=>$slot_id,'change_request_status' => 3])->orderBy(['slot_modifications_id' => SORT_DESC])->one();
        if(empty($model)){
            $model = new SlotModifications();
            $model->scenario = 'slotmodificationadd';
            $SlotAttachment = new SlotAttachment();
            $modelAttachment = [$SlotAttachment];
            
            $SlotItems = new SlotItems();
            $modelItems = [$SlotItems];
        }else{
            $model->scenario = 'slotmodificationupdate';
            $SlotAttachment = array();
            if ($model->attachment) {
                $attachment = explode(',', $model->attachment);
                $modelAttachment = SlotAttachment::find()->andWhere(['slot_id' => $model->slot_id])->andWhere(['in', 'slot_attachment_id', $attachment])->all();
            }else{
                $modelAttachment = array();
            }
            
            if ($model->change_request_sender == 'Staff' && $model->item_id_staff) {
                $item_id = explode(',', $model->item_id_staff);
            } else if ($model->change_request_sender == 'Owner' && $model->item_id_owner) {
                $item_id = explode(',', $model->item_id_owner);
            } else {
                $item_id = array();
            }
            if ($item_id) {
                $modelItems = SlotItems::find()->andWhere(['slot_id' => $model->slot_id,'type'=>"No"])->andWhere(['in', 'item_id', $item_id])->all();
                $sumitemtotal = SlotItems::find()->andWhere(['slot_id' => $model->slot_id,'type'=>"No"])->andWhere(['in', 'item_id', $item_id])->sum('item_total');
            } else {
                $modelItems = array();
                $sumitemtotal = 0;
            }
            
            if(empty($modelAttachment)){
                $SlotAttachment = new SlotAttachment();
                $modelAttachment = [$SlotAttachment];
            }
            if(empty($modelItems)){
                $SlotItems = new SlotItems();
                $modelItems = [$SlotItems];
            } 
        }
        
        if($slotdata && isset($slotdata['type'])){
            $model->type = $slotdata['type'];
        }
        $SlotItemsbackend = SlotItems::find()->where(['slot_id'=>$slot_id,'add_in' => 'Staff'])->all();
        
        $renderdata = array(
            "baseUrl" => $this->_baseUrl,
            "moduleID" => $this->_moduleID,
            "module" => $this->_module,
            "moduleType" => $this->_moduleType,
            "moduleTypePath" => $this->_moduleTypePath,
            "moduleTypeUrl" => $this->_moduleTypeUrl,
            "moduleTypePathStaff" => $this->_moduleTypePathStaff,
            "moduleTypeUrlStaff" => $this->_moduleTypeUrlStaff,
            "lang" => $this->_lang,
            "langID" => $this->_langID,
            'user' => $this->_user,
            'profiledata' => $this->_profiledata,
            'slotdata' => $slotdata,
            'model' => $model,
            'modelItems' => (empty($modelItems)) ? [$SlotItems] : $modelItems,
            'modelAttachment' => (empty($modelAttachment)) ? [$SlotAttachment] : $modelAttachment,
            'SlotItemsbackend' => $SlotItemsbackend,
            'slot_id' => $slot_id,
            'slotmodifications' => $slotmodifications,
            'userModulesetting' => $userModulesetting,
            'SlotAttachmentdata' => (empty($modelAttachmentdata)) ? [$SlotAttachmentdata] : $modelAttachmentdata,
        );
        if ($model->load(Yii::$app->request->post()) && $slot_id) {
            $postData = Yii::$app->request->post();

            $prefix = 'SlotModifications';
            $slot_type = ($postData[$prefix]['type'] == '2') ? 'Multidays' : 'Singleday';
            $postData[$prefix]['type'] = $slot_type;

            $removeKeyArr = array('selectDateSingle','single_date','single_date_starttime', 'single_date_endtime', 'selectDateMultidate_start','selectDateMultidate_end', 'multiple_date', 'multiple_date_starttime', 'multiple_date_endtime');

            $date_name = ($slot_type == 'Multidays') ? 'multiple_date' : 'single_date';
            $start_time_name = ($slot_type == 'Multidays') ? 'multiple_date_starttime' : 'single_date_starttime';
            $end_time_name = ($slot_type == 'Multidays') ? 'multiple_date_endtime' : 'single_date_endtime';
            $start_datetime = array();
            $end_datetime = array();
            if($postData[$prefix][$date_name]) {
                foreach ($postData[$prefix][$date_name] as $key => $value) {
                    $start_datetime[] = $value.' '.$postData[$prefix][$start_time_name][$key].':00';
                    $end_datetime[] = $value.' '.$postData[$prefix][$end_time_name][$key].':00';
                }
            }
            $postData[$prefix]['start_datetime'] = "";
            $postData[$prefix]['end_datetime'] = "";
            
            $postData[$prefix]['slot_id'] = $slot_id;
            $postData[$prefix]['number_of_day'] = count($postData[$prefix][$date_name]);
            if($start_datetime){
                $postData[$prefix]['start_datetime'] = implode(",",$start_datetime);
            }
            if($end_datetime){
                $postData[$prefix]['end_datetime'] = implode(",",$end_datetime);
            }


            if(isset($postData[$prefix]['travel']) && $postData[$prefix]['travel']){
                $postData[$prefix]['travel'] = $postData[$prefix]['travel'];
                if($postData[$prefix]['travel'] == "Fees" || $postData[$prefix]['accommodation'] == 'StaffFees'){
                    if(isset($postData[$prefix]['travel_fees']) && $postData[$prefix]['travel_fees']){
                        $postData[$prefix]['travel_fees'] = $postData[$prefix]['travel_fees'];
                    }else{
                        $postData[$prefix]['travel_fees'] = '00.00';
                    }
                }else{
                    $postData[$prefix]['travel_fees'] = '00.00';
                }
            }else{
                $postData[$prefix]['travel'] = 'No';
                $postData[$prefix]['travel_fees'] = '00.00';
            }


            if(isset($postData[$prefix]['accommodation']) && $postData[$prefix]['accommodation']){
                $postData[$prefix]['accommodation'] = $postData[$prefix]['accommodation'];
                if(isset($postData[$prefix]['accommodation_fees']) && $postData[$prefix]['accommodation_fees'] == "Address"){
                    $postData[$prefix]['accommodation'] = "Address";
                    $postData[$prefix]['accommodation_fees'] = '00.00';
                    $postData[$prefix]['accommodation_address'] = $postData[$prefix]['accommodation_address'];
                }elseif($postData[$prefix]['accommodation'] == "Fees" || $postData[$prefix]['accommodation'] == 'StaffFees'){
                    $postData[$prefix]['accommodation'] = $postData[$prefix]['accommodation'];
                    $postData[$prefix]['accommodation_fees'] = ($postData[$prefix]['accommodation_fees'] * $postData[$prefix]['number_of_day']);
                    $postData[$prefix]['accommodation_address'] = '';
                }else{
                    $postData[$prefix]['accommodation_fees'] = '00.00';
                    $postData[$prefix]['accommodation_address'] = '';
                }
            }else{
                $postData[$prefix]['accommodation'] = 'No';
                $postData[$prefix]['accommodation_fees'] = '00.00';
                $postData[$prefix]['accommodation_address'] = '';
            }
            $restapiData = array();
            if(isset($postData[$prefix]) && $postData[$prefix]) {
                foreach ($postData[$prefix] as $key => $value) {
                    if(!in_array($key,$removeKeyArr))
                    {
                        $restapiData[$prefix.'['.$key.']'] = $value;
                    }
                }
            }
            //$restapiData['owner_user_id'] = $slotdata['owner_user_id'];
            //$restapiData['staff_user_id'] = $slotdata['staff_user_id'];
            //$restapiData['change_request_sender'] = 'Staff';

            $restapiData['last_modify_from'] = 'Staff-Web';
            if(isset($postData['SlotItems']) && $postData['SlotItems']){
                $restapiData['slot_items'] = json_encode($postData['SlotItems']);
            }
            if(isset($postData[$prefix]['removeattachmentid']) && $postData[$prefix]['removeattachmentid']){
                $restapiData['removeattachmentid'] = $postData[$prefix]['removeattachmentid'];
            }
            $modelAttachment = Model::createMultiple(SlotAttachment::classname());

            Model::loadMultiple($modelAttachment, Yii::$app->request->post());

            $i = 0;
            foreach ($modelAttachment as $index => $modelOptionValue) {
                $attachmentdata = UploadedFile::getInstance($modelOptionValue, "[{$index}]attachment_url");
                if($attachmentdata){
                    $explodeext = explode(".", $attachmentdata->name);
                    if ($explodeext) {
                        $ext = end($explodeext);
                        $attachment['name'] = $modelOptionValue->name . "." . $ext;
                        $attachment['type'] = $attachmentdata->type;
                        $attachment['tmp_name'] = $attachmentdata->tempName;
                        $attachment['error'] = $attachmentdata->error;
                        $attachment['size'] = $attachmentdata->size;
                        $restapiData['attachment[' . $i . ']'] = new \CURLFile($attachmentdata->tempName, $attachmentdata->type, $attachment['name']);
                        $i++;
                    }
                }
            }

            //echo "<pre>"; print_r($restapiData);exit;
            $output = Yii::$app->ApiCallFunctions->SlotModificationRequestSend($restapiData);
            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', $message);
            }
            return $this->redirect([$this->_moduleTypePath . 'historymodification', 'slot_id' => Yii::$app->MyFunctions->encode($slot_id)]);
        }
        return $this->render('requestmodification', $renderdata);
    }
    
    public function actionHistorymodification(){
        if (Yii::$app->user->isGuest) {
            return $this->redirect([$this->_moduleTypePathStaff . 'login']);
        }
        $user = $this->_user;
       
        $userModulesetting = "";
        if (isset($_SESSION['UserModulesetting']) && $_SESSION['UserModulesetting']) {
            $userModulesetting = $_SESSION['UserModulesetting'];
        }
        $slot_id = "";
        if (isset($_REQUEST['slot_id']) && $_REQUEST['slot_id']) {
            $slot_id = Yii::$app->MyFunctions->decode($_REQUEST['slot_id']);
        }
        $slots_id = "";
        if (isset($_REQUEST['slots_id']) && $_REQUEST['slots_id']) {
            $slots_id = Yii::$app->MyFunctions->decode($_REQUEST['slots_id']);
        }
        $btncancel = "";
        if (isset($_REQUEST['btncancel']) && $_REQUEST['btncancel']) {
            $btncancel = $_REQUEST['btncancel'];
        }
        if (empty($slot_id) || (isset($userModulesetting['slotModification']) && $userModulesetting['slotModification'] == "No")) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Slots id or slotModification disabled.'));
            return $this->redirect([$this->_moduleTypePathStaff . 'dashboard']);
        }
        $restapiData = array();
        $restapiData['slot_id'] = $slot_id;
        $output = Yii::$app->ApiCallFunctions->getSingleSlotsDetails($restapiData);
        $slotdata = array();
        $slotcancellation = array();
        $message = $output['message'];
        if ($output['status'] == 200) {
            $slotdata = $output['data'];
        }
        
        $restapiData = array();
        $restapiData['slot_id'] = $slot_id;
        $outputhistory = Yii::$app->ApiCallFunctions->GetModifiedSlotHistory($restapiData);
        $slothistorydata = array();
        $message = $outputhistory['message'];
        if ($outputhistory['status'] == 200) {
            $slothistorydata = $outputhistory['data'];
        }
        $model = SlotModifications::find()->where(['slot_id'=>$slot_id])->orderBy(['slot_modifications_id' => SORT_DESC])->one();
        if(empty($model)){
            $model = new SlotModifications();
        }
        if ($model->load(Yii::$app->request->post()) && $slot_id) {
            $model->change_request_status = Yii::$app->MyFunctions->decode($model->change_request_status);
            if($model->change_request_status == 2){
                if($btncancel == "Approve"){
                    $model->change_request_status = 3;
                }elseif($btncancel == "Reject"){
                    $model->change_request_status = 4;
                }elseif($btncancel == "Remove"){
                    $model->change_request_status = 5;
                }
            }
            $restapiData = array();
            $restapiData['slot_id'] = $slot_id;
            $restapiData['change_request_status'] = $model->change_request_status;
            
            if($btncancel == "Remove"){
                $output = Yii::$app->ApiCallFunctions->CancelModifiedSlotRequest($restapiData);
            }else{
                $output = Yii::$app->ApiCallFunctions->AcceptRejectModifiedSlotRequest($restapiData);
            }
            $message = $output['message'];
            if ($output['status'] == 200) {
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', $message);
            }
            return $this->redirect([$this->_moduleTypePath . 'historymodification', 'slot_id' => Yii::$app->MyFunctions->encode($slot_id)]);
        }
        $renderdata = array(
            "baseUrl" => $this->_baseUrl,
            "moduleID" => $this->_moduleID,
            "module" => $this->_module,
            "moduleType" => $this->_moduleType,
            "moduleTypePath" => $this->_moduleTypePath,
            "moduleTypeUrl" => $this->_moduleTypeUrl,
            "moduleTypePathStaff"=> $this->_moduleTypePathStaff,
            "moduleTypeUrlStaff" => $this->_moduleTypeUrlStaff,
            "lang" => $this->_lang,
            "langID" => $this->_langID,
            'user' => $this->_user,
            'profiledata' => $this->_profiledata,
            'slotdata' => $slotdata,
            'slothistorydata' => $slothistorydata,
            'slot_id' => $slot_id,
            'slots_id' => $slots_id,
            'model' => $model,
            'userModulesetting' => $userModulesetting,
        );
        if($slots_id){
            return $this->render('historymodificationdetails', $renderdata);
        }else{
            return $this->render('historymodification', $renderdata);
        }
    }
}
