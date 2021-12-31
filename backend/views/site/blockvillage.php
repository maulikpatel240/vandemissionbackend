<?php

/* @var $this yii\web\View */

use yii\bootstrap5\LinkPager;
use backend\models\States;
use backend\models\Districts;
use backend\models\Subdistricts;
use backend\models\Blocks;
use backend\models\Villages;
use backend\models\Locality;
use backend\models\Pincode;
use backend\models\Blockvillage;
use yii\widgets\Pjax;

$page = (isset($_REQUEST['page']))?$_REQUEST['page']:1;
$village_count_page = Villages::find()->where(['page' => $page])->count();

//$date1 = time();
echo '<div class="villageform">';

Pjax::begin(['id' => 'villageajax']);

echo '<div class="row">'
 . '<div class="col-md-12">';
// display pagination
echo LinkPager::widget([
    'pagination' => $pages,
]);
echo '</div>'
 . '</div>';
echo '<div class="row">'
 . '<div class="col-md-12"><div class="table-responsive">';
echo '<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">english</th>
      <th scope="col">pincode</th>
      <th scope="col">officename</th>
    </tr>
  </thead><tbody>';
//$villagedb = [];
$i = 0;
foreach ($models as $model) {
    
    $strq = $model->english.', '.$model->subdistrict->english.', '.$model->state->english;
   
    //$strq = 'Pattan, Lunavada, Gujarat, India';
    //$strq = 'gota, Ahmedabad, Gujarat, India';
    $url = "http://dev.virtualearth.net/REST/v1/Locations?q=".urlencode($strq)."&maxResults=1&key=AtOwYoDmFoS0y4CPmckjstxdipjaOqF6VTr8DPsgeLNilvcRzqyu6Yz1KqHv5Mq_";
    //$url = "https://www.bing.com/api/v6/Places/AutoSuggest?q=".urlencode($strq)."&appid=D41D8CD98F00B204E9800998ECF8427E1FBE79C2&mv8cid=68c6d7f3-6184-af82-92ee-455bbe5955ad&mv8ig=D90E6BB122DC418982E5DB924532D144&localMapView=23.129987954929106,73.53636640994264,23.12229193470003,73.56567759005739&localcircularview=23.12614,73.551022,100&count=5&structuredaddress=true&types=&setmkt=en-IN&setlang=en-IN&histcnt=&favcnt=&ptypes=favorite&clientid=344A5A1E95826A1333E34A7E94306B4D&abbrtext=1";
    $output = Yii::$app->BackFunctions->restcurl($url);
    //echo '<pre>'; print_r($output);echo '</pre>';exit;
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
            //echo '<pre>'; print_r($output1);echo '</pre>';exit;
            $postalcode = (isset($output1['resourceSets'][0]['resources'][0]['address']['postalCode']))?$output1['resourceSets'][0]['resources'][0]['address']['postalCode']:'';
        }
        $update = [
            'pincode' => $postalcode,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'bounding_box' => $bbox
        ];
        Yii::$app->db->createCommand()->update('villages', $update, ['id'=>$model->id])->execute();
       // echo '<pre>'; print_r($output1);echo '</pre>';exit;  
    }
   
    echo '<tr>
      <th scope="row">' . $model->id . '</th>
      <td>' . $model->english . '</td>
      <td>' . $model->pincode . '</td>
      <td>' . $model->officename . '</td>
    </tr>';

   /* if (empty($village_count_page) || $village_count_page == 0) {
       //echo '<pre>'; print_r($model->subdistrict);echo '</pre>';exit;
//        $States = States::find()->where(['code' => $model->state_code])->one();
//        $Districts = Districts::find()->where(['code' => $model->district_code])->one();
//        $Blocks = Blocks::find()->where(['code' => $model->block_code])->one();
//        $Subdistricts = Subdistricts::find()->where(['code' => $model->subdistrict_code])->one();
        $Localitydb = Locality::find()->where(['english' => ucfirst(strtolower($model->english))])->andFilterWhere(['like', 'subdistrict', rtrim($model->subdistrict->english, 's')])->one();
        if($Localitydb){
            $pincode = ($Localitydb) ? $Localitydb->pincode : null;
            $officename = ($Localitydb) ? $Localitydb->officename : null;
            Yii::$app->db->createCommand()->update('villages', ['pincode' => $pincode, 'officename'=>$officename, 'page'=>$model->id], ['id'=>$model->id])->execute();
        }else{
            $Localitydb = Locality::find()->where(['english' => ucfirst(strtolower($model->english))])->one();
            $pincode = ($Localitydb) ? $Localitydb->pincode : null;
            $officename = ($Localitydb) ? $Localitydb->officename : null;
            Yii::$app->db->createCommand()->update('villages', ['pincode' => $pincode, 'officename'=>$officename, 'page'=>$model->id], ['id'=>$model->id])->execute();
        }
//        if ($Districts) {
//            $columnNameArray = ['country_id', 'district_id', 'state_id', 'block_id', 'subdistrict_id', 'code', 'english', 'lang_key', 'pincode', 'officename', 'status_at', 'created_at', 'updated_at', 'status'];
//            $villagedb = [
//                1,
//                $Districts->id,
//                $States->id,
//                ($Blocks) ? $Blocks->id : null,
//                ($Subdistricts) ? $Subdistricts->id : null,
//                $model->village_code,
//                ucfirst(strtolower($model->village_name)),
//                str_replace(' ', '_', strtolower($model->village_name)) . '_' . ($i + 1),
//                $pincode,
//                $officename,
//                date('Y-m-d H:i:s'),
//                date('Y-m-d H:i:s'),
//                date('Y-m-d H:i:s'),
//                'Active',
//                $page
//            ];
//            $columnNameArray = ['country_id', 'district_id', 'state_id', 'block_id', 'subdistrict_id', 'code', 'english', 'lang_key', 'pincode', 'officename', 'status_at', 'created_at', 'updated_at', 'status', 'page'];
//            Yii::$app->db->createCommand()->batchInsert('villages', $columnNameArray, [$villagedb])->execute();
//        }
    } else {
        echo 'already inserted page:' . $page.' id:'.$model->id.'<br>';
    }*/
    $i++;
}
$date2= time();

echo ($date2-$date1);
//Yii::$app->cacheBackend->set('villagedbFirst', $villagedb);
//$columnNameArray = ['country_id', 'district_id', 'state_id', 'block_id', 'subdistrict_id', 'code', 'english', 'lang_key', 'pincode', 'officename', 'status_at', 'created_at', 'updated_at', 'status', 'page'];
//Yii::$app->db->createCommand()->batchInsert('villages', $columnNameArray, $villagedb)->execute();

echo '</tbody>
</table>';
echo '</div></div>'
 . '</div>';
echo '<div class="row">'
 . '<div class="col-md-12">';
// display pagination
echo LinkPager::widget([
    'pagination' => $pages,
]);
echo '</div>'
 . '</div>';
Pjax::end();
echo '</div>';
$this->registerJs(
        'jQuery(document).on("pjax:success", "#villageajax",  function(event){
                
          }
        );'
);
?>

<script>
    $(document).ready(function() {
//        var pathname = window.location.pathname; // Returns path only (/path/example.html)
//        var url      = window.location.href;     // Returns full URL (https://example.com/path/example.html)
//        var origin   = window.location.origin;   // Returns base URL (https://example.com)
        //location.href = '/vandemission/adminpanel/site/villagesxls?page=<?=($page+1)?>&per-page=1';
        //alert(location);
    });
</script>

