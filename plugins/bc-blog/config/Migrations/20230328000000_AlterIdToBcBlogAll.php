<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;
use Phinx\Db\Adapter\PostgresAdapter;

class AlterIdToBcBlogAll extends BcMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('blog_contents')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('blog_categories')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('blog_posts')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('blog_comments')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('blog_posts_blog_tags')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
        $this->table('blog_tags')
            ->changeColumn('id', 'integer', [
                'identity' => true,
                'generated' => PostgresAdapter::GENERATED_BY_DEFAULT
            ])
            ->update();
    }

}
