<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContentFolder Fixture
 */
class ContentFoldersFixture extends TestFixture
{

    /**
     * Import
     *
     * @var array
     */
    public $import = ['table' => 'content_folders'];

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => '1',
                'folder_template' => 'フォルダーテンプレート1',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                'id' => '2',
                'folder_template' => 'フォルダーテンプレート2',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                'id' => '3',
                'folder_template' => 'フォルダーテンプレート3',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                'id' => '10',
                'folder_template' => '削除済みフォルダー(親)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                'id' => '11',
                'folder_template' => '削除済みフォルダー(子)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
        ];
        parent::init();
    }
}
