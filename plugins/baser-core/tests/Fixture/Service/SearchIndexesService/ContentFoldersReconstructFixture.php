<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture\Service\SearchIndexesService;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContentFolder Fixture
 */
class ContentFoldersReconstructFixture extends TestFixture
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
                'folder_template' => '',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
            [
                'id' => 2,
                'folder_template' => '',
                'page_template' => '',
                'created' => '2016-08-10 02:17:28',
                'modified' => null
            ],
        ];
        parent::init();
    }
}
