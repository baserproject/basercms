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

namespace BaserCore\Test\TestCase\Controller\Admin;
use Cake\ORM\TableRegistry;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UploadsControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  UploadsController $UploadsController
 */
class UploadsControllerTest extends BcTestCase
{

    /**
     * set up
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
        session_reset();
        parent::tearDown();
    }

    /**
     * セッションに保存した一時ファイルを出力する
     */
    public function testTmp()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->Content = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $data = [
            'eyecatch' => [
                "tmp_name" => "/tmp/testBcUpload.png",
                "error" => 0,
                "name" => "test.png",
                "type" => "image/png",
                "size" => 100
            ]
        ];
        // ダミーファイルの作成
        touch($data['eyecatch']['tmp_name']);
        $this->Content->saveTmpFiles($data, 1);
        $this->get('/baser/admin/baser-core/uploads/tmp/medium/00000001_eyecatch_png');
        $this->assertResponseOk();
    }
}
