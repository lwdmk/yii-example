<?php
namespace app\managers;

use app\components\post\models\Post;
use app\components\post\services\PostService;
use app\components\user\services\UserService;
use yii\base\BaseObject;
use yii\base\Model;

/**
 * Класс для межкомпонентного взаимодействия POST-USER
 */
class PostManager extends BaseObject
{
    /**
     * @var PostService
     */
    public $postService;

    /**
     * @var UserService
     */
    public $userService;

    /**
     * PostManager constructor.
     *
     * @param array $config
     * @param PostService $postService
     * @param UserService $userService
     */
    public function __construct(array $config = [], PostService $postService, UserService $userService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
        parent::__construct($config);
    }

    /**
     * Создание поста
     *
     * @param Model $form
     *
     * @return bool|Post
     */
    public function createPost(Model &$form)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            if($this->userService->createIfNotExist($form)) {
                /** @var Post $post */
                $post = \Yii::createObject(Post::class);
                $post->setAttributes($form->getAttributes());
                if($this->postService->save($post)) {
                    $transaction->commit();
                    return $post;
                } else {
                    $form->errors = $post->getErrors();
                    $transaction->rollBack();

                    return false;
                }
            } else {
                $transaction->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            $form->addError('title', $this->getErrorMessage($e));
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * Установка оценки для поста
     *
     * @param Model $form
     *
     * @return bool
     */
    public function makeMark(Model &$form)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $post = $this->postService->getOneBy(['id' =>$form->post_id]);
            if(null == $post) {
                $form->addError('post_id', 'Record not found');
                return false;
            }

            if($this->postService->makeMark($form)) {
                $form->average_rate = $this->postService->countAverage($post);
                $transaction->commit();
                return true;
            }
            $transaction->rollBack();

            return false;

        } catch (\Exception $e) {
            $form->addError('title', $this->getErrorMessage($e));
            $transaction->rollBack();

            return false;
        }
    }

    /**
     * Обертка получения сообщения о ошибке при исключении
     *
     * @param \Exception $e
     *
     * @return string
     */
    protected function getErrorMessage(\Exception $e)
    {
        return YII_DEBUG ? $e->getMessage() : 'Internal server error, please try again later';
    }
}