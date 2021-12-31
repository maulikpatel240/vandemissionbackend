<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
); 

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'users/index',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
        //1) Staff user allow to login without approved , just changed the api url format
        'A202009021050' => [
            'class' => 'api\modules\A202009021050\Module',
        ],
        //1) Dental Care Starts , Nursing Care connected 
        'A202009040312' => [
            'class' => 'api\modules\A202009040312\Module',
        ],
        //1) ROC Starts
        //2) Modification Done in last version
        //3) Cancellation Done in last version
        //4) Incrase Hourly rates Done in last version
        //5) New Website Structure starts from this version
        'A202011271057' => [
            'class' => 'api\modules\A202011271057\Module',
        ],

        
    ],
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'class' => 'common\components\Request',
            'web'=> '/api/web',
            'adminUrl'=>'/restapi'
            //'cookieValidationKey' => 'vuul04eBWxvWDO2--J4R6RP6A0oDNGnm',
         ],
        'user' => [
            'identityClass' => 'common\models\Admin',
            'enableAutoLogin' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
              'maxSourceLines' => 20,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'urlManagerFrontEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'urlManagerBackEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '/adminpanel',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];
