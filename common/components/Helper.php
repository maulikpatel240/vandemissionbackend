<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;

class Helper extends Component {

    public function init() {
        parent::init();
    }

    public function cookiedestory() {
        $key = Yii::$app->user->identityCookie['name'];
        $cookie = Yii::$app->request->cookies->getValue($key);
        if ($cookie) {
            $cookie = json_decode($cookie, true);
        }
        $result = array();
        $result['type'] = "";
        $result['result'] = false;
        if (Yii::$app->user->identity) {
            $result['type'] = Yii::$app->user->identity->type;
            if (isset($cookie[1]) && $cookie[1] != Yii::$app->user->identity->authkey) {
                $result['result'] = true;
            }
        }
        return $result;
    }

    public function array_remove_by_value($array, $value) {
        return array_values(array_diff($array, array($value)));
    }

    public function get_times($default = '19:00', $interval = '+30 minutes', $endtime = "") {

        $output = array();

        $current = strtotime('00:00');
        $end = strtotime('23:59');

        while ($current <= $end) {
            $time = date('H:i', $current);
            $sel = ( $time == $default ) ? ' selected' : '';

            $output[$time] = $time;
            $current = strtotime($interval, $current);
            if ($endtime && $endtime == $time) {
                break;
            }
        }

        return $output;
    }

    // Array Search Function
//    Ex. // Array Data Of Users
//  $userdb = array (
//      array ('uid' => '100','name' => 'Sandra Shush','url' => 'urlof100' ),
//      array ('uid' => '5465','name' => 'Stefanie Mcmohn','url' => 'urlof100' ),
//      array ('uid' => '40489','name' => 'Michael','url' => 'urlof40489' ),
//  );
//  
//  // Obtain The Key Of The Array
//  $arrayKey = searchArrayKeyVal("uid", '100', $userdb);
    public function searchArrayKeyVal($sKey, $id, $array) {
        foreach ($array as $key => $val) {
            if ($val[$sKey] == $id) {
                return $key;
            }
        }
        return false;
    }

    public function multiSearch(array $array, array $pairs, $key_sorting = "ASC") {
        $found = array();
        foreach ($array as $aKey => $aVal) {
            $coincidences = 0;
            foreach ($pairs as $pKey => $pVal) {
                if (array_key_exists($pKey, $aVal) && $aVal[$pKey] == $pVal) {
                    $coincidences++;
                }
            }
            if ($coincidences == count($pairs)) {
                if ($key_sorting == "ASC") {
                    $found[] = $aVal;
                } else {
                    $found[$aKey] = $aVal;
                }
            }
        }

        return $found;
    }

    public function removeElementWithValue($array, $key, $value) {
        foreach ($array as $subKey => $subArray) {
            if ($subArray[$key] == $value) {
                unset($array[$subKey]);
            }
        }
        return $array;
    }

    public function removeElementWithValuebyGetID($array, $key, $value) {
        $getkeyvalue = array();
        foreach ($array as $subKey => $subArray) {
            if ($subArray[$key] == $value) {
                unset($array[$subKey]);
            }
        }
        return array_values($array);
    }

    public function getarraycolumn($array = array(), $key = "", $string = true) {
        if ($array && $key) {
            $array = array_column($array, $key);
            foreach ($array AS $index => $value) {
                if ($string) {
                    $array[$index] = "'" . $value . "'";
                }
            }
        }
        return $array;
    }

    public function check_cc($cc, $extra_check = false) {
//        $cards = array(
//            "4111 1111 1111 1111",
//        );
//
//        foreach($cards as $c){
//            $check = check_cc($c, true);
//            if($check!==false)
//                echo $c." - ".$check;
//            else
//                echo "$c - Not a match";
//            echo "<br/>";
//        }
        $cards = array(
            "visa" => "(4\d{12}(?:\d{3})?)",
            "amex" => "(3[47]\d{13})",
            "jcb" => "(35[2-8][89]\d\d\d{10})",
            "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
            "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
            "mastercard" => "(5[1-5]\d{14})",
            "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
        );
        $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch");
        $matches = array();
        $pattern = "#^(?:" . implode("|", $cards) . ")$#";
        $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
        if ($extra_check && $result > 0) {
            $result = (validatecard($cc)) ? 1 : 0;
        }
        return ($result > 0) ? $names[sizeof($matches) - 2] : false;
    }

    public function tohtmlcode($string = "") {
        $messageString = "";
        if ($string) {
            $htmlcode = Htmlcode::find()->where(['status' => 'Active'])->andWhere(['!=', 'named_code', ""])->all();
            $htmlcodeArray = ArrayHelper::map($htmlcode, 'charkey', 'named_code');
            //$combine=array_combine(Yii::$app->params['specialCharacter'],Yii::$app->params['htmlcodeCharacter']);
            $messageString = strtr($string, $htmlcodeArray);
            //print_r($messageString);exit;
        }
        return $messageString;
    }

    public function removeurlvar($url, $varname) {
        list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
        parse_str($qspart, $qsvars);
        unset($qsvars[$varname]);
        $newqs = http_build_query($qsvars);
        return $urlpart . '?' . $newqs;
    }

    public function checkfile($file = "", $type = 'image') {
        if (isset($file->postname) && $file->postname) {
            $file = $file->postname;
        }
        $output = array();
        $typeArrayExt = array();
        if ($type == 'image') {
            $typeArrayExt = Yii::$app->params['IMAGE_EXTENTION'];
        }
        $output['result'] = false;
        $output['typeArrayExt'] = $typeArrayExt;
        $output['ext'] = "";
        if ($file) {
            $extexplode = explode(".", $file);
            if ($extexplode) {
                $ext = end($extexplode);
                if (in_array($ext, $typeArrayExt)) {
                    $output['result'] = true;
                    $output['ext'] = $ext;
                }
            }
        }
        return $output;
    }

}
