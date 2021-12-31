<?php

namespace backend\components;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;
use backend\models\Config;

class BaseController extends Controller {

    public function init() {
        parent::init();
        global $vm;
        $vm = array();
        $vm['base_url'] = $vm['site_url'] = Url::base(true);
        $vm['base_path'] = $vm['site_path'] = Url::base();
        $adminuser = Yii::$app->user->identity;
        $vm['admin'] = $adminuser;
    }
}
