<?php
use yii\web\HttpException;
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);
return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gridview' => [
            'class' => 'kartik\grid\Module',
//            'downloadAction' => 'gridview/export/download',
        ],
        'gridviewKrajee' =>  [
            'class' => '\kartik\grid\Module',
            // your other grid module settings
        ]
    ],
    'on beforeAction' => function ($event) {
           $controller = $event->action->controller->id;
           $action = $event->action->id;
//           $access = array();
//           $access['controller'] = $controller;
//           $access['action'] = $action;
//           $check = Yii::$app->BackFunctions->checkaccess($access);
//           if($check){
//               return true;
//           }else{
//               throw new \yii\web\NotFoundHttpException("You don't have permission to access on this role.");
//           }
    },
    'components' => [
        'cacheBackend' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => Yii::getAlias('@backend') . '/runtime/cache'
        ],
        'BackFunctions' => [
            'class' => 'backend\components\BackFunctions'
        ],
        'SqlFunctions' => [
            'class' => 'backend\components\SqlFunctions'
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'class' => 'common\components\Request',
            'web'=> '/backend/web',
            'adminUrl' => '/adminpanel'
        ],
        'assetManager' => [
            'forceCopy' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'jsOptions' => [ 'position' => \yii\web\View::POS_HEAD ],
                ],
//                'yii\bootstrap5\BootstrapPluginAsset' => [
//                    'js'=>[]
//                ],
//                'yii\bootstrap5\BootstrapAsset' => [
//                    'css' => [],
//                ],
                 'kartik\form\ActiveFormAsset' => [
                     'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                 ],
            ],
        ],
        'user' => [
            'identityClass' => 'backend\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                'db' => [
                    'class' => 'yii\log\DbTarget',
                ]
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if(Yii::$app->response->statusCode==500)
                {
                   //$response->redirect('site/error');
                }
            },
        ],
         'urlManager' => [
             'enablePrettyUrl' => true,
             'showScriptName' => false,
             'rules' => [
                // '<action>'=>'site/<action>'
             ],
         ],
                    
         'urlManagerFrontEnd' => [
             'class' => 'yii\web\urlManager',
             'baseUrl' => '/',
             'enablePrettyUrl' => true,
             'showScriptName' => false,
         ],           
        
    ],
    'params' => $params,
];
