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

use BaserCore\Controller\BcFrontAppController;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;

/**
 * BcFrontAppControllerTest
 * @property BcFrontAppController $BcFrontAppController
 */
class BcFrontAppControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFrontAppController = new BcFrontAppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcFrontAppController);
    }

    /**
     * test beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->BcFrontAppController->setRequest($this->getRequest('https://localhost/index'));
        $result = $this->BcFrontAppController->beforeFilter(new Event('beforeFilter'));
        $this->assertNull($result);
        $this->BcFrontAppController->setRequest($this->getRequest('http://localhost/index'));
        $result = $this->BcFrontAppController->beforeFilter(new Event('beforeFilter'));
        $this->assertNotNull($result);
    }

    /**
     * Test beforeRender
     */
    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
        // TODO ucmitz 本体側の実装要
        /* >>>
        $this->BcFrontAppController->setRequest($this->getRequest('/en/サイトID3の固定ページ'));
        $this->BcFrontAppController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('en', $this->BcFrontAppController->viewBuilder()->getLayoutPath());
        $this->assertEquals('en', $this->BcFrontAppController->viewBuilder()->getTemplatePath());
        <<< */
    }

    /**
     * test redirectIfIsNotSameSite
     */
    public function testRedirectIfIsNotSameSite()
    {
        $this->BcFrontAppController->setRequest($this->getRequest('https://localhost/index'));
        $this->_response = $this->BcFrontAppController->redirectIfIsNotSameSite();
        $this->assertNull($this->_response);
        $this->BcFrontAppController->setRequest($this->getRequest('http://localhost/index'));
        $this->_response = $this->BcFrontAppController->redirectIfIsNotSameSite();
        $this->assertRedirect('https://localhost/index');
        $this->BcFrontAppController->setRequest($this->getRequest('https://localhost/baser/admin'));
        $this->_response = $this->BcFrontAppController->redirectIfIsNotSameSite();
        $this->assertNull($this->_response);
    }

}
