<?php

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'timeZone' => 'UTC',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => [
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ],
        'PushNotification' => [
            'class' => 'common\components\PushNotification'
        ],
        'Helper' => [
            'class' => 'common\components\Helper'
        ],
        'AppStart' => [
            'class' => 'common\components\AppStart'
        ],
        'GeneralFunctions' => [
            'class' => 'common\components\GeneralFunctions'
        ],
        'FunctionsOne' => [
            'class' => 'common\components\FunctionsOne'
        ],
        'FunctionsTwo' => [
            'class' => 'common\components\FunctionsTwo'
        ],
        'FunctionsThree' => [
            'class' => 'common\components\FunctionsThree'
        ],
        'OnesignalConfig' => [
            'class' => 'common\components\OnesignalConfig'
        ],
        'PaypalConfig' => [
            'class' => 'common\components\PaypalConfig'
        ],
        'PhpMailerConfig' => [
            'class' => 'common\components\PhpMailerConfig'
        ],
        'StripeConfig' => [
            'class' => 'common\components\StripeConfig'
        ],
    ],
];
