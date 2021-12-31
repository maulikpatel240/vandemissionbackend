<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use common\components\AppStart;
use backend\components\BaseController;
use backend\models\UploadExcel;
use yii\web\UploadedFile;

use backend\models\Langs;
use backend\models\States;
use backend\models\Districts;
use backend\models\Subdistricts;
use backend\models\Blocks;
use backend\models\Villages;
use backend\models\Locality;
use backend\models\Pincode;
use backend\models\Blockvillage;
use backend\models\Blockvillage1;
use yii\data\Pagination;
/**
 * Site controller
 */
class SiteController extends BaseController {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'import','export','villagesxls','village'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
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
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        if (empty(Yii::$app->BackFunctions->checkaccess(Yii::$app->controller->action->id, Yii::$app->controller->id))) {
            throw new \yii\web\HttpException('403', Yii::$app->params['permission_message']);
        }
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
//        if (!Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
        $this->layout = 'beforelogin';
        //$password = "123456";
        //echo Yii::$app->security->generatePasswordHash($password);exit;
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionError() {
        if ($error = Yii::$app->errorHandler->error) {
            $this->render('error', $error);
        }
    }

    public function actionImport() {
        $this->layout = 'beforelogin';
        $model = new UploadExcel();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            //      if ($model->upload()) {
            //        print <<<EOT
            //< script > alert ('upload succeeded ') < / script >
            //EOT;
            //      } else {
            //        print <<<EOT
            //< script > alert ('upload failed ') < / script >
            //EOT;
            //      }
            if (!$model->upload()) {
                print <<<EOT
                < script > alert ('upload failed ') < / script >
               EOT;
            }
        }

