<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

$components = \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../components/post/config/main.php'),
    require(__DIR__ . '/../components/user/config/main.php')
);

$config = [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'container'  => [
        'singletons'  => [
            app\managers\PostManager::class => \app\managers\PostManager::class
        ],
    ],
    'modules' => [
        'v1' => app\modules\v1\Module::class
    ],
    'components' => [
        'db' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => true,
            'rules'           => [
                'POST <module:\w+>/post'    => '<module>/post/create',
                'POST <module:\w+>/mark'    => '<module>/post/mark',
                'GET <module:\w+>/ip-list'  => '<module>/post/ip-list',
                'GET <module:\w+>/post/top' => '<module>/post/top',
            ],
        ],
        'response'   => [
            'format' => \yii\web\Response::FORMAT_JSON
        ],
        'cache'      => [
            'class' => yii\caching\DummyCache::class,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ],
    'params' => $params,
];

return \yii\helpers\ArrayHelper::merge($components, $config);

