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
        mkdir(TMP . 'uploads');
        touch(TMP . 'uploads/test.gif');
        copy(ROOT . '/plugins/bc-admin-third/webroot/img/baser.power.gif', TMP . 'uploads/test.gif');

        $this->session([
            'Upload.test_gif.data' => base64_encode(file_get_contents(TMP . 'uploads/test.gif')),
            'Upload.test_gif.type' => 'image/gif',
            'Upload.test_gif.imagecopy.medium' => ['width' => 100, 'height' => 100],
        ]);

        $this->get('/baser-core/uploads/tmp/thumb/test.gif');
        $this->assertResponseSuccess();
        $this->assertNotEmpty($this->_controller->getResponse()->getBody());

        @unlink(TMP . 'uploads/test.gif');
        rmdir(TMP . 'uploads');
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
