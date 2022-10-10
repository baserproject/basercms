<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller;
use Cake\Http\Session;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\UploadsController;

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
        $this->get('/baser/baser-core/uploads/tmp/medium/00000001_eyecatch_png');
    }
    /**
     * セッションに保存した一時ファイルを出力する
     */
    public function testOutput()
    {
        mkdir(TMP . 'uploads');
        touch(TMP . 'uploads/test.gif');
        copy(ROOT . '/plugins/bc-admin-third/webroot/img/baser.power.gif', TMP . 'uploads/test.gif');
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $session = new Session();
        $session->write('Upload.test_gif.data', base64_encode(file_get_contents(TMP . 'uploads/test.gif')));
        $session->write('Upload.test_gif.type', 'image/gif');
        $session->write('Upload.test_gif.imagecopy.medium', ['width' => 100, 'height' => 100]);
        $UploadsController = new UploadsController($this->getRequest('/baser/baser-core/uploads/', [], 'GET', ['session' => $session]));
        $output = @$this->execPrivateMethod($UploadsController, 'output', [['medium', 'test.gif'], 2]);
        $this->assertNotEmpty($output);
        @unlink(TMP . 'uploads/test.gif');
        rmdir(TMP . 'uploads');
    }
}
