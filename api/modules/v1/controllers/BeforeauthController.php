<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

use common\models\AppConfigurations;
use common\models\AppLanguages;
use common\models\AppModules;
use common\models\AppCategory;
use common\models\AppStaticpage;
use common\models\AppEmailTemplate;

use common\models\User;
use common\models\UserDevice;

use common\models\StaffUser;

use common\models\OwnerUser;





class BeforeauthController extends Controller
{
	public function beforeAction($action)
    {        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;

        Yii::$app->language ='en-US';
        if(!empty($_REQUEST['language_id']))
        {
            $language = AppLanguages::find()->select(['key_backend'])->andWhere(['status'=>'Active','is_deleted'=>'No','language_id'=>$_REQUEST['language_id']])->one();
            if(!empty($language)){ Yii::$app->language = $language->key_backend; }
            
        }
        return parent::beforeAction($action);
    }

    public function actionTest()
    {	
	     require_once(Yii::getAlias('@vendor').'/fpdf/common.php');	
	     //if($_REQUEST['type']=='invoice')
		 //{
			echo Yii::$app->SlotFunctions->generateInvoice($_REQUEST['slot_id']);
		 //}
		 echo '<br/>';
		 //if($_REQUEST['type']=='mandat')
		 //{
			echo Yii::$app->SlotFunctions->generateMandat($_REQUEST['slot_id']);	
		 //}
		
		exit;
		/*
        $StaffUser=StaffUser::find()->all();
		if(!empty($StaffUser))
		{
			foreach($StaffUser as $key=>$value)
			{
				$value->timezone_id = Yii::$app->MyFunctions->getTimezone($value->latitude,$value->longitude);
				echo $value->timezone_id;echo '<br/>';
				if($value->save()){}else{ echo '<pre>';print_r($value);exit; }
			}
		}
		*/
		/*
		$OwnerUser=OwnerUser::find()->all();
		if(!empty($OwnerUser))
		{
			foreach($OwnerUser as $key=>$value)
			{
				$value->timezone_id = Yii::$app->MyFunctions->getTimezone($value->latitude,$value->longitude);
				echo $value->timezone_id;echo '<br/>';
				if($value->save()){}else{ echo $value->name; }
			}
		}
		*/
    }
   
    //01. AppLanguages (beforeauth)
	public function actionAppLanguages()
	{ 	
		$data=array();
		$model = AppLanguages::find()->select(['language_id','name','display_name','key_name'])->andWhere(['status'=>'Active','is_deleted'=>'No'])->orderBy(['display_order'=>SORT_ASC])->all(); 
		if(!empty($model))
		{	
			foreach($model as $key => $value) 
			{
				$data[]=Yii::$app->MyFunctions->objectAppLanguages($value);
			}
			$result = array("result"=>"1","message"=>'',"data"=>$data);
		}
		else
		{
			$result = array("result"=>"2","message"=>'',"data"=>$data);	
		}	
	    	Yii::$app->MyFunctions->JsonPrint($result);	        
	}

	//02. AppModules (beforeauth)
	public function actionAppModules()
	{ 	
		Yii::$app->MyFunctions->requiredParamsValidation($_REQUEST,'AppModules');
		$data=array();
		$model = AppModules::find()->select(['module_id','name'])->andWhere(['status'=>'Active','is_deleted'=>'No'])->orderBy(['display_order'=>SORT_ASC])->all(); 
		if(!empty($model))
		{	
			foreach($model as $key => $value) 
			{   $value->language_id=$_REQUEST['language_id'];
				$data[]=Yii::$app->MyFunctions->objectAppModules($value);
			}
			$result = array("result"=>"1","message"=>'',"data"=>$data);
		}
		else
		{
			$result = array("result"=>"2","message"=>'',"data"=>$data);	
		}	
	    	Yii::$app->MyFunctions->JsonPrint($result);	        
	}

