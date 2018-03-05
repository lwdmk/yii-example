<?php

\app\components\post\models\Mark::deleteAll();
\app\components\post\models\Post::deleteAll();
\app\components\user\models\User::deleteAll();


$objects = [
    'empty'      => [
        'title'        => '',
        'content'      => '',
        'author_login' => '',
        'author_ip'    => '',
    ],
    'wrongLogin' => [
        'title'        => 'test1',
        'content'      => 'test2',
        'author_login' => 'sdkf;ldskf;ls;lkjlk jlkj lkj kl jlk jlkj lkjl jlk jlkj lk jdfk sdl;fkdsl; fksd;lfk slf lkf sgdfkdkllfjs ljsdlf jlfj djf kf jdfj sdf jdfj l',
        'author_ip'    => '10.10.10.10',
    ],
    'wrongIp'    => [
        'title'        => 'test1',
        'content'      => 'test2',
        'author_login' => 'sdkf;ldskf;lsdfk sdl;fkdsl; fksd;lfk slf lkf sgdfkdkllfjs ljsdlf jlfj djf kf jdfj sdf jdfj l',
        'author_ip'    => '1065465465465464',
    ],
];

$I = new FunctionalTester($scenario);

$I->wantTo('try test /v1/post api');

foreach ($objects as $theme => $object) {
    $I->wantTo('try test ' . $theme);

    $I->sendPOST('/v1/post', $object);

    $I->seeResponseCodeIs(422);
    $I->seeResponseIsJson();
    $I->seeResponseJsonMatchesJsonPath('$.*.field');
    $I->seeResponseJsonMatchesJsonPath('$.*.message');
}

$I->wantTo('try test post creation');

$I->sendPOST('/v1/post', [
    'title'        => 'title',
    'content'      => 'content',
    'author_login' => 'author_login',
    'author_ip'    => '127.0.0.1',
]);

$I->seeResponseContainsJson();
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

$record = $I->grabRecord(\app\components\post\models\Post::class, [
    'title'        => 'title',
    'content'      => 'content',
    'author_login' => 'author_login',
    'author_ip'    => '127.0.0.1',
]);

$I->seeResponseContainsJson(['id' => $record->id]);


$I->wantTo('try to rate a post');

$wrongObjects = [
    'empty'       => [
        'post_id' => '',
        'mark'    => '',
    ],
    'wrongPostId' => [
        'title'   => '0',
        'content' => '2',
    ],
    'wrongMark'   => [
        'title'   => $record->id,
        'content' => '88',
    ],
];

foreach ($wrongObjects as $theme => $object) {
    $I->wantTo('try test ' . $theme);

    $I->sendPOST('/v1/post/mark', $object);

    $I->seeResponseCodeIs(422);
    $I->seeResponseIsJson();
    $I->seeResponseJsonMatchesJsonPath('$.*.field');
    $I->seeResponseJsonMatchesJsonPath('$.*.message');
}

$I->wantTo('try test mark creation');

$I->sendPOST('/v1/post/mark', [
    'post_id' => $record->id,
    'mark'    => 5
]);

$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

$I->haveInDatabase(\app\components\post\models\Mark::tableName(), [
    'post_id' => $record->id,
    'mark'    => 5
]);

$record = $I->grabRecord(\app\components\post\models\Post::class, [
    'id' => $record->id,
]);

$I->seeResponseContainsJson(['average_rate' => $record->average_rate]);


$I->wantTo('try test post top');

$I->sendGET('/v1/post/top', [
    'n' => 1,
]);

$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson([0 => ['title' => 'title']]);

$I->wantTo('try test ip list');

$I->sendPOST('/v1/post', [
    'title'        => 'title',
    'content'      => 'content',
    'author_login' => 'author_login2',
    'author_ip'    => '127.0.0.1',
]);

$I->sendGET('/v1/ip-list');

$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

$I->seeResponseContainsJson(['127.0.0.1' => ['author_login', 'author_login2']]);


