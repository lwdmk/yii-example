<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$components = \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../components/post/config/main.php'),
    require(__DIR__ . '/../components/user/config/main.php')
);

$config = [
    'id'         => 'basic',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container'  => [
        'singletons'  => [
            app\managers\PostManager::class => \app\managers\PostManager::class
        ],
    ],
    'modules' => [
        'v1' => app\modules\v1\Module::class
    ],
    'components' => [
        'request'    => [
            'cookieValidationKey' => '1Hle5Zw3vIhj54_rPImuEH1uwmPj9YRR',
        ],
        'response'   => [
            'format' => \yii\web\Response::FORMAT_JSON
        ],
        'cache'      => [
            'class' => 'yii\caching\FileCache',
        ],
        'user'       => [
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'log'        => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'         => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
                'POST <module:\w+>/post'    => '<module>/post/create',
                'POST <module:\w+>/mark'    => '<module>/post/mark',
                'GET <module:\w+>/ip-list'  => '<module>/post/ip-list',
                'GET <module:\w+>/post/top' => '<module>/post/top',
            ],
        ],
    ],
    'params'     => $params,
];

return \yii\helpers\ArrayHelper::merge($components, $config);
