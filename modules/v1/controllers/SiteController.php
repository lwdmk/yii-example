<?php
namespace modules\v1\controllers;

use app\components\ApiController;
use yii\web\NotFoundHttpException;

class SiteController extends ApiController
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        throw new NotFoundHttpException();
    }
}