<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\ORM\Entity;
use ReflectionClass;
use Cake\Filesystem\File;
use BaserCore\TestSuite\BcTestCase;
use Laminas\Diactoros\UploadedFile;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcUploadBehavior;
use BaserCore\Service\ContentServiceInterface;

/**
 * Class BcSearchIndexManagerBehavioreTest
 *
 * @package Baser.Test.Case.Model
 */
class BcSearchIndexManagerBehaviorTest extends BcTestCase
{

    public $fixtures = [];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }


    /**
     * コンテンツデータを登録する
     *
     * @param Model $model
     * @param array $data
     * @return boolean
     */
    public function testSaveSearchIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツデータを削除する
     */
    public function testDeleteSearchIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツメタ情報を更新する
     */
    public function testUpdateSearchIndexMeta()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
