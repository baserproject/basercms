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

use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PagesScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use Cake\ORM\TableRegistry;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\PagesController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PagesController
 */
class PagesControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {

        parent::setUp();
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PagesScenario::class);
        $this->PagesController = new PagesController($this->getRequest());
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
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->PagesController->BcFrontContents);
    }

    /**
     * testDisplay
     *
     * @return void
     */
    public function testView(): void
    {
        $this->get('/');
        $this->assertResponseOk();
    }

}
