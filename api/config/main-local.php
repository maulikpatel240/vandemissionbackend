<?php
return [
    'language' => 'en-US',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=belocum',
            'username' => 'root',
            'password' => 'WfJDA0pXMeUxSnLT',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'info@belocum.com',
                //'password' => 'jFM#Ujr9Z!',
                'password' => '}57r6;sEX*sN8/#~', 
                'port' => 587,
                'encryption' => 'tls',
            ],
            'useFileTransport' => false,
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        //'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        // 'i18n' => [
        //     'translations' => [
        //         'frontend*' => [
        //             'class' => 'yii\i18n\PhpMessageSource',
        //             'basePath' => '@common/messages',
        //         ],
        //         'backend*' => [
        //             'class' => 'yii\i18n\PhpMessageSource',
        //             'basePath' => '@common/messages',
        //         ],
        //     ],
        // ],
        
    ],
];
