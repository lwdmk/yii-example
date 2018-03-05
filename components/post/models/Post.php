<?php
namespace app\components\post\models;

use yii\db\ActiveRecord;

/**
 * Модель Post. Запись
 *
 * @property int    $id
 * @property int    $author_id
 * @property string $title
 * @property string $content
 * @property string $author_login
 * @property string $author_ip
 * @property float  $average_rate
 *
 */
class Post extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id'], 'required'],
            [['title', 'content', 'author_login', 'author_ip'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'author_id'    => 'Author Id',
            'title'        => 'Title',
            'content'      => 'Content',
            'author_login' => 'Author_login',
            'author_ip'    => 'Author Ip',
            'average_rate' => 'Average Rate'
        ];
    }
}