        $ok = 0;
        if ($model->load(Yii::$app->request->post())) {
            $data = [];
            Yii::$app->cacheBackend->delete('pincode');
            if(Yii::$app->cacheBackend->get('pincode')){
                $data = Yii::$app->cacheBackend->get('pincode');
            }else{
                $file = UploadedFile::getInstance($model, 'file');

                if ($file) {
                    $filename = 'upload/Files/' . $file->name;
                    $file->saveAs($filename);

                    if (in_array($file->extension, array('xls', 'xlsx'))) {
                        $data = \moonland\phpexcel\Excel::widget([
                                'mode' => 'import', 
                                'fileName' => $filename, 
                                'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel. 
                                'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric. 
                               // 'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
                        ]);
                        //prev(array)
                        //Yii::$app->cacheBackend->set('pincode', $data);
                        //Yii::$app->cacheBackend->set('villages', $data);
                        //$data = $data['Report'];
                        //echo '<pre>'; print_r($data);echo '</pre>';exit;
                        return $this->render('import_exel', ['data' => $data]);
                    }
                }
            }
            $villagedb = [];
            if($data){
                for($i=0; $i<count($data); $i++){  
                    $States = States::find()->where(['code'=>$data[$i]['State code']])->one();
                    
                    $Districts = Districts::find()->where(['code'=>$data[$i]['District code']])->one();
//                    $Districts = Districts::getDb()->cache(function ($db) use ($data,$i) {
//                                return Districts::find()->where(['code'=>$data[$i]['District code']])->one();
//                            });
                    $Blocks = Blocks::find()->where(['code'=>$data[$i]['Block code']])->one();
//                    $Blocks = Blocks::getDb()->cache(function ($db) use ($data,$i) {
//                                    return Blocks::find()->where(['code'=>$data[$i]['Block code']])->one();
//                                });
                    
                    $Subdistricts = Subdistricts::find()->where(['code'=>$data[$i]['Subdistrict code']])->one();
//                    $Subdistricts = Subdistricts::getDb()->cache(function ($db) use ($data,$i) {
//                                    return Subdistricts::find()->where(['code'=>$data[$i]['Subdistrict code']])->one();
//                                });
                    //$Localitydb = Locality::find()->where(['english'=>ucfirst(strtolower($data[$i]['Village Name(In English)']))])->andFilterWhere(['like', 'subdistrict', rtrim($Subdistricts->english,'s')])->one();
//                    $pincode = ($Localitydb)?$Localitydb->pincode:null;
//                    $officename = ($Localitydb)?$Localitydb->officename:null;
                    $pincode = null;
                    $officename = null;
                    if($Districts){
                        $columnNameArray = ['country_id', 'district_id', 'state_id', 'block_id', 'subdistrict_id', 'code', 'english', 'lang_key', 'pincode', 'officename', 'status_at', 'created_at', 'updated_at', 'status'];
                        $villagedb[] = [
                            1,
                            $Districts->id,
                            $States->id,
                            ($Blocks) ? $Blocks->id : null,
                            ($Subdistricts) ? $Subdistricts->id : null,
                            $data[$i]['Village code'],
                            ucfirst(strtolower($data[$i]['Village Name(In English)'])),
                            str_replace(' ', '_', strtolower($data[$i]['Village Name(In English)'])).'_'.($i+1),
                            $pincode,
                            $officename,
                            date('Y-m-d H:i:s'),
                            date('Y-m-d H:i:s'),
                            date('Y-m-d H:i:s'),
                            'Active'
                        ];
                        echo $i.'<br>';
                        /*$Locality = new Villages();
                        $Locality->country_id = 1;
                        $Locality->district_id = $Districts->id;
                        $Locality->state_id = $States->id;
                        $Locality->block_id = ($Blocks)?$Blocks->id:null;
                        $Locality->subdistrict_id = ($Subdistricts)?$Subdistricts->id:null;
                        $Locality->code = $data[$i]['Village code'];
                        $Locality->english = ucfirst(strtolower($data[$i]['Village Name(In English)']));
                        $Locality->lang_key = str_replace(' ','_',strtolower($data[$i]['Village Name(In English)']));
                        $Locality->status_at = date('Y-m-d H:i:s');
                        $Locality->created_at = date('Y-m-d H:i:s');
                        $Locality->updated_at = date('Y-m-d H:i:s');
                        $Locality->status = 'Active';
                        
                        if(!$Locality->save()){
                            echo '<pre>'; print_r($Locality->getErrors());echo '</pre>';exit;
                        }
//                        $Localitydb = Locality::find()->where(['english'=>$Locality->english])->andFilterWhere(['like', 'subdistrict', rtrim($Subdistricts->english,'s')])->one();
////                        $Localitydb = Locality::getDb()->cache(function ($db) use ($Locality) {
////                                    return Locality::find()->where(['english'=>$Locality->english])->one();
////                                });
//                        $Locality->pincode = ($Localitydb)?$Localitydb->pincode:null;
//                        $Locality->officename = ($Localitydb)?$Localitydb->officename:null;
                        $Locality->lang_key = str_replace(' ','_',strtolower($data[$i]['Village Name(In English)'])).'_'.$Locality->id;
                        $Locality->save();
                        echo $Locality->id.'<br>';*/
                    }
                }
            }
        } else {
            return $this->render('import_exel', ['model' => $model]);
        }
    }
    
    public function actionExport(){
        $this->layout = 'beforelogin';
        //echo Yii::getAlias('@app');exit;
//        $pincodesql = file_get_contents(Yii::getAlias('@app')."/runtime/pincode.txt");
//        echo '<pre>'; print_r($pincodesql);echo '</pre>';exit;
        $pincodesql = Yii::$app->cacheBackend->get('pincodesql');
        $modalpincode = json_decode($pincodesql,true);
        $data = [];
        if($modalpincode){
            foreach ($modalpincode[2]['data'] as $row){
                Yii::$app->cacheBackend->delete('LocalityAll');
                if(Yii::$app->cacheBackend->get('LocalityAll')){
                    $model = Yii::$app->cacheBackend->get('LocalityAll');
                }else{
                    $model = Locality::find()->where(['pincode'=>$row['Pincode'],'subdistrict'])->asArray()->all();
                    Yii::$app->cacheBackend->set('LocalityAll',$model);
                }
                echo '<pre>'; print_r($model);echo '</pre>';
                array_push($data, $model);
//                if($model){
//                    foreach ($model as $k){
//                        $modelnew = Locality::find()->where(['id'=>$k['id']])->one();
//                        $modelnew->id = $modelnew->id;
//                        $modelnew->code = $modelnew->code;
//                        $modelnew->english = $modelnew->english;
//                        $modelnew->officename = $row['OfficeName'];
//                        $modelnew->pincode = $modelnew->pincode;
//                        $modelnew->subdistrict = $modelnew->subdistrict;
//                        $modelnew->district = $row['District'];
//                        $modelnew->state = $row['StateName'];
//                        echo '<pre>'; print_r($modelnew);echo '</pre>';
//                    }
//                }
                
            }
        }
        
    }

    public function actionVillagesxls(){
        $this->layout = 'blank';
        // $date1 = time();
        // $query = Villages::find();
        // $countQuery = clone $query;
        // $pages = new Pagination(['totalCount' => $countQuery->count(),'pageSize' => 500]);
        // //$pages->limit = 25;
        // //echo '<pre>'; print_r($pages);echo '</pre>';exit;
        // $models = $query->offset($pages->offset)
        //     ->limit($pages->limit)
        //     ->all();
        // //echo '<pre>'; print_r($models);echo '</pre>';exit;
        // return $this->render('blockvillage', [
        //         'models' => $models,
        //         'pages' => $pages,
        //         'date1'=>$date1
        //    ]);
    }
    
    public function actionVillage(){
        $this->layout = 'blank';
      
        // $query = Villages::find();
        // $countQuery = clone $query;
        // $pages = new Pagination(['totalCount' => $countQuery->count(),'pageSize' => 10]);
        // //$pages->limit = 25;
        // //echo '<pre>'; print_r($pages);echo '</pre>';exit;
        // $models = $query->offset($pages->offset)
        //     ->limit($pages->limit)->orderBy(['id' => SORT_DESC])
        //     ->all();
        // //echo '<pre>'; print_r($models);echo '</pre>';exit;
        // return $this->render('blockvillage_1', [
        //         'models' => $models,
        //         'pages' => $pages,
        //    ]);
    }
}