    //03. AppCategory (beforeauth)
	public function actionAppCategory()
	{ 	
		Yii::$app->MyFunctions->requiredParamsValidation($_REQUEST,'AppCategory');
		$data=array();
		$model = AppCategory::find()->select(['category_id','name','hour_price','grater_150_hp','grater_150_afees','grater_100_travel_fees','grater_50_travel_fees','daylimitofsinglewithmulti','daylimitofmultidates'])->andWhere(['status'=>'Active','is_deleted'=>'No','type'=>$_REQUEST['type'],'module_id'=>$_REQUEST['module_id']])->orderBy(['display_order'=>SORT_ASC])->all(); 
		if(!empty($model))
		{	
			foreach($model as $key => $value) 
			{   $value->language_id=$_REQUEST['language_id'];
				$data[]=Yii::$app->MyFunctions->objectAppCategory($value);
			}
			$result = array("result"=>"1","message"=>'',"data"=>$data);
		}
		else
		{
			$result = array("result"=>"2","message"=>'',"data"=>$data);	
		}		
	    	Yii::$app->MyFunctions->JsonPrint($result);	        
	}

	//04. AppStaticpage (beforeauth)
	public function actionAppStaticpage()
	{ 	
		Yii::$app->MyFunctions->requiredParamsValidation($_REQUEST,'AppStaticpage');
		$data=array();
		$model = AppStaticpage::find()->select(['staticpage_id','title','details'])->andWhere(['status'=>'Active','is_deleted'=>'No','staticpage_id'=>$_REQUEST['staticpage_id']])->orderBy(['display_order'=>SORT_ASC])->one(); 
		if(!empty($model))
		{	
			//foreach($model as $key => $value) 
			//{   
				$data['staticpage_id']=$model->staticpage_id;
				$data['title']=$model->title;
				$data['details']=$model->details;
			//}
			$result = array("result"=>"1","message"=>'',"data"=>$data);
		}
		else
		{
			$result = array("result"=>"2","message"=>'',"data"=>$data);	
		}	
	    	Yii::$app->MyFunctions->JsonPrint($result);	        
	}

	//05. Login (beforeauth)	
	public function actionLogin()
	{ 	
		$data=array();
		if( !empty($_REQUEST['loginwith']) && !empty($_REQUEST['type']) && !empty($_REQUEST['unique_id']) && !empty($_REQUEST['devicetype']) && !empty($_REQUEST['devicetoken']) && !empty($_REQUEST['app_version']) && !empty($_REQUEST['device_model']) )
		{
			$loginwith=$_REQUEST['loginwith'];
			$type=$_REQUEST['type'];
			$unique_id=$_REQUEST['unique_id'];
			$devicetype=$_REQUEST['devicetype'];
			$devicetoken=$_REQUEST['devicetoken'];
			$app_version=$_REQUEST['app_version'];
			$device_model=$_REQUEST['device_model'];
			
			if($loginwith=='Normal' && !empty($_REQUEST['password']))
			{
				$model = User::find()->andWhere(['is_deleted'=>'No','type'=>$type,'loginwith'=>'Normal','unique_id'=>$unique_id,'password'=>sha1($_REQUEST['password']) ])->one();		
			}
			else
			{
				$model = User::find()->andWhere(['!=','loginwith','Normal'])->andWhere(['is_deleted'=>'No','type'=>$type,'unique_id'=>$unique_id ])->one();		
			}			
		}

		if(!empty($model))
		{	
			if($model->status=='In Complete')
			{
				$data[0]['step_completed']=$model->step_completed;
				$data[0]['type']=$model->type;
				$data[0]['authkey']=$model->authkey;
				
                $result = array("result"=>"2","message"=>Yii::t('app','Your profile is Incomplete!'),"data"=>$data);    
                Yii::$app->MyFunctions->JsonPrint($result);
			}
			else if($model->status=='Blocked' || $model->status=='Blocked-Not Approved')
			{
				$data[0]['step_completed']=$model->step_completed;
				$data[0]['type']=$model->type;
				$data[0]['authkey']=$model->authkey;
				
                $result = array("result"=>($loginwith=='Normal')?"2":"3","message"=>Yii::t('app', 'Your account is disabled. Please contact BeLocum for more information.'),"data"=>$data);    
                Yii::$app->MyFunctions->JsonPrint($result);
			}
			else if($model->status=='Not Approved')
			{
				//$data['step_completed']=$model->step_completed;
				//$data['type']=$model->type;
				//$data['authkey']=$model->authkey;
				
                $result = array("result"=>($loginwith=='Normal')?"2":"3","message"=>Yii::t('app', 'BeLocum will contact you within 48 hours to authorize your account. For an emergency please call BeLocum at (514)-638-6908.'),"data"=>$data);    
                Yii::$app->MyFunctions->JsonPrint($result);
			}

			$ipInfo = Yii::$app->MyFunctions->ip_info();
            Yii::$app->MyFunctions->addAccessLog( $model->user_id,$model->type.'User','login',json_encode($ipInfo) );

			Yii::$app->MyFunctions->addDateLog($model->user_id,'user','userlogin');

			$UserDevice=new UserDevice();
			$UserDevice->user_id=$model->user_id;
			$UserDevice->devicetype=$devicetype;
			$UserDevice->devicetoken=$devicetoken;
			$UserDevice->app_version=$app_version;
			$UserDevice->device_model=$device_model;
			$UserDevice->save();

			$user = Yii::$app->UserFunctions->GetUser($model->authkey,$this->action->id,'v1'); 
            $childuser = Yii::$app->UserFunctions->GetChildUser($user);  
            $data[0]=Yii::$app->UserFunctions->objectUser($user,$user,$childuser,''); 

			$result = array("result"=>"1","message"=>Yii::t('app', 'Login Successfully.'),"data"=>$data);
		}
		else
		{
			$result = array("result"=>"2","message"=>Yii::t('app', 'Invalid Username or Password.'),"data"=>$data);	
		}	
	    	Yii::$app->MyFunctions->JsonPrint($result);	        
	}

