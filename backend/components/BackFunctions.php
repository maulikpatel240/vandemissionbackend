<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;
use backend\models\WoConfig;
use backend\models\Permission;
use backend\models\RoleAccess;
use backend\models\Modules;
use backend\models\EmailTemplates;
use yii\db\TableSchema;
use Aws\S3\S3Client;
use Google\Cloud\Storage\StorageClient;

class BackFunctions extends Component {

    public function init() {
        parent::init();
    }

    public function message($key) {
        $message = array();
        $message['insert'] = "Created successfully.";
        $message['update'] = "Updated successfully.";
        $message['delete'] = "Deleted successfully.";
        $message['status_change'] = "Status changed.";

        if ($key) {
            return $message[$key];
        }
        return '';
    }

    public function checkaccess($action = "", $controller = "") {
        global $vm;
        $adminuser = $vm['admin'];
        if ($action && $controller) {
            $modulesdata = Modules::find()->where(['status' => 'Active', 'controller' => $controller, 'action' => $action])->one();
            if (empty($modulesdata)) {
                $modulesdata = Modules::find()->where(['status' => 'Active', 'controller' => $controller])->one();
            }
            if ($modulesdata) {
                $permission = Permission::find()->where(['module_id' => $modulesdata->id, 'controller' => $controller])->andWhere(['or', ['action' => $action], ['name' => $action]])->one();
                if ($permission) {
                    $module_access = RoleAccess::find()->where(['role_id' => $adminuser->role_id, 'permission_id' => $permission->id, 'access' => '1'])->one();
                    if ($module_access) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function currentDateTime() {
        $format = 'Y-m-d H:i:s';
        return date($format);
    }

    public function currentDate() {
        $format = 'Y-m-d';
        return date($format);
    }

    public function currentTime($format = '24') {
        $format = 'H:i:s';
        if ($format == 12) {
            $format = 'H:i A';
        }
        return date($format);
    }

    function array_msort($array, $cols) {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k]))
                    $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }

    public function unlinkimage($imageName = '') {
        $image = Yii::getAlias('@webroot') . '/uploads/state/' . $imageName;
        if (unlink($image)) {
            return true;
        }
        return false;
    }

    public function restcurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $output = json_decode($output, true);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            //var_dump($output);
        }
        curl_close($ch);
        return $output;
    }

    public function sendmail($email, $code,$field=array()) {
        $EmailTemplates = EmailTemplates::find()->where(['code' => $code])->one();
        if(empty($EmailTemplates)){
            return false;
        }
        $action_url = Url::to([Yii::$app->request->ActualBaseUrl], true);
        $name = "Tesr";
        
        $logo = "<a href='" . Url::to([Yii::$app->request->ActualBaseUrl], true) . "'><img src='" . Yii::$app->params['logo_img'] . "' alt='Logo' style='width: 94px;'></a>";
        $site_url = Yii::$app->params['site_url'];
        $site_name = Yii::$app->params['site_name'];
        $site_address = Yii::$app->params['site_address'];
        $fromEmail = Yii::$app->params['support_email'];
        $subject = $EmailTemplates->subject;
        $toEmail = $email;
        $copyright_text = str_replace('{year}', date('Y'), Yii::$app->params['copyright_text']);
        
        $message = $EmailTemplates->html;
        $message = str_replace('{{logo}}', $logo, $message);
        $message = str_replace('{{site_url}}', $site_url, $message);
        $message = str_replace('{{site_name}}', $site_name, $message);
        $message = str_replace('{{site_address}}', $site_address, $message);
        $message = str_replace('{{copyright_text}}', $copyright_text, $message);
        $message = str_replace('{{name}}', $name, $message);
        $message = str_replace('{{action_url}}', $action_url, $message);
        

        if($field){
            foreach ($field as $key=>$value){
                $message = str_replace($key, $value, $message);
            }
        }
      
        $sendemail = Yii::$app->mailer->compose()
                ->setFrom($fromEmail)
                ->setTo($toEmail)
                ->setSubject($subject)
                ->setTextBody('Plain text content')
                ->setHtmlBody($message)
                ->send();
        return true;
    }

    public function sendotp($phone_number = "") {
        
        return true;
    }

}
