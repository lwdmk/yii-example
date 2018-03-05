<?php

use yii\db\Migration;

/**
 * Class m180301_184229_init_tables
 */
class m180303_184229_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('posts_average_rate', 'posts', 'average_rate');
        $this->createIndex('posts_author_ip', 'posts', 'author_ip');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('posts_average_rate', 'posts');
        $this->dropIndex('posts_author_ip', 'posts');
    }
}
