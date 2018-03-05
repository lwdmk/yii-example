<?php

namespace app\components;

use yii\rest\Serializer;

/**
 * Родительский контроллер для апи
 */
class ApiController extends \yii\web\Controller
{
    /**
     * @var Serializer
     */
    public $serializer = Serializer::class;

    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->serializer = \Yii::createObject($this->serializer);
        parent::init();
    }
}
