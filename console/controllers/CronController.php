<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use backend\models\Villages;
use yii\data\Pagination;
/**
 * CitiesController implements the CRUD actions for Cities model.
 */
class CronController extends Controller {

    public function actionIndex() { 
        echo "cron service runnning";
        $query = Villages::find();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'pageSize' => 70000]);
        //$pages->limit = 25;
        //echo '<pre>'; print_r($pages);echo '</pre>';exit;
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)->where(['latitude'=>null])->orderBy(['id' => SORT_ASC])
            ->all();
        //echo '<pre>'; print_r($models);echo '</pre>';exit;
        //$models = Villages::find()->where(['latitude'=>null])->orderBy(['id' => SORT_ASC])->all();
        
        $keyapi = 'AptkonY30vLj05cmQBIEqcvRHHPwd_B9RCalVxm6ifsQ6f5EItJSLzToNPj-GxY2';
        $i=0;
        foreach ($models as $model) {
            $strq = $model->english.', '.$model->subdistrict->english.', '.$model->state->english;
            $url = "http://dev.virtualearth.net/REST/v1/Locations?q=".urlencode($strq)."&maxResults=1&key=".$keyapi;
            //$url = "https://www.bing.com/api/v6/Places/AutoSuggest?q=".urlencode($strq)."&appid=D41D8CD98F00B204E9800998ECF8427E1FBE79C2&mv8cid=68c6d7f3-6184-af82-92ee-455bbe5955ad&mv8ig=D90E6BB122DC418982E5DB924532D144&localMapView=23.129987954929106,73.53636640994264,23.12229193470003,73.56567759005739&localcircularview=23.12614,73.551022,100&count=5&structuredaddress=true&types=&setmkt=en-IN&setlang=en-IN&histcnt=&favcnt=&ptypes=favorite&clientid=344A5A1E95826A1333E34A7E94306B4D&abbrtext=1";
            $output = Yii::$app->BackFunctions->restcurl($url);
            
            if(isset($output['statusCode']) && $output['statusCode'] == 200){
                $bbox = (isset($output['resourceSets'][0]['resources'][0]['bbox']))?implode(', ',$output['resourceSets'][0]['resources'][0]['bbox']):'';
                $latitude = (isset($output['resourceSets'][0]['resources'][0]['point']['coordinates'][0]))?$output['resourceSets'][0]['resources'][0]['point']['coordinates'][0]:'';
                $longitude = (isset($output['resourceSets'][0]['resources'][0]['point']['coordinates'][1]))?$output['resourceSets'][0]['resources'][0]['point']['coordinates'][1]:'';
        //        $latitude = $output['value'][0]['geo']['latitude'];
        //        $longitude = $output['value'][0]['geo']['longitude'];
        //        $addressLocality = $output['value'][0]['address']['addressLocality'];
        //        $addressRegion = $output['value'][0]['address']['addressRegion'];
        //        $countryIso = $output['value'][0]['address']['countryIso'];
        //        $text = $output['value'][0]['address']['text'];

                $latlong = $latitude.','.$longitude;
                //$latlong = '23.112663269043,72.547752380371';
                $url2 = 'http://dev.virtualearth.net/REST/v1/Locations/'.$latlong.'?o=json&key='.$keyapi;
                $output1 = Yii::$app->BackFunctions->restcurl($url2);
                $postalcode = '';
                if(isset($output1['statusCode']) && $output1['statusCode'] == 200){
                    $postalcode = (isset($output1['resourceSets'][0]['resources'][0]['address']['postalCode']))?$output1['resourceSets'][0]['resources'][0]['address']['postalCode']:'';
                }
                $update = [
                    'pincode' => $postalcode,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'bounding_box' => $bbox
                ];
                echo ($i+1).' : '.$model->id. ' = ';
                //echo '<pre>'; print_r($update);echo '</pre>';exit;
                Yii::$app->db->createCommand()->update('villages', $update, ['id'=>$model->id])->execute();
            }
        }
        //echo '<pre>'; print_r($models);echo '</pre>';exit;
    }
    
    public function updatevillages(){ 
        
        $query = Villages::find();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'pageSize' => 1]);
        //$pages->limit = 25;
        //echo '<pre>'; print_r($pages);echo '</pre>';exit;
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)->orderBy(['id' => SORT_DESC])
            ->all();
        
        foreach ($models as $model) {
            
            $place = 'https://www.bing.com/api/v6/Places/AutoSuggest?q=lunawada&appid=F2DD9E3AA45F7512D9C6CA9A150CBA7F76556B81&mv8cid=9f41a71e-f909-cfe4-513b-effb9964645e&mv8ig=0217AF4B9E7B4404988CDC3DBC85B2A9&localMapView=&localcircularview=23.067100524902344,72.5656967163086,100&count=5&structuredaddress=true&types=place,address&setmkt=en-IN&setlang=en-IN&histcnt=&favcnt=&clientid=3E0ED2BB6C316D9D2332C2D06D836C95';
            $strq = $model->english.', '.$model->subdistrict->english.', '.$model->state->english;
            $url = "http://dev.virtualearth.net/REST/v1/Locations?q=".urlencode($strq)."&maxResults=1&key=AtOwYoDmFoS0y4CPmckjstxdipjaOqF6VTr8DPsgeLNilvcRzqyu6Yz1KqHv5Mq_";
            //$url = "https://www.bing.com/api/v6/Places/AutoSuggest?q=".urlencode($strq)."&appid=D41D8CD98F00B204E9800998ECF8427E1FBE79C2&mv8cid=68c6d7f3-6184-af82-92ee-455bbe5955ad&mv8ig=D90E6BB122DC418982E5DB924532D144&localMapView=23.129987954929106,73.53636640994264,23.12229193470003,73.56567759005739&localcircularview=23.12614,73.551022,100&count=5&structuredaddress=true&types=&setmkt=en-IN&setlang=en-IN&histcnt=&favcnt=&ptypes=favorite&clientid=344A5A1E95826A1333E34A7E94306B4D&abbrtext=1";
            $output = Yii::$app->BackFunctions->restcurl($url);
            
            if(isset($output['statusCode']) && $output['statusCode'] == 200){
                $bbox = (isset($output['resourceSets'][0]['resources'][0]['bbox']))?implode(', ',$output['resourceSets'][0]['resources'][0]['bbox']):'';
                $latitude = $output['resourceSets'][0]['resources'][0]['point']['coordinates'][0];
                $longitude = $output['resourceSets'][0]['resources'][0]['point']['coordinates'][1];
        //        $latitude = $output['value'][0]['geo']['latitude'];
        //        $longitude = $output['value'][0]['geo']['longitude'];
        //        $addressLocality = $output['value'][0]['address']['addressLocality'];
        //        $addressRegion = $output['value'][0]['address']['addressRegion'];
        //        $countryIso = $output['value'][0]['address']['countryIso'];
        //        $text = $output['value'][0]['address']['text'];

                $latlong = $latitude.','.$longitude;
                //$latlong = '23.112663269043,72.547752380371';
                $url2 = 'http://dev.virtualearth.net/REST/v1/Locations/'.$latlong.'?o=json&key=AtOwYoDmFoS0y4CPmckjstxdipjaOqF6VTr8DPsgeLNilvcRzqyu6Yz1KqHv5Mq_';
                $output1 = Yii::$app->BackFunctions->restcurl($url2);
                $postalcode = '';
                if(isset($output1['statusCode']) && $output1['statusCode'] == 200){
                    $postalcode = $output1['resourceSets'][0]['resources'][0]['address']['postalCode'];
                }
                $update = [
                    'pincode' => $postalcode,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'bounding_box' => $bbox
                ];
                echo '<pre>'; print_r($update);echo '</pre>';exit;
                //Yii::$app->db->createCommand()->update('villages', $update, ['id'=>$model->id])->execute();
            }
        }
    }

}
