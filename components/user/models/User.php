<?php
namespace app\components\user\models;

use yii\db\ActiveRecord;

/**
 * Модель User
 *
 * @property int    $id
 * @property string $login
 *
 */
class User extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            [['login'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'login' => 'Login'
        ];
    }
}