	//05.1 ForgotPassword (beforeauth)
	public function actionForgotPassword()
	{ 	
		Yii::$app->MyFunctions->requiredParamsValidation($_REQUEST,'ForgotPassword');
		$data=array();
		$model = User::find()->select(['full_name','email','type'])->andWhere(['status'=>'Approved','is_deleted'=>'No','loginwith'=>'Normal','type'=>$_REQUEST['type'],'email'=>$_REQUEST['email']])->one();
		if(!empty($model))
		{	
			$model->resetpasswordemail(3);
			$result = array("result"=>"1","message"=>Yii::t('app','Kindly check your email to reset your password!'),"data"=>$data);
		}
		else
		{
			$result = array("result"=>"2","message"=>Yii::t('app','Your ID is not activated/registered, kindly contact our support team for further query!'),"data"=>$data);    
		}	
	    	Yii::$app->MyFunctions->JsonPrint($result);	        
	}
	
	//06 EmailCheck (beforeauth)	
	public function actionEmailCheck()
	{ 	
		Yii::$app->MyFunctions->requiredParamsValidation($_REQUEST,'EmailCheck');
		$data=array();

		$User=User::find()->andWhere(['module_id'=>$_REQUEST['User']['module_id'],'type'=>$_REQUEST['User']['type'],'email'=>$_REQUEST['User']['email'] ])->one();

		if(empty($User))
		{
			$data[0]['step_completed']='step-0';
			$data[0]['type']=$_REQUEST['User']['type'];
			$data[0]['authkey']='';
			$data[0]['staff_user_id']='';
			$data[0]['owner_user_id']='';
			$result = array("result"=>"1","message"=>'Success',"data"=>$data);			
		}
		else
		{
			if($User->status=='In Complete')
			{
				$data[0]['step_completed']=$User->step_completed;
				$data[0]['type']=$User->type;
				$data[0]['authkey']=$User->authkey;
				$data[0]['staff_user_id']='';
				$data[0]['owner_user_id']='';
				$ChildUser='';
				if($User->type=='Staff')
				{
					$ChildUser=StaffUser::find()->andWhere(['user_id'=>$User->user_id])->orderBy(['staff_user_id'=>SORT_ASC])->one();
					if(!empty($ChildUser))
					{
						$data[0]['staff_user_id']=$ChildUser->staff_user_id;
					}
				}
				else if($User->type=='Owner')
				{
					$ChildUser=OwnerUser::find()->andWhere(['user_id'=>$User->user_id])->orderBy(['owner_user_id'=>SORT_ASC])->one();
					if(!empty($ChildUser))
					{
						$data[0]['owner_user_id']=$ChildUser->owner_user_id;
					}
				}

				$result = array("result"=>"1","message"=>'Success',"data"=>$data);			
			}
			else
			{
				$result = array("result"=>"2","message"=>Yii::t('app','Your email is already registered with BeLocum. Please contact us at info@belocum.com if you experience any difficulties.'),"data"=>$data);    
			}						
		}

		Yii::$app->MyFunctions->JsonPrint($result);		
	}

