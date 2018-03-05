<?php
use app\components\post\services\PostService;
use app\components\post\forms\PostForm;
use app\components\post\forms\MarkForm;

return [
    'container' => [
        'singletons' => [
            PostService::class => PostService::class
        ],
        'definitions' => [
            PostForm::class => PostForm::class,
            MarkForm::class => MarkForm::class
        ]
    ]
];