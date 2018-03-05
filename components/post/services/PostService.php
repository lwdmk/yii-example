<?php
namespace app\components\post\services;

use app\components\post\forms\MarkForm;
use app\components\post\models\Post;
use app\components\post\models\Mark;
use yii\base\BaseObject;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Сервис для работы с сущностью Поста
 */
class PostService extends BaseObject
{
    /**
     * Ключ для хранилища кэша
     */
    const TOP_POST_CACHE_KEY = 'top_post_cache_key', POST_IP_LIST_CACHE_KEY = 'post_ip_list_cache_key';

    /**
     * Максимальное кол-во постов на странице
     *
     * @var int
     */
    public $postMaxBatchSize = 100;

    /**
     * Кол-во постов на странице по умолчанию
     *
     * @var int
     */
    public $postDefaultBatchSize = 10;

    /**
     * Сохранение модели
     *
     * @param Post $model
     *
     * @return bool
     */
    public function save(Post &$model)
    {
        if($model->save()) {
            \Yii::$app->cache->delete(self::POST_IP_LIST_CACHE_KEY);
            return true;
        }
        return false;
    }

    /**
     * @param MarkForm $form
     *
     * @return bool
     */
    public function makeMark(MarkForm $form) {
        $mark = \Yii::createObject(Mark::class);
        $mark->setAttributes($form->getAttributes());

        if($mark->save()) {
            \Yii::$app->cache->delete(self::TOP_POST_CACHE_KEY);
            return true;
        }
        return false;
    }

    /**
     * @param Post $post
     *
     * @return int|mixed|string
     */
    public function countAverage(Post $post)
    {
        $average = \Yii::$app->db->createCommand('
            SELECT ROUND(AVG(mark)::numeric,2)
            FROM '.Mark::tableName() . ' 
            WHERE post_id = :post_id', [
            ':post_id' => $post->id
        ])->queryScalar();

        if(is_numeric($average)) {
            $post->average_rate = $average;
            $post->update(false, ['average_rate']);
        }

        return $post->average_rate;
    }

    /**
     * Найти сущность по PK
     *
     * @param $condition
     *
     * @return null|Post
     */
    public function getOneBy($condition)
    {
        return Post::findOne($condition);
    }

    /**
     * Топ N постов по среднему рейтингу
     *
     * @param int $limit
     *
     * @return ActiveDataProvider
     */
    public function getTopPosts($limit = 10)
    {
        if(false == ($result = ArrayHelper::getValue(\Yii::$app->cache->get(self::TOP_POST_CACHE_KEY), $limit, false))) {
            $result[$limit] = Post::find()->limit($this->correctLimit($limit))->orderBy('average_rate DESC')->all();
            \Yii::$app->cache->set(self::TOP_POST_CACHE_KEY, $result);
        }

        return $result;
    }

    /**
     * Получение сгруппированного списка где ключ IP, а значение - массив логинов авторов писавших пост с этого IP
     *
     * @return array
     */
    public function getIpLists()
    {
        if(false == ($result = \Yii::$app->cache->get(self::POST_IP_LIST_CACHE_KEY))) {
            $result = ArrayHelper::map(\Yii::$app->db->createCommand('
                SELECT author_ip,
                    string_agg(DISTINCT(users.login), \',\') as author_logins
                FROM posts main
                INNER JOIN users on users.id = main.author_id
                GROUP BY author_ip
                HAVING count(author_login) > 1
            ')->queryAll(), 'author_ip', function($item) {
                    return explode(',', $item['author_logins']);
            });

            \Yii::$app->cache->set(self::POST_IP_LIST_CACHE_KEY, $result);
        }

        return $result;
    }

    /**
     * Проверяем кол-во записей. Принудительно корректируем.
     *
     * @param $limit
     *
     * @return int
     */
    protected function correctLimit($limit)
    {
        if(! is_numeric($limit)) {
            return $this->postDefaultBatchSize;
        }

        if($limit > $this->postMaxBatchSize) {
            return $this->postMaxBatchSize;
        }

        return $limit;
    }
}