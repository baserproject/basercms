<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * ContentFolders seed.
 */
class ContentFoldersSeed extends AbstractSeed
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
                'folder_template' => '',
                'page_template' => '',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 2,
                'folder_template' => '',
                'page_template' => '',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 3,
                'folder_template' => '',
                'page_template' => '',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => 4,
                'folder_template' => '',
                'page_template' => '',
                'created' => '2022-10-01 09:00:00',
                'modified' => NULL,
            ],
        ];

        $table = $this->table('content_folders');
        $table->insert($data)->save();
    }
}
