<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
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
            'class' => 'common\components\LangUrlManager',
            'rules' => [
                'http://abc.itdesign.ru' => 'hotel/index',
                'http://abc2.itdesign.ru' => 'hotel/index',
                '/' => 'site/index',
                '/signup' => 'site/signup',
                '/login' => 'site/login',
                '/logout' => 'site/logout',
                'POST /hotel/search' => 'hotel/search',
//	            'POST /hotel/booking' => 'hotel/booking',
                '/hotel/test' => 'hotel/test',
                '/payment/<id:[0-9a-zA-Z\-_]{64}>' => 'payment/show',
                'GET /hotel/<name:\w+>' => 'hotel/index',
	            '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
            ]
        ],
        'request' => [
            'class' => 'common\components\LangRequest'
        ],


    ],
    'params' => $params,
];
