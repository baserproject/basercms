<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Pages seed.
 */
class PagesSeed extends AbstractSeed
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
                'id' => '2',
                'contents' => '<p>座右の銘「やるときゃやる」</p>',
                'draft' => '',
                'page_template' => '',
                'modified' => '2017-05-03 15:12:27',
                'created' => '2015-06-26 20:34:06',
            ],
        ];

        $table = $this->table('pages');
        $table->insert($data)->save();
    }
}
