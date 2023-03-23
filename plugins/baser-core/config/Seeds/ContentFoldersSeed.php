<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * ContentFolders seed.
 */
class ContentFoldersSeed extends BcSeed
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
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'folder_template' => '',
                'page_template' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 2,
                'folder_template' => '',
                'page_template' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 3,
                'folder_template' => '',
                'page_template' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => 4,
                'folder_template' => '',
                'page_template' => '',
                'created' => NULL,
                'modified' => NULL,
            ],
        ];

        $table = $this->table('content_folders');
        $table->insert($data)->save();
    }
}
