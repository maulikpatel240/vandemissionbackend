<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\components\BaseController;
use backend\models\States;
use backend\models\Districts;
use backend\models\Subdistricts;
use backend\models\Blocks;
use backend\models\Villages;
use backend\models\VillagesSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * DistrictsController implements the CRUD actions for Districts model.
 */
class RequestsController extends BaseController {

    public function actionStates() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $country_id = end($_POST['depdrop_parents']);
            $list = ArrayHelper::map(States::find()->where(['status' => 'Active', 'country_id' => $country_id])->asArray()->all(), 'id', 'english');
            $selected = null;
            if ($country_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $type) {
                    $out[] = ['id' => $i, 'name' => $type];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionDistricts() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $state_id = end($_POST['depdrop_parents']);
            $list = ArrayHelper::map(Districts::find()->where(['status' => 'Active', 'state_id' => $state_id])->asArray()->all(), 'id', 'english');
            $selected = null;
            if ($state_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $type) {
                    $out[] = ['id' => $i, 'name' => $type];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionSubdistricts() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $district_id = end($_POST['depdrop_parents']);
            $list = ArrayHelper::map(Subdistricts::find()->where(['status' => 'Active', 'district_id' => $district_id])->asArray()->all(), 'id', 'english');
            $selected = null;
            if ($district_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $type) {
                    $out[] = ['id' => $i, 'name' => $type];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionBlocks() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $district_id = end($_POST['depdrop_parents']);
            $list = ArrayHelper::map(Blocks::find()->where(['status' => 'Active', 'district_id' => $district_id])->asArray()->all(), 'id', 'english');
            $selected = null;
            if ($district_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $type) {
                    $out[] = ['id' => $i, 'name' => $type];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionVillagesBlock() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $block_id = end($_POST['depdrop_parents']);
            $list = ArrayHelper::map(Villages::find()->where(['status' => 'Active', 'block_id' => $block_id])->asArray()->all(), 'id', 'english');
            $selected = null;
            if ($block_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $type) {
                    $out[] = ['id' => $i, 'name' => $type];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionVillagesSubdistrict() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $subdistrict_id = end($_POST['depdrop_parents']);
            $list = ArrayHelper::map(Villages::find()->where(['status' => 'Active', 'block_id' => $subdistrict_id])->asArray()->all(), 'id', 'english');
            $selected = null;
            if ($subdistrict_id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $type) {
                    $out[] = ['id' => $i, 'name' => $type];
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionLocationlist() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        //$getq = 'gujarat';
        $getq = Yii::$app->request->get('q');
        $getq = str_replace(array(':', '\\', '/', '*',',',' '), '+', $getq);
        $q = explode('+', $getq);
        $q = array_values(array_filter($q));
        if ($q) {
            $orderby = '(CASE';
            $orderby .= " WHEN ((`countries`.`english` LIKE '" . $q[0] . "%')";
            $whereby = "(`villages`.`english` LIKE '" . $q[0] . "%')";
            $whereby_v = "(`villages`.`english` LIKE '" . $q[0] . "%')";
            if ($q) {
                $i=0;
                foreach ($q as $search) {
                    if($i != 0){
                        $orderby .= " or (`countries`.`english` LIKE '" . $search . "%')"; 
                    }
                    $whereby .= " or (`countries`.`english` LIKE '" . $search . "%')";
                    $i++;
                }
            }
            $orderby .= ") THEN 1 ";
            
            $orderby = '(CASE';
            $orderby .= " WHEN ((`states`.`english` LIKE '" . $q[0] . "%')";
            if ($q) {
                $i=0;
                foreach ($q as $search) {
                    if($i != 0){
                        $orderby .= " or (`states`.`english` LIKE '" . $search . "%')";
                    }
                    $whereby .= " or (`states`.`english` LIKE '" . $search . "%')";
                    $i++;
                }
            }
            $orderby .= ") THEN 2 ";
            
            $orderby = '(CASE';
            $orderby .= " WHEN ((`districts`.`english` LIKE '" . $q[0] . "%')";
            if ($q) {
                $i=0;
                foreach ($q as $search) {
                    if($i != 0){
                        $orderby .= " or (`districts`.`english` LIKE '" . $search . "%')";
                    }
                    $whereby .= " or (`districts`.`english` LIKE '" . $search . "%')";
                    $i++;
                }
            }
            $orderby .= ") THEN 3 ";
            
            $orderby = '(CASE';
            $orderby .= " WHEN ((`subdistricts`.`english` LIKE '" . $q[0] . "%')";
            if ($q) {
                $i=0;
                foreach ($q as $search) {
                    if($i != 0){
                        $orderby .= " or (`subdistricts`.`english` LIKE '" . $search . "%')";
                    }
                    $whereby .= " or (`subdistricts`.`english` LIKE '" . $search . "%')";
                    $i++;
                }
            }
            $orderby .= ") THEN 4 ";
            
            $orderby .= " WHEN ((`blocks`.`english` LIKE '" . $q[0] . "%')";
            if ($q) {
                $i=0;
                foreach ($q as $search) {
                    if($i != 0){
                        $orderby .= " or (`blocks`.`english` LIKE '" . $search . "%')";
                    }
                    $whereby .= " or (`blocks`.`english` LIKE '" . $search . "%')";
                    $i++;
                }
            }
            $orderby .= ") THEN 5 ";
            
            $orderby .= "WHEN (`villages`.`english` LIKE '%" . $q[0] . "%') THEN 6 ";
            $orderby .= 'ELSE 7 END) ASC';
            
            $list = Villages::find()->select(['villages.id', 'villages.country_id', 'villages.state_id', 'villages.district_id', 'villages.subdistrict_id', 'villages.block_id', 'villages.english', 'villages.pincode', 'villages.officename', 'villages.map', 'villages.gujarati', 'villages.hindi'])
                        ->where($whereby_v)
                        ->joinWith([
                                'block' => function ($query) use($q) {
                                    $query->select(['blocks.id', 'blocks.english', 'blocks.map', 'blocks.gujarati', 'blocks.hindi']);
                                },
                                'subdistrict' => function ($query) use($q) {
                                    $query->select(['subdistricts.id', 'subdistricts.english', 'subdistricts.map', 'subdistricts.gujarati', 'subdistricts.hindi']);
                                },
                                'district' => function ($query) use($q) {
                                    $query->select(['districts.id', 'districts.english', 'districts.map', 'districts.gujarati', 'districts.hindi']);
                                },
                                'state' => function ($query) use($q) {
                                    $query->select(['states.id', 'states.english', 'states.map', 'states.gujarati', 'states.hindi']);
                                },
                                'country' => function ($query) use($q) {
                                    $query->select(['countries.id', 'countries.english', 'countries.map', 'countries.gujarati', 'countries.hindi']);
                                }
                            ])   
                            ->andWhere(['villages.status' => 'Active'])
                            ->orderBy($orderby)        
                            ->limit(1000)->asArray()->all(); 
            $out = [];
            foreach ($list as $d) {
                $block = (isset($d['block']['english']))?$d['block']['english']:$d['subdistrict']['english'];
                $pincode = ($d['pincode'])?" ".$d['pincode']:"";
                $out[] = [
                    'id' => $d['id'],
                    'village' => $d['english'],
                    'other' => $block.', '.$d['district']['english'].', '.$d['state']['english'].', '.$d['country']['english'].''.$pincode,
                    'value' => $d['english'].', '.$block.', '.$d['district']['english'].', '.$d['state']['english'].', '.$d['country']['english'].''.$pincode,
                ];
            }
            return Json::encode($out);
        }
    }

}
