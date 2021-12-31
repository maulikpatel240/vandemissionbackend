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
//$village_count_page = Villages::find()->where(['page' => $page])->count();
$village_count_page = 0;

echo '<div class="villageform">';

//Pjax::begin(['id' => 'villageajax']);

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
    
    echo '<tr>
      <th scope="row">' . $model->id . '</th>
      <td>' . $model->english . '</td>
      <td>' . $model->pincode . '</td>
      <td>' . $model->officename . '</td>
    </tr>';

    if (empty($village_count_page) || $village_count_page == 0) {
       //echo '<pre>'; print_r($model->subdistrict);echo '</pre>';exit;
//        $States = States::find()->where(['code' => $model->state_code])->one();
//        $Districts = Districts::find()->where(['code' => $model->district_code])->one();
//        $Blocks = Blocks::find()->where(['code' => $model->block_code])->one();
//        $Subdistricts = Subdistricts::find()->where(['code' => $model->subdistrict_code])->one();
        $Localitydb = Locality::find()->where(['english' => ucfirst(strtolower($model->english))])->andFilterWhere(['like', 'subdistrict', rtrim($model->subdistrict->english, 's')])->one();
        
        if($Localitydb){
            $pincode = $Localitydb->pincode;
            $officename = $Localitydb->officename;
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
    }
    $i++;
}
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
//Pjax::end();
echo '</div>';
//echo "dsd";
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
        location.href = '/vandemission/adminpanel/site/village?page=<?=($page+1)?>&per-page=1';
        //alert(location);
    });
</script>

