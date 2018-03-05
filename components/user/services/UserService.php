<?php
namespace app\components\user\services;

use app\components\user\models\User;
use yii\base\BaseObject;
use yii\base\Model;

/**
 * Сервис работы с сущностью Юзера
 */
class UserService extends BaseObject
{
    /**
     * Сохранение модели
     *
     * @param User $model
     *
     * @return bool
     */
    public function save(User &$model)
    {
        return $model->save();
    }

    /**
     * Найти сущность по условию
     *
     * @param $condition
     *
     * @return null|User
     */
    public function getOneBy($condition)
    {
        return User::findOne($condition);
    }

    /**
     * Создание пользователя, если такого еще не было
     *
     * @param Model $form
     *
     * @return bool
     */
    public function createIfNotExist(Model &$form)
    {

        if(isset($form->author_login)) {
           if(null == ($user = $this->getOneBy(['login' => $form->author_login]))) {
                /** @var User $user */
                $user = \Yii::createObject(User::class);
                $user->login = $form->author_login;

                if($this->save($user)) {
                    $form->author_id = $user->id;
                    return true;
                } else {
                    $form->addError('author_login', $user->getFirstError('login'));
                    return false;
                }
            } else {
               $form->author_id = $user->id;
               return true;
            }
        }
        $form->addError('author_login', 'Author login is required');
        return false;
    }
}