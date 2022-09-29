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
                'contents' => '<p>This is ucmitz Project.</p>',
                'draft' => '',
                'page_template' => '',
                'modified' => '2022-10-01 09:00:00',
                'created' => '2022-10-01 09:00:00',
            ],
        ];

        $table = $this->table('pages');
        $table->insert($data)->save();
    }
}
