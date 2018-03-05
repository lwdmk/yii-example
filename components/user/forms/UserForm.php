<?php

namespace app\components\user\forms;

use app\components\user\models\User;
use yii\base\Model;

/**
 * Форма создания нового пользователя
 */
class UserForm extends Model
{
    /**
     * Логин пользователя
     *
     * @var string
     */
    public $login;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'unique', 'targetClass' => User::class]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => 'Login'
        ];
    }
}