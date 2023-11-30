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

use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\ContentFoldersController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * ContentFoldersController
 */
class ContentFoldersControllerTest extends BcTestCase
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
        UserFactory::make()->admin()->persist();
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->ContentFoldersController = new ContentFoldersController($this->getRequest());
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
        $this->assertNotEmpty($this->ContentFoldersController->BcFrontContents);
    }

    /**
     * testDisplay
     *
     * @return void
     */
    public function testView(): void
    {
        $this->get("/en/");
        $this->assertResponseOk();
        I18n::setLocale(Configure::read('App.defaultLocale'));
    }

}
