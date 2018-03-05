<?php

use yii\db\Migration;

/**
 * Class m180301_184229_init_tables
 */
class m180301_184229_init_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id'    => $this->primaryKey(),
            'login' => $this->string(100)->unique()
        ]);

        $this->createTable('posts', [
            'id'           => $this->primaryKey(),
            'title'        => $this->string(200),
            'content'      => $this->text(),
            'author_login' => $this->string(100),
            'average_rate' => $this->decimal(4,2)->unsigned()->defaultValue(0),
            'author_ip'    => 'inet',
            'author_id'    => $this->integer()
        ]);

        $this->createTable('marks', [
            'id'      => $this->primaryKey(),
            'post_id' => $this->integer(),
            'mark'    => $this->tinyInteger()->unsigned()
        ]);

        $this->addForeignKey('marks_post_id_fk', 'marks', 'post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('posts_author_login_fk', 'posts', 'author_login', 'users', 'login', 'SET NULL', 'CASCADE');
        $this->addForeignKey('posts_author_id_fk', 'posts', 'author_id', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('posts_author_login_fk', 'posts');
        $this->dropForeignKey('marks_post_id_fk', 'marks');
        $this->dropTable('marks');
        $this->dropTable('posts');
        $this->dropTable('users');
    }
}
