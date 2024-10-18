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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\JwksController;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * JwksControllerTest Test Case
 */
class JwksControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $controller = new JwksController($this->getRequest());
        $this->assertEquals(['index'], $controller->Authentication->getUnauthenticatedActions());
    }

    /**
     * test index
     */
    public function testIndex()
    {
        $this->get('/baser/api/admin/baser-core/jwks/index.json');
        //ステータスを確認
        $this->assertResponseCode(200);
        //戻り値確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('kid', $vars['keys']['keys'][0]);
        $this->assertArrayHasKey('kty', $vars['keys']['keys'][0]);
        $this->assertArrayHasKey('alg', $vars['keys']['keys'][0]);
        $this->assertArrayHasKey('use', $vars['keys']['keys'][0]);
        $this->assertArrayHasKey('e', $vars['keys']['keys'][0]);
        $this->assertArrayHasKey('n', $vars['keys']['keys'][0]);
    }
}
