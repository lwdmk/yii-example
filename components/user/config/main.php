<?php
use app\components\user\services\UserService;
use app\components\user\forms\UserForm;

return [
    'container' => [
        'singletons' => [
            UserService::class => UserService::class
        ],
        'definitions' => [
            UserForm::class => UserForm::class
        ]
    ]
];