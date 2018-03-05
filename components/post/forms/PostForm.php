<?php
namespace app\components\post\forms;

use yii\base\Model;

/**
 * Форма обработки создания нового поста
 */
class PostForm extends Model
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $author_login;

    /**
     * @var string
     */
    public $author_ip;

    /**
     * @var integer
     */
    public $author_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'author_login', 'content', 'author_ip'], 'required'],
            [['title'], 'string', 'max' => 200],
            [['author_login'], 'string', 'max' => 100],
            [['author_ip'], 'ip'],
            [['title', 'content', 'author_login', 'author_ip', 'author_id'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title'        => 'Title',
            'author_login' => 'Author Login',
            'content'      => 'Content',
            'author_ip'    => 'Author Ip',
            'author_id'    => 'Author ID'
        ];
    }
}