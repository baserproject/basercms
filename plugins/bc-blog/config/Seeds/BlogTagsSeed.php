<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * BlogTags seed.
 */
class BlogTagsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'name' => '新製品',
                'created' => 
                Cake\I18n\FrozenTime::__set_state(array(
                'date' => '2016-08-12 00:48:33.000000',
                'timezone_type' => 3,
                'timezone' => 'Asia/Tokyo',
                )),
                'modified' => NULL,
            ],
        ];

        $table = $this->table('blog_tags');
        $table->insert($data)->save();
    }
}
