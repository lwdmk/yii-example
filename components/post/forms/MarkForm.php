<?php
namespace app\components\post\forms;

use app\components\post\models\Post;
use yii\base\Model;

/**
 * Форма обработки создания оценки для поста, учет средней оценки после создания
 */
class MarkForm extends Model
{
    /**
     * @var integer
     */
    public $post_id;

    /**
     * @var integer
     */
    public $mark;

    /**
     * @var double
     */
    public $average_rate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'mark'], 'required'],
            [['post_id', 'mark'], 'filter', 'filter' => 'intval'],
            [['post_id', 'mark'], 'integer', 'min' => 1],
            [['post_id'], 'exist', 'targetClass' => Post::class, 'targetAttribute' => 'id'],
            [['mark'], 'in', 'range' => range(1, 5, 1)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'post_id'      => 'Post ID',
            'mark'         => 'Mark',
            'average_rate' => 'Average Rate',
        ];
    }
}