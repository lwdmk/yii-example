<?php
namespace app\components\post\models;

use yii\db\ActiveRecord;

/**
 * Модель Mark. Оценка поста
 *
 * @property int $id
 * @property int $post_id
 * @property int $mark
 *
 */
class Mark extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'marks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'mark'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'post_id' => 'Post ID',
            'mark'    => 'Mark'
        ];
    }
}