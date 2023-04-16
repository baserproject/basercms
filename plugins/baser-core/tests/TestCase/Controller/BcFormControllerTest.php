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

use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\BcFormController;

/**
 * BaserCore\Controller\BcFormController Test Case
 */
class BcFormControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFormController = new BcFormController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcFormController);
    }

    /**
     * Test get_token
     *
     * @return void
     */
    public function testGet_token()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->get('/baser-core/bc_form/get_token?requestview=false');
        $this->assertResponseSuccess();
        $this->assertNotEmpty($this->_getBodyAsString());
    }

}
