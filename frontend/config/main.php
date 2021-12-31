<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);
use \yii\web\Request;
$baseUrl = str_replace('/frontend/web', '', (new Request)->getBaseUrl());
return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'ApiCallFunctions' => [
            'class' => 'frontend\components\ApiCallFunctions'
        ],
        'FrontFunctions' => [
            'class' => 'frontend\components\FrontFunctions'
        ],
        'GetObject' => [
            'class' => 'frontend\components\GetObject'
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
                // 'kartik\form\ActiveFormAsset' => [
                //     'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                // ],
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => $baseUrl,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/vandemission/',
            'rules' => [
            ],
        ],

        'urlManagerBackEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '/adminpanel',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ]
    ],
    'params' => $params,
];
