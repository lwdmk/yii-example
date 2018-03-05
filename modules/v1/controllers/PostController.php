<?php
namespace app\modules\v1\controllers;

use app\components\ApiController;
use app\components\post\forms\MarkForm;
use app\components\post\forms\PostForm;
use app\components\post\services\PostService;
use app\managers\PostManager;
use yii\base\Model;
use yii\base\Module;

class PostController extends ApiController
{
    /**
     * @var PostManager
     */
    public $postManager;

    /**
     * @var PostService
     */
    public $postService;

    /**
     * PostController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param array $config
     * @param PostManager $postManager
     * @param PostService $postService
     */
    public function __construct(
        $id,
        Module $module,
        array $config = [],
        PostManager $postManager,
        PostService $postService
    ) {
        $this->postManager = $postManager;
        $this->postService = $postService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Создание поста
     *
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var Model $form */
        $form = (\Yii::createObject(PostForm::class));
        $form->load(\Yii::$app->request->post(), '');

        if ($form->validate()) {
            if ($post = $this->postManager->createPost($form)) {
                return $this->serializer->serialize($post);
            }
        }
        return $this->serializer->serialize($form);
    }

    /**
     * Установка оценки для поста
     *
     * @return mixed
     */
    public function actionMark()
    {
        /** @var Model $form */
        $form = (\Yii::createObject(MarkForm::class));
        $form->load(\Yii::$app->request->post(), '');
        if ($form->validate()) {

            if ($post = $this->postManager->makeMark($form)) {
                return $this->serializer->serialize(['average_rate' => $form->average_rate]);
            }
        }
        return $this->serializer->serialize($form);
    }

    /**
     * Получение записей с максимальным рейтингом
     *
     * @param int $n Кол-во записей
     *
     * @return mixed
     */
    public function actionTop($n = 10)
    {
        return $this->serializer->serialize($this->postService->getTopPosts($n));
    }

    /**
     * Получение списков пользователей с группировкой по IP адрессам
     *
     * @return mixed
     */
    public function actionIpList()
    {
        return $this->serializer->serialize($this->postService->getIpLists());
    }
}