	//06.1 Signup-Staff-Step-1 (beforeauth)	
	//06.2 Signup-Owner-Step-1 (beforeauth)	
	public function actionSignupStep1()
	{ 	
		$data=array();

		$User=new User();
		$User->scenario='step1';
		$User->load($_REQUEST);

		if($User->validate()){ }
		else
		{
			$errors = Yii::$app->MyFunctions->getModelErrors($User);
			$result = array("result"=>"2","message"=>$errors,"data"=>$data);	
			Yii::$app->MyFunctions->JsonPrint($result);					
		}

		if($User->type=='Staff')
		{
			$ChildUser=new StaffUser();
			$ChildUser->user_id=NULL;
			if($User->module_id==5)
			{
				$ChildUser->scenario='step1_5';			
			}			
			$ChildUser->load($_REQUEST);	
		}
		else
		{
			$ChildUser=new OwnerUser();
			$ChildUser->user_id=NULL;
			if($User->module_id==5)
			{
				$ChildUser->scenario='step1_5';			
			}			
			$ChildUser->load($_REQUEST);
		}		

		if($ChildUser->validate()){ }
		else
		{
			$errors = Yii::$app->MyFunctions->getModelErrors($ChildUser);
			$result = array("result"=>"2","message"=>$errors,"data"=>$data);	
			Yii::$app->MyFunctions->JsonPrint($result);					
		}

		$User->validateuser($_REQUEST);

		if($User->save())
		{
			Yii::$app->MyFunctions->addDateLog($User->user_id,'user','step-1');
			$ChildUser->user_id=$User->user_id;
		}
		else
		{
			$errors = Yii::$app->MyFunctions->getModelErrors($User);
			$result = array("result"=>"2","message"=>$errors,"data"=>$data);	
			Yii::$app->MyFunctions->JsonPrint($result);				
		}	

		if($ChildUser->save())
		{   
			Yii::$app->MyFunctions->addDateLog($User->user_id,($User->type=='Staff')?'staff_user':'owner_user','step-1');
			$data[0]['step_completed']=$User->step_completed;
			$data[0]['type']=$User->type;
			$data[0]['authkey']=$User->authkey;
			$data[0]['staff_user_id']='';
			$data[0]['owner_user_id']='';
			if($User->type=='Staff'){ $data[0]['staff_user_id']=$ChildUser->staff_user_id; }
			if($User->type=='Owner'){ $data[0]['owner_user_id']=$ChildUser->owner_user_id; }			
			$result = array("result"=>"1","message"=>'Success',"data"=>$data);
			Yii::$app->MyFunctions->JsonPrint($result);
		}
		else
		{
			$errors = Yii::$app->MyFunctions->getModelErrors($ChildUser);
			$result = array("result"=>"2","message"=>$errors,"data"=>$data);
			Yii::$app->MyFunctions->JsonPrint($result);					
		}
		
	}

	//06.3 CheckVersion (beforeauth)	
	public function actionCheckVersion()
	{ 	
		Yii::$app->MyFunctions->requiredParamsValidation($_REQUEST,'CheckVersion');
		$data=array();

		$app_version=$_REQUEST['app_version'];
	    $devicetype=$_REQUEST['devicetype'];
	    $language_id=$_REQUEST['language_id'];
	    $corona=Yii::$app->MyFunctions->Covid19($language_id);
	    if($devicetype=='iOS'){ $configuration_id=10; }else{ $configuration_id=11; }

	    $configuration=AppConfigurations::find()->andWhere(['configuration_id'=>$configuration_id])->one();

	    if($devicetype=="iOS" && $app_version<$configuration->key_value)
	    {
	    	$result = array(
				 "corona"=>$corona,
				 "result"=>(string)$configuration->comment,
				 "message"=>Yii::t('app', 'You need to update your application.'),
				 "app_version"=>$configuration->key_value,
				 "data"=>$data
			);	    	
	    }
	    else if($devicetype=="Android" && $app_version<$configuration->key_value)
	    {
			$result = array(
				 "corona"=>$corona,
				 "result"=>(string)$configuration->comment,
				 "message"=>Yii::t('app', 'You need to update your application.'),
				 "app_version"=>$configuration->key_value,
				 "data"=>$data
			);	    	
	    }
	    else
	    {
	    	
	    	$result = array(
	    		 "corona"=>$corona,
				 "result"=>"1",
				 "message"=>Yii::t('app', 'Your app is updated.')
			);
	    }	

		Yii::$app->MyFunctions->JsonPrint($result);		
	}

	


}
