<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AddAuthCaptchaToBlogContents extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('blog_contents')
            ->addColumn('auth_captcha', 'boolean', [
                'after' => 'comment_approve',
                'default' => false,
                'null' => true,
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('blog_contents')
            ->removeColumn('auth_captcha')
            ->update();
    }
}
