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
                // NOTE: contentFixtureのbaserCMSサンプル
                'id' => '1',
                'folder_template' => 'baserCMSサンプル',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                // NOTE: contentFixtureのサービス
                'id' => '4',
                'folder_template' => 'サービスフォルダー',
                'page_template' => 'サービスページ',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                // NOTE: contentFixtureの削除済みフォルダー(親)
                'id' => '10',
                'folder_template' => '削除済みフォルダー(親)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                // NOTE: contentFixtureの削除済みフォルダー(親)
                'id' => '11',
                'folder_template' => '削除済みフォルダー(子)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                // NOTE: contentFixtureのツリー階層削除用フォルダー(親)
                'id' => '12',
                'folder_template' => 'ツリー階層削除用フォルダー(親)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                // NOTE: contentFixtureのツリー階層削除用フォルダー(子)
                'id' => '13',
                'folder_template' => 'ツリー階層削除用フォルダー(子)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                // NOTE: contentFixtureのツリー階層削除用フォルダー(孫)
                'id' => '14',
                'folder_template' => 'ツリー階層削除用フォルダー(孫)',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                // NOTE ucmitz: contentFixtureのtestEdit
                'id' => '15',
                'folder_template' => 'testEdit',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
        ];
        parent::init();
    }